<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Posisi;
use App\Models\Absensi;
use App\Models\JamKerja;
use App\Models\LokasiKerja;
use App\Models\JadwalPengecualian;
use App\Exports\LaporanAbsensiExport;
use App\Exports\LaporanAbsensiHarianExport;
use App\Exports\LaporanAbsensiMingguanExport;
use App\Exports\LaporanLemburExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function dashboard()
    {
        // Total karyawan aktif
        $totalKaryawan = Karyawan::where('status', 'aktif')->count();

        // Hadir hari ini: absensi hari ini dengan jam_masuk tidak null
        $today = Carbon::today()->toDateString();
        $hadirHariIni = Absensi::where('tanggal_absen', $today)->whereNotNull('jam_masuk')->count();

        // Total karyawan lembur hari ini berdasarkan status di database
        $totalKaryawanLembur = Absensi::where('tanggal_absen', $today)->where('status', 'lembur')->distinct('id_karyawan')->count();

        // Performa Absensi Bulan Ini (untuk Pie Chart)
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        $startDate = Carbon::create($tahunIni, $bulanIni, 1)->startOfMonth();
        $endDate = Carbon::create($tahunIni, $bulanIni, 1)->endOfMonth();
        
        // Get all absensis for this month
        $absensisRaw = Absensi::whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()])
            ->with(['karyawan', 'jamKerja'])
            ->get();
        
        // Group by karyawan and tanggal
        $absensis = [];
        foreach ($absensisRaw as $absen) {
            $key = $absen->id_karyawan . '-' . $absen->tanggal_absen;
            if (!isset($absensis[$key])) {
                $absensis[$key] = $absen;
            } else {
                if ($absen->jam_masuk && (!$absensis[$key]->jam_masuk || $absen->jam_masuk < $absensis[$key]->jam_masuk)) {
                    $absensis[$key]->jam_masuk = $absen->jam_masuk;
                }
                if ($absen->jam_keluar && (!$absensis[$key]->jam_keluar || $absen->jam_keluar > $absensis[$key]->jam_keluar)) {
                    $absensis[$key]->jam_keluar = $absen->jam_keluar;
                }
            }
        }
        
        // Calculate statistics
        $tepatWaktu = 0;
        $terlambat = 0;
        $tidakHadir = 0;
        
        // Get working days count (exclude weekends and holidays)
        $workingDaysCount = 0;
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate && $currentDate <= Carbon::today()) {
            $dayOfWeek = $currentDate->dayOfWeek;
            $isWeekend = $dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY;
            $isHoliday = \App\Models\JadwalPengecualian::isHoliday($currentDate->toDateString());
            
            if (!$isWeekend && !$isHoliday) {
                $workingDaysCount++;
            }
            $currentDate->addDay();
        }
        
        $totalExpectedAttendance = $totalKaryawan * $workingDaysCount;
        
        foreach ($absensis as $absen) {
            // Skip weekends and holidays
            $absenDate = Carbon::parse($absen->tanggal_absen);
            $dayOfWeek = $absenDate->dayOfWeek;
            $isWeekend = $dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY;
            $isHoliday = \App\Models\JadwalPengecualian::isHoliday($absen->tanggal_absen);
            
            if ($isWeekend || $isHoliday) {
                continue;
            }
            
            if (!$absen->jam_masuk) {
                $tidakHadir++;
            } else if ($absen->jamKerja) {
                $jamMasukNormal = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_masuk_normal);
                $jamMasukActual = Carbon::createFromFormat('H:i:s', $absen->jam_masuk);
                $toleransi = $absen->jamKerja->toleransi_keterlambatan ?? 0;
                $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);
                
                if ($jamMasukActual->gt($jamMasukNormalWithTolerance)) {
                    $terlambat++;
                } else {
                    $tepatWaktu++;
                }
            } else {
                $tepatWaktu++;
            }
        }
        
        // Calculate tidak hadir for employees who didn't clock in on working days
        $actualAttendance = $tepatWaktu + $terlambat;
        $tidakHadir = $totalExpectedAttendance - $actualAttendance;
        
        // Calculate percentages
        $tepatWaktuPercentage = $totalExpectedAttendance > 0 ? round(($tepatWaktu / $totalExpectedAttendance) * 100, 1) : 0;
        $terlambatPercentage = $totalExpectedAttendance > 0 ? round(($terlambat / $totalExpectedAttendance) * 100, 1) : 0;
        $tidakHadirPercentage = $totalExpectedAttendance > 0 ? round(($tidakHadir / $totalExpectedAttendance) * 100, 1) : 0;
        
        $performaAbsensi = [
            'tepat_waktu' => [
                'count' => $tepatWaktu,
                'percentage' => $tepatWaktuPercentage
            ],
            'terlambat' => [
                'count' => $terlambat,
                'percentage' => $terlambatPercentage
            ],
            'tidak_hadir' => [
                'count' => $tidakHadir,
                'percentage' => $tidakHadirPercentage
            ]
        ];

        return view('dashboard', compact('totalKaryawan', 'hadirHariIni', 'totalKaryawanLembur', 'performaAbsensi'));
    }

    public function indexKaryawan(Request $request)
    {
        $query = Karyawan::with('departemen', 'posisi', 'lokasiKerja');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_karyawan', 'like', '%' . $search . '%')
                  ->orWhere('username_karyawan', 'like', '%' . $search . '%')
                  ->orWhere('email_karyawan', 'like', '%' . $search . '%');
            });
        }

        // Filter by departemen
        if ($request->has('departemen') && $request->departemen) {
            $query->where('id_departemen', $request->departemen);
        }

        $karyawans = $query->paginate(10);
        $departemens = Departemen::all();
        $posisis = Posisi::with('departemen')->get();
        $lokasis = LokasiKerja::all();
        return view('kelola-karyawan', compact('karyawans', 'departemens', 'posisis', 'lokasis'));
    }

    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'username_karyawan' => 'required|string|max:255|unique:karyawan',
            'email_karyawan' => 'required|string|email|max:255|unique:karyawan',
            'password_karyawan' => 'required|string|min:8',
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'id_posisi' => 'nullable',
            'id_lokasi_kerja' => 'nullable|exists:lokasi_kerja,id_lokasi',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->all();
        if (empty($data['id_posisi'])) {
            $data['id_posisi'] = null;
        }
        if (empty($data['id_lokasi_kerja'])) {
            $data['id_lokasi_kerja'] = null;
        }

        Karyawan::create($data);

        return redirect()->route('kelola-karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function updateKaryawan(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'username_karyawan' => 'required|string|max:255|unique:karyawan,username_karyawan,' . $id . ',id_karyawan',
            'email_karyawan' => 'required|string|email|max:255|unique:karyawan,email_karyawan,' . $id . ',id_karyawan',
            'password_karyawan' => 'nullable|string|min:8',
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'id_posisi' => 'nullable',
            'id_lokasi_kerja' => 'nullable|exists:lokasi_kerja,id_lokasi',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->only(['nama_karyawan', 'username_karyawan', 'email_karyawan', 'id_departemen', 'id_posisi', 'id_lokasi_kerja', 'status']);
        if ($request->filled('password_karyawan')) {
            $data['password_karyawan'] = $request->password_karyawan;
        }

        if (empty($data['id_posisi'])) {
            $data['id_posisi'] = null;
        }
        if (empty($data['id_lokasi_kerja'])) {
            $data['id_lokasi_kerja'] = null;
        }

        $karyawan->update($data);

        return redirect()->route('kelola-karyawan')->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroyKaryawan($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('kelola-karyawan')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function indexDepartemen(Request $request)
    {
        $query = Departemen::with('posisis');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nama_departemen', 'like', '%' . $search . '%');
        }

        $departemens = $query->paginate(10);
        return view('kelola-departemen', compact('departemens'));
    }

    public function storeDepartemen(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
        ]);

        // Check for duplicate nama_departemen
        $exists = Departemen::where('nama_departemen', $request->nama_departemen)->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'Nama departemen sudah ada.'])->withInput();
        }

        Departemen::create($request->only(['nama_departemen']));

        return redirect()->route('kelola-departemen')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function updateDepartemen(Request $request, $id)
    {
        $departemen = Departemen::findOrFail($id);

        $request->validate([
            'nama_departemen' => 'required|string|max:255',
        ]);

        // Check for duplicate nama_departemen, excluding current
        $exists = Departemen::where('nama_departemen', $request->nama_departemen)
            ->where('id_departemen', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'Nama departemen sudah ada.'])->withInput();
        }

        $departemen->update($request->only(['nama_departemen']));

        return redirect()->route('kelola-departemen')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroyDepartemen($id)
    {
        $departemen = Departemen::findOrFail($id);
        $departemen->delete();

        return redirect()->route('kelola-departemen')->with('success', 'Departemen berhasil dihapus.');
    }

    public function indexPosisi(Request $request)
    {
        $query = Posisi::with('departemen');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nama_posisi', 'like', '%' . $search . '%');
        }

        // Filter by departemen
        if ($request->has('departemen') && $request->departemen) {
            $query->where('id_departemen', $request->departemen);
        }

        $posisis = $query->paginate(10);
        $departemens = Departemen::all();
        return view('kelola-posisi', compact('posisis', 'departemens'));
    }

    public function storePosisi(Request $request)
    {
        $request->validate([
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'nama_posisi' => 'required|string|max:255',
        ]);

        // Check for duplicate nama_posisi in the same departemen
        $exists = Posisi::where('id_departemen', $request->id_departemen)
            ->where('nama_posisi', $request->nama_posisi)
            ->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'Posisi dengan nama tersebut sudah ada di departemen ini.'])->withInput();
        }

        Posisi::create($request->all());

        return redirect()->route('kelola-posisi')->with('success', 'Posisi berhasil ditambahkan.');
    }

    public function updatePosisi(Request $request, $id)
    {
        $posisi = Posisi::findOrFail($id);

        $request->validate([
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'nama_posisi' => 'required|string|max:255',
        ]);

        // Check for duplicate, excluding current
        $exists = Posisi::where('id_departemen', $request->id_departemen)
            ->where('nama_posisi', $request->nama_posisi)
            ->where('id_posisi', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'Posisi dengan nama tersebut sudah ada di departemen ini.'])->withInput();
        }

        $posisi->update($request->only(['id_departemen', 'nama_posisi']));

        return redirect()->route('kelola-posisi')->with('success', 'Posisi berhasil diperbarui.');
    }

    public function destroyPosisi($id)
    {
        $posisi = Posisi::findOrFail($id);
        $posisi->delete();

        return redirect()->route('kelola-posisi')->with('success', 'Posisi berhasil dihapus.');
    }

    public function getPosisiByDepartemen($id_departemen)
    {
        $posisis = Posisi::where('id_departemen', $id_departemen)->get();
        return response()->json($posisis);
    }

    public function indexAbsensi(Request $request)
    {
        // Get filter type (harian, mingguan, bulanan)
        $type = $request->get('type', 'harian');
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $minggu = $request->get('minggu', Carbon::now()->format('Y-\WW'));
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        // Determine date range based on type
        if ($type == 'harian') {
            $startDate = Carbon::parse($tanggal);
            $endDate = Carbon::parse($tanggal);
        } elseif ($type == 'mingguan') {
            // minggu is in format YYYY-Www
            list($year, $week) = explode('-W', $minggu);
            $startDate = Carbon::createFromDate($year, 1, 1)->setISODate($year, $week)->startOfWeek();
            $endDate = Carbon::createFromDate($year, 1, 1)->setISODate($year, $week)->endOfWeek();
        } else { // bulanan
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        }

        $query = Absensi::with(['karyawan.departemen', 'karyawan.posisi', 'karyawan.lokasiKerja', 'jamKerja'])
            ->select('absensi.*') // Ensure all columns including override fields are selected
            ->whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('tanggal_absen', 'desc')
            ->orderBy('jam_masuk', 'desc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama_karyawan', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status && $request->status != 'Semua Status') {
            // Status akan dihitung berdasarkan logika di bawah
        }

        $allAbsensisRaw = $query->get();
        
        // Group by karyawan and tanggal to merge multiple check-ins
        $groupedAbsensis = [];
        foreach ($allAbsensisRaw as $absen) {
            $key = $absen->id_karyawan . '-' . $absen->tanggal_absen;
            
            if (!isset($groupedAbsensis[$key])) {
                $groupedAbsensis[$key] = $absen;
            } else {
                // If jam_masuk is earlier, update it
                if ($absen->jam_masuk && (!$groupedAbsensis[$key]->jam_masuk || $absen->jam_masuk < $groupedAbsensis[$key]->jam_masuk)) {
                    $groupedAbsensis[$key]->jam_masuk = $absen->jam_masuk;
                    $groupedAbsensis[$key]->lokasi_absen_masuk = $absen->lokasi_absen_masuk;
                    $groupedAbsensis[$key]->foto_masuk = $absen->foto_masuk;
                }
                
                // If jam_keluar is later, update it
                if ($absen->jam_keluar && (!$groupedAbsensis[$key]->jam_keluar || $absen->jam_keluar > $groupedAbsensis[$key]->jam_keluar)) {
                    $groupedAbsensis[$key]->jam_keluar = $absen->jam_keluar;
                    $groupedAbsensis[$key]->lokasi_absen_keluar = $absen->lokasi_absen_keluar;
                    $groupedAbsensis[$key]->foto_keluar = $absen->foto_keluar;
                }
            }
        }
        
        $allAbsensis = collect(array_values($groupedAbsensis));

        // Proses data absensi untuk stats
        $stats = [
            'hadir' => 0,
            'terlambat' => 0,
            'lembur' => 0,
            'tidak_hadir' => 0,
            'libur' => 0,
        ];

        foreach ($allAbsensis as $absen) {
            $status = $this->determineStatus($absen);

            // Hitung statistik
            switch ($status) {
                case 'Hadir':
                    $stats['hadir']++;
                    break;
                case 'Terlambat':
                    $stats['terlambat']++;
                    break;
                case 'Lembur':
                    $stats['lembur']++;
                    break;
                case 'Tidak Hadir':
                    $stats['tidak_hadir']++;
                    break;
                case 'Libur':
                    $stats['libur']++;
                    break;
            }
        }

        // Manual pagination after grouping
        $perPage = 10;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedAbsensis = $allAbsensis->slice($offset, $perPage);
        $total = $allAbsensis->count();
        
        // Proses data absensi untuk display
        $processedAbsensis = [];
        foreach ($paginatedAbsensis as $absen) {
            $status = $this->determineStatus($absen);
            $totalJam = $this->calculateTotalJam($absen);

            // Filter berdasarkan status jika dipilih
            if ($request->has('status') && $request->status && $request->status != 'Semua Status') {
                if ($status != $request->status) {
                    continue;
                }
            }

            // Get location name only from lokasiKerja
            $lokasiMasuk = null;
            $lokasiKeluar = null;
            if ($absen->karyawan && $absen->karyawan->lokasiKerja) {
                $lokasiMasuk = $absen->karyawan->lokasiKerja->lokasi_kerja;
                $lokasiKeluar = $absen->karyawan->lokasiKerja->lokasi_kerja;
            }
            
            $processedAbsensis[] = [
                'id' => $absen->id_absensi,
                'id_karyawan' => $absen->id_karyawan,
                'nama_karyawan' => $absen->karyawan->nama_karyawan ?? 'N/A',
                'departemen' => $absen->karyawan->departemen->nama_departemen ?? 'N/A',
                'posisi' => $absen->karyawan->posisi->nama_posisi ?? 'N/A',
                'tanggal' => $absen->tanggal_absen,
                'jam_masuk' => $absen->jam_masuk,
                'jam_keluar' => $absen->jam_keluar,
                'lokasi_masuk' => $lokasiMasuk,
                'lokasi_keluar' => $lokasiKeluar,
                'total_jam' => $totalJam,
                'status' => $status,
                'absen' => $absen, // untuk detail modal
            ];
        }
        
        // Create manual pagination object
        $absensis = new \Illuminate\Pagination\LengthAwarePaginator(
            $processedAbsensis,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('data-absensi', compact('processedAbsensis', 'stats', 'absensis', 'type', 'tanggal', 'minggu', 'bulan', 'tahun'));
    }

    private function isWithinEarlyLeaveTolerance($absen)
    {
        if (!$absen->jam_keluar || !$absen->jamKerja) {
            return false;
        }

        $jamKeluarNormal = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_keluar_normal);
        $jamKeluarActual = Carbon::createFromFormat('H:i:s', $absen->jam_keluar);
        $toleransiPulangCepat = $absen->jamKerja->toleransi_pulang_cepat ?? 0;

        // Jika toleransi 0, maka tidak ada toleransi
        if ($toleransiPulangCepat <= 0) {
            return false;
        }

        // Hitung batas toleransi: jam keluar normal dikurangi toleransi
        $batasToleransi = $jamKeluarNormal->copy()->subMinutes($toleransiPulangCepat);

        // Jika jam keluar aktual >= batas toleransi, maka dalam toleransi
        return $jamKeluarActual->gte($batasToleransi);
    }

    /**
     * Get base status without override considerations
     * This is used to show the actual attendance condition
     */
    private function getBaseStatus($absen)
    {
        // Check if the date is weekend (Saturday or Sunday) - HARI LIBUR
        $tanggalAbsen = Carbon::parse($absen->tanggal_absen);
        if ($tanggalAbsen->isWeekend()) {
            // If no attendance record on weekend, it's a holiday
            if (!$absen->jam_masuk) {
                return 'Libur';
            }
            // If there's attendance on weekend, it could be lembur
            if ($absen->is_lembur) {
                return 'Lembur';
            }
        }

        // Check if date is a holiday
        $isHoliday = \App\Models\JadwalPengecualian::isHoliday($absen->tanggal_absen);
        if ($isHoliday) {
            if (!$absen->jam_masuk) {
                return 'Libur';
            }
            if ($absen->is_lembur) {
                return 'Lembur';
            }
        }

        // If is_lembur is true, set status to lembur
        if ($absen->is_lembur) {
            return 'Lembur';
        }

        // If status is already set to manual statuses, return it
        if ($absen->status && in_array(strtolower($absen->status), ['izin', 'sakit', 'cuti', 'dinas luar'])) {
            return ucfirst($absen->status);
        }

        if (!$absen->jam_masuk) {
            return 'Tidak Hadir';
        }

        if ($absen->jamKerja) {
            $jamMasukNormal = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_masuk_normal);
            $jamMasukActual = Carbon::createFromFormat('H:i:s', $absen->jam_masuk);

            // Tambahkan toleransi keterlambatan
            $toleransi = $absen->jamKerja->toleransi_keterlambatan ?? 0;
            $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);

            $isLateIn = $jamMasukActual->gt($jamMasukNormalWithTolerance);

            if (!$absen->jam_keluar) {
                // No check-out, status based on check-in only
                return $isLateIn ? 'Terlambat' : 'Hadir';
            }

            // Has check-out
            $jamKeluarNormal = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_keluar_normal);
            $jamKeluarActual = Carbon::createFromFormat('H:i:s', $absen->jam_keluar);
            $toleransiPulangCepat = $absen->jamKerja->toleransi_pulang_cepat ?? 0;
            $jamKeluarNormalWithTolerance = $jamKeluarNormal->copy()->subMinutes($toleransiPulangCepat);

            $isEarlyOut = $jamKeluarActual->lt($jamKeluarNormalWithTolerance);

            if ($isLateIn && $isEarlyOut) {
                return 'Tidak Konsisten';
            } elseif (!$isLateIn && $isEarlyOut) {
                return 'Pulang Cepat';
            } elseif ($isLateIn && !$isEarlyOut) {
                return 'Terlambat';
            } else {
                return 'Hadir';
            }
        }

        // Default jika tidak ada jam kerja
        return 'Hadir';
    }

    private function determineStatus($absen)
    {
        // Check override request status first
        if ($absen->override_request) {
            if ($absen->override_status === 'pending') {
                // If override is pending, show status with pending note
                $baseStatus = $this->getBaseStatus($absen);
                return $baseStatus . ' (Menunggu Approval Override)';
            } elseif ($absen->override_status === 'rejected') {
                // If override is rejected, show status with rejected note
                $baseStatus = $this->getBaseStatus($absen);
                return $baseStatus . ' (Override Ditolak)';
            }
            // If approved, continue with normal status determination
        }

        // Get base status for all other cases
        return $this->getBaseStatus($absen);
    }

    private function calculateTotalJam($absen)
    {
        if (!$absen->jam_masuk || !$absen->jam_keluar) {
            return '0 jam';
        }

        $jamMasuk = Carbon::createFromFormat('H:i:s', $absen->jam_masuk);
        $jamKeluar = Carbon::createFromFormat('H:i:s', $absen->jam_keluar);

        $minutes = $jamMasuk->diffInMinutes($jamKeluar, false);
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes > 0) {
            return $hours . ' jam ' . $remainingMinutes . ' menit';
        } else {
            return $hours . ' jam';
        }
    }

    public function indexPengaturan()
    {
        $jamKerja = JamKerja::first();
        return view('pengaturan', compact('jamKerja'));
    }

    public function updatePengaturan(Request $request)
    {
        $request->validate([
            'jam_masuk_normal' => 'required|regex:/^[0-9]{1,2}:[0-5][0-9](:[0-5][0-9])?$/',
            'jam_keluar_normal' => 'required|regex:/^[0-9]{1,2}:[0-5][0-9](:[0-5][0-9])?$/',
            'toleransi_keterlambatan' => 'required|integer|min:0',
            'toleransi_pulang_cepat' => 'required|integer|min:0',
            'jam_lembur' => 'nullable|regex:/^[0-9]{1,2}:[0-5][0-9](:[0-5][0-9])?$/',
        ]);

        $jamKerja = JamKerja::first();
        if (!$jamKerja) {
            $jamKerja = new JamKerja();
        }

        $jamKerja->fill($request->only(['jam_masuk_normal', 'jam_keluar_normal', 'toleransi_keterlambatan', 'toleransi_pulang_cepat', 'jam_lembur']));
        $jamKerja->save();

        return redirect()->route('pengaturan')->with('success', 'Pengaturan jam kerja berhasil diperbarui.');
    }

    public function indexLokasi(Request $request)
    {
        $query = LokasiKerja::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('lokasi_kerja', 'like', '%' . $search . '%');
        }

        $lokasis = $query->paginate(10);
        return view('kelola-lokasi', compact('lokasis'));
    }

    public function storeLokasi(Request $request)
    {
        $request->validate([
            'lokasi_kerja' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
        ]);

        // Check for duplicate lokasi_kerja
        $exists = LokasiKerja::where('lokasi_kerja', $request->lokasi_kerja)->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'Nama lokasi kerja sudah ada.'])->withInput();
        }

        LokasiKerja::create($request->all());

        return redirect()->route('kelola-lokasi')->with('success', 'Lokasi kerja berhasil ditambahkan.');
    }

    public function updateLokasi(Request $request, $id)
    {
        $lokasi = LokasiKerja::findOrFail($id);

        $request->validate([
            'lokasi_kerja' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
        ]);

        // Check for duplicate lokasi_kerja, excluding current
        $exists = LokasiKerja::where('lokasi_kerja', $request->lokasi_kerja)
            ->where('id_lokasi', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'Nama lokasi kerja sudah ada.'])->withInput();
        }

        $lokasi->update($request->all());

        return redirect()->route('kelola-lokasi')->with('success', 'Lokasi kerja berhasil diperbarui.');
    }

    public function destroyLokasi($id)
    {
        $lokasi = LokasiKerja::findOrFail($id);
        $lokasi->delete();

        return redirect()->route('kelola-lokasi')->with('success', 'Lokasi kerja berhasil dihapus.');
    }

    public function indexLaporan(Request $request)
    {
        // Get filters - DEFAULT HARI INI
        $type = $request->get('type', 'harian');
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $minggu = $request->get('minggu', Carbon::now()->format('Y-\WW'));
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $search = $request->get('search');

        if ($type == 'harian') {
            $startDate = Carbon::parse($tanggal);
            $endDate = Carbon::parse($tanggal);
        } elseif ($type == 'mingguan') {
            // minggu is in format YYYY-Www
            list($year, $week) = explode('-W', $minggu);
            $startDate = Carbon::createFromDate($year, 1, 1)->setISODate($year, $week)->startOfWeek();
            $endDate = Carbon::createFromDate($year, 1, 1)->setISODate($year, $week)->endOfWeek();
        } else { // bulanan
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        }

        $today = Carbon::today();
        if ($endDate->gt($today)) {
            $endDate = $today;
        }

        // Get all active employees
        $karyawanQuery = Karyawan::where('status', 'aktif');
        if ($search) {
            $karyawanQuery->where('nama_karyawan', 'like', '%' . $search . '%');
        }
        $karyawans = $karyawanQuery->get();

        // Get all absensis for the month
        $absensisRaw = Absensi::whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()])
            ->with(['karyawan', 'jamKerja'])
            ->get();

        // Group by karyawan and tanggal, then get earliest jam_masuk and latest jam_keluar
        $absensis = [];
        foreach ($absensisRaw as $absen) {
            $key = $absen->id_karyawan . '-' . $absen->tanggal_absen;
            
            if (!isset($absensis[$key])) {
                $absensis[$key] = $absen;
            } else {
                // If jam_masuk is earlier, update it
                if ($absen->jam_masuk && (!$absensis[$key]->jam_masuk || $absen->jam_masuk < $absensis[$key]->jam_masuk)) {
                    $absensis[$key]->jam_masuk = $absen->jam_masuk;
                    $absensis[$key]->lokasi_absen_masuk = $absen->lokasi_absen_masuk;
                    $absensis[$key]->foto_masuk = $absen->foto_masuk;
                }
                
                // If jam_keluar is later, update it
                if ($absen->jam_keluar && (!$absensis[$key]->jam_keluar || $absen->jam_keluar > $absensis[$key]->jam_keluar)) {
                    $absensis[$key]->jam_keluar = $absen->jam_keluar;
                    $absensis[$key]->lokasi_absen_keluar = $absen->lokasi_absen_keluar;
                    $absensis[$key]->foto_keluar = $absen->foto_keluar;
                }
            }
        }

        // Build report data: one entry per employee per day
        $reportData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->toDateString();
            
            // Skip Sabtu (Saturday) dan Minggu (Sunday) - LIBUR
            $dayOfWeek = $currentDate->dayOfWeek;
            $isSaturday = $dayOfWeek === Carbon::SATURDAY;
            $isSunday = $dayOfWeek === Carbon::SUNDAY;
            
            // Check if it's a holiday from jadwal_pengecualian
            $isHoliday = \App\Models\JadwalPengecualian::isHoliday($dateStr);
            $holidayName = \App\Models\JadwalPengecualian::getHolidayName($dateStr);
            
            foreach ($karyawans as $karyawan) {
                $key = $karyawan->id_karyawan . '-' . $dateStr;
                
                // If weekend or holiday, mark as Libur
                if ($isSaturday || $isSunday || $isHoliday) {
                    $statusLibur = $holidayName ? "Libur - {$holidayName}" : ($isSaturday || $isSunday ? "Libur - Akhir Pekan" : "Libur");
                    $reportData[] = [
                        'nama_karyawan' => $karyawan->nama_karyawan,
                        'tanggal' => $currentDate->format('d-m-Y'),
                        'jam_masuk' => null,
                        'jam_keluar' => null,
                        'total_jam_kerja' => '-',
                        'status' => $statusLibur,
                        'is_absent' => false,
                        'is_holiday' => true,
                    ];
                } elseif (isset($absensis[$key])) {
                    $absen = $absensis[$key];
                    $status = $this->determineStatus($absen);
                    $totalJam = $this->calculateTotalJam($absen);
                    
                    // Get location name only from lokasiKerja
                    $lokasiMasuk = null;
                    $lokasiKeluar = null;
                    if ($absen->karyawan && $absen->karyawan->lokasiKerja) {
                        $lokasiMasuk = $absen->karyawan->lokasiKerja->lokasi_kerja;
                        $lokasiKeluar = $absen->karyawan->lokasiKerja->lokasi_kerja;
                    }
                    
                    $reportData[] = [
                        'nama_karyawan' => $karyawan->nama_karyawan,
                        'tanggal' => $currentDate->format('d-m-Y'),
                        'jam_masuk' => $absen->jam_masuk,
                        'jam_keluar' => $absen->jam_keluar,
                        'lokasi_masuk' => $lokasiMasuk,
                        'lokasi_keluar' => $lokasiKeluar,
                        'total_jam_kerja' => $totalJam,
                        'status' => $status,
                        'is_absent' => false,
                        'is_holiday' => false,
                    ];
                } else {
                    $reportData[] = [
                        'nama_karyawan' => $karyawan->nama_karyawan,
                        'tanggal' => $currentDate->format('d-m-Y'),
                        'jam_masuk' => null,
                        'jam_keluar' => null,
                        'total_jam_kerja' => '0 jam',
                        'status' => 'Tidak Hadir',
                        'is_absent' => true,
                        'is_holiday' => false,
                    ];
                }
            }
            $currentDate->addDay();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'tanggal');
        $sortDirection = $request->get('sort_direction', 'desc');
        $allowedSorts = ['tanggal', 'nama_karyawan', 'jam_masuk', 'jam_keluar', 'total_jam_kerja', 'status'];
        if (in_array($sortBy, $allowedSorts)) {
            usort($reportData, function($a, $b) use ($sortBy, $sortDirection) {
                $valA = $a[$sortBy] ?? '';
                $valB = $b[$sortBy] ?? '';
                
                // Special handling for date sorting (format: dd-mm-yyyy)
                if ($sortBy === 'tanggal') {
                    // Convert dd-mm-yyyy to yyyy-mm-dd for proper comparison
                    $dateA = $valA ? implode('-', array_reverse(explode('-', $valA))) : '';
                    $dateB = $valB ? implode('-', array_reverse(explode('-', $valB))) : '';
                    $valA = $dateA;
                    $valB = $dateB;
                }
                
                if ($sortDirection == 'asc') {
                    return strcmp($valA, $valB);
                } else {
                    return strcmp($valB, $valA);
                }
            });
        }

        // Paginate manually
        $perPage = 20;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedData = array_slice($reportData, $offset, $perPage);
        $total = count($reportData);
        $absensis = new \Illuminate\Pagination\LengthAwarePaginator($paginatedData, $total, $perPage, $page, [
            'path' => $request->url(),
            'pageName' => 'page',
        ]);

        // Process for display
        $processedAbsensis = [];
        foreach ($paginatedData as $index => $data) {
            $karyawan = $karyawans->where('nama_karyawan', $data['nama_karyawan'])->first();
            $processedAbsensis[] = [
                'no' => $offset + $index + 1,
                'nama_karyawan' => $data['nama_karyawan'],
                'tanggal' => $data['tanggal'],
                'jam_masuk' => $data['jam_masuk'],
                'jam_keluar' => $data['jam_keluar'],
                'total_jam_kerja' => $data['total_jam_kerja'],
                'status' => $data['status'],
                'id_karyawan' => $karyawan ? $karyawan->id_karyawan : null,
            ];
        }

        // Calculate statistics
        $totalKaryawan = $karyawans->count();
        $hadirBulanIni = collect($reportData)->where('status', '!=', 'Tidak Hadir')->count();
        $terlambatBulanIni = collect($reportData)->where('status', 'Terlambat')->count();

        // Tingkat Kehadiran Rata-rata
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalPossible = $totalKaryawan * $totalDays;
        $attendanceRate = $totalPossible > 0 ? round(($hadirBulanIni / $totalPossible) * 100, 1) : 0;

        $stats = [
            'hadir_bulan_ini' => $hadirBulanIni,
            'terlambat_bulan_ini' => $terlambatBulanIni,
            'tingkat_kehadiran' => $attendanceRate,
        ];

        // Prepare chart data: attendance per day
        $chartData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->toDateString();
            $hadirCount = collect($reportData)->where('tanggal', $currentDate->format('d-m-Y'))->where('status', '!=', 'Tidak Hadir')->count();
            $chartData[] = [
                'date' => $currentDate->format('d'),
                'hadir' => $hadirCount,
            ];
            $currentDate->addDay();
        }

        // Get overtime data
        $overtimeQuery = Absensi::whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'lembur')
            ->orderBy('tanggal_absen', 'desc')
            ->orderBy('jam_masuk', 'desc')
            ->with(['karyawan.departemen', 'karyawan.posisi', 'jamKerja']);

        if ($search) {
            $overtimeQuery->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama_karyawan', 'like', '%' . $search . '%');
            });
        }

        $overtimeAbsensis = $overtimeQuery->paginate(20, ['*'], 'overtime_page');

        // Process overtime data for display
        $processedOvertime = [];
        foreach ($overtimeAbsensis as $absen) {
            $totalJam = $this->calculateTotalJam($absen);
            $processedOvertime[] = [
                'id_absensi' => $absen->id_absensi,
                'nama_karyawan' => $absen->karyawan->nama_karyawan ?? 'N/A',
                'departemen' => $absen->karyawan->departemen->nama_departemen ?? 'N/A',
                'posisi' => $absen->karyawan->posisi->nama_posisi ?? 'N/A',
                'tanggal_absen' => $absen->tanggal_absen,
                'jam_masuk' => $absen->jam_masuk,
                'jam_keluar' => $absen->jam_keluar,
                'total_jam_kerja' => $totalJam,
                'status' => 'Lembur',
            ];
        }

        return view('laporan', compact('stats', 'processedAbsensis', 'absensis', 'type', 'bulan', 'tahun', 'minggu', 'tanggal', 'search', 'sortBy', 'sortDirection', 'chartData', 'processedOvertime', 'overtimeAbsensis'));
    }

    public function exportLaporan(Request $request)
    {
        $type = $request->get('type', 'bulanan');
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $minggu = $request->get('minggu', Carbon::now()->format('Y-\WW'));
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $search = $request->get('search');

        if ($type == 'harian') {
            return Excel::download(new LaporanAbsensiHarianExport($tanggal, $search), 'laporan_absensi_harian_' . $tanggal . '.xlsx');
        } elseif ($type == 'mingguan') {
            return Excel::download(new LaporanAbsensiMingguanExport($minggu, $search), 'laporan_absensi_mingguan_' . $minggu . '.xlsx');
        } else {
            return Excel::download(new LaporanAbsensiExport($bulan, $tahun, $search), 'laporan_absensi_bulanan_' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx');
        }
    }

    public function exportLaporanLembur(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $search = $request->get('search');

        return Excel::download(new LaporanLemburExport($bulan, $tahun, $search), 'laporan_lembur_' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx');
    }



    /**
     * Update attendance status manually by admin
     */
    public function updateAbsensiStatus(Request $request, $id_absensi)
    {
        $request->validate([
            'status' => 'required|in:izin,sakit,cuti,dinas luar',
        ]);

        $absensi = Absensi::findOrFail($id_absensi);
        $absensi->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status absensi berhasil diperbarui.']);
    }

    /**
     * Update status in report (create or update absensi for specific employee and date)
     */
    public function updateLaporanStatus(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,terlambat,pulang cepat,tidak konsisten,tidak hadir,izin,sakit,cuti,dinas luar,lembur',
        ]);

        $absensi = Absensi::where('id_karyawan', $request->id_karyawan)
            ->where('tanggal_absen', $request->tanggal)
            ->first();

        if ($absensi) {
            $absensi->update(['status' => $request->status]);
        } else {
            Absensi::create([
                'id_karyawan' => $request->id_karyawan,
                'tanggal_absen' => $request->tanggal,
                'status' => $request->status,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Status laporan berhasil diperbarui.']);
    }

    // ===== KELOLA JADWAL PENGECUALIAN =====
    
    public function indexJadwalPengecualian(Request $request)
    {
        $query = JadwalPengecualian::query()->orderBy('tanggal', 'desc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nama_hari_libur', 'like', '%' . $search . '%');
        }

        $jadwalPengecualians = $query->paginate(10);
        return view('kelola-jadwal-pengecualian', compact('jadwalPengecualians'));
    }

    public function storeJadwalPengecualian(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_hari_libur' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'jenis' => 'required|in:libur_nasional,cuti_bersama,lainnya',
        ]);

        // Check for duplicate tanggal
        $exists = JadwalPengecualian::where('tanggal', $request->tanggal)->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'Tanggal ini sudah ada dalam jadwal pengecualian.'])->withInput();
        }

        JadwalPengecualian::create($request->all());

        return redirect()->route('kelola-jadwal-pengecualian')->with('success', 'Jadwal pengecualian berhasil ditambahkan.');
    }

    public function updateJadwalPengecualian(Request $request, $id)
    {
        $jadwal = JadwalPengecualian::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'nama_hari_libur' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'jenis' => 'required|in:libur_nasional,cuti_bersama,lainnya',
        ]);

        // Check for duplicate tanggal, excluding current
        $exists = JadwalPengecualian::where('tanggal', $request->tanggal)
            ->where('id_jadwal_pengecualian', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'Tanggal ini sudah ada dalam jadwal pengecualian.'])->withInput();
        }

        $jadwal->update($request->all());

        return redirect()->route('kelola-jadwal-pengecualian')->with('success', 'Jadwal pengecualian berhasil diperbarui.');
    }

    public function destroyJadwalPengecualian($id)
    {
        $jadwal = JadwalPengecualian::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('kelola-jadwal-pengecualian')->with('success', 'Jadwal pengecualian berhasil dihapus.');
    }

    /**
     * Approve override request from employee
     */
    public function approveOverrideRequest(Request $request, $id)
    {
        $request->validate([
            'response_note' => 'nullable|string|max:500',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
            'status' => 'nullable|string|in:hadir,terlambat,pulang cepat,tidak konsisten,izin,sakit,cuti,dinas luar',
        ]);

        try {
            $absensi = Absensi::findOrFail($id);
            
            if (!$absensi->override_request) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data absensi ini tidak memiliki permintaan override.'
                ], 400);
            }

            if ($absensi->override_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan override ini sudah diproses sebelumnya.'
                ], 400);
            }

            $admin = Auth::user();
            
            // Prepare update data
            $updateData = [
                'override_status' => 'approved',
                'override_responded_at' => now(),
                'override_responded_by' => $admin->id_karyawan,
                'override_response_note' => $request->response_note ?? 'Disetujui oleh Manager SDM',
            ];

            // Update jam_masuk if provided
            if ($request->has('jam_masuk') && $request->jam_masuk) {
                $updateData['jam_masuk'] = $request->jam_masuk;
                Log::info('Updating jam_masuk to: ' . $request->jam_masuk);
            }

            // Update jam_keluar if provided
            if ($request->has('jam_keluar') && $request->jam_keluar) {
                $updateData['jam_keluar'] = $request->jam_keluar;
                Log::info('Updating jam_keluar to: ' . $request->jam_keluar);
            }

            // Update status if provided (don't change status if not explicitly set)
            if ($request->has('status') && $request->status) {
                $updateData['status'] = $request->status;
                Log::info('Updating status to: ' . $request->status);
            }
            // Note: Status will remain as is (terlambat, pulang cepat, etc.) if not changed by admin

            $absensi->update($updateData);

            // Build success message with changes info
            $message = 'Permintaan override berhasil disetujui.';
            $changes = [];
            if ($request->jam_masuk) $changes[] = 'Jam Check-In diubah';
            if ($request->jam_keluar) $changes[] = 'Jam Check-Out diubah';
            if ($request->status) $changes[] = 'Status diubah';
            
            if (!empty($changes)) {
                $message .= ' ' . implode(', ', $changes) . '.';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving override: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui permintaan override.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject override request from employee
     */
    public function rejectOverrideRequest(Request $request, $id)
    {
        $request->validate([
            'response_note' => 'required|string|max:500',
        ]);

        try {
            $absensi = Absensi::findOrFail($id);
            
            if (!$absensi->override_request) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data absensi ini tidak memiliki permintaan override.'
                ], 400);
            }

            if ($absensi->override_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan override ini sudah diproses sebelumnya.'
                ], 400);
            }

            $admin = Auth::user();
            
            $absensi->update([
                'override_status' => 'rejected',
                'override_responded_at' => now(),
                'override_responded_by' => $admin->id_karyawan,
                'override_response_note' => $request->response_note,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan override ditolak.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan override.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display list of override requests
     */
    public function indexOverrideRequests(Request $request)
    {
        $status = $request->get('status', 'pending'); // pending, approved, rejected, all
        
        $query = Absensi::with(['karyawan.departemen', 'karyawan.posisi', 'overrideRespondedBy'])
            ->where('override_request', true)
            ->orderBy('override_requested_at', 'desc');
        
        if ($status !== 'all') {
            $query->where('override_status', $status);
        }
        
        $overrideRequests = $query->paginate(20);
        
        // Count by status
        $counts = [
            'pending' => Absensi::where('override_request', true)->where('override_status', 'pending')->count(),
            'approved' => Absensi::where('override_request', true)->where('override_status', 'approved')->count(),
            'rejected' => Absensi::where('override_request', true)->where('override_status', 'rejected')->count(),
            'all' => Absensi::where('override_request', true)->count(),
        ];
        
        return view('override-requests', compact('overrideRequests', 'status', 'counts'));
    }

}
