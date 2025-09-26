<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Posisi;
use App\Models\Absensi;
use App\Models\JamKerja;
use App\Models\LokasiKerja;
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

        // Total lembur hari ini
        $absensis = Absensi::with('jamKerja')->where('tanggal_absen', $today)->whereNotNull('jam_masuk')->whereNotNull('jam_keluar')->get();
        $totalLembur = 0;

        foreach ($absensis as $absen) {
            if ($absen->jamKerja) {
                $jamMasuk = Carbon::createFromFormat('H:i:s', $absen->jam_masuk);
                $jamKeluar = Carbon::createFromFormat('H:i:s', $absen->jam_keluar);
                $normalMasuk = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_masuk_normal);
                $normalKeluar = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_keluar_normal);

                $workedMinutes = $jamMasuk->diffInMinutes($jamKeluar, false);
                $normalMinutes = $normalMasuk->diffInMinutes($normalKeluar, false);

                if ($workedMinutes > $normalMinutes) {
                    $overtimeMinutes = $workedMinutes - $normalMinutes;
                    $totalLembur += $overtimeMinutes / 60.0; // jam
                }
            }
        }

        return view('dashboard', compact('totalKaryawan', 'hadirHariIni', 'totalLembur'));
    }

    public function indexKaryawan()
    {
        $karyawans = Karyawan::with('departemen', 'posisi')->get();
        $departemens = Departemen::all();
        $posisis = Posisi::with('departemen')->get();
        return view('kelola-karyawan', compact('karyawans', 'departemens', 'posisis'));
    }

    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'username_karyawan' => 'required|string|max:255|unique:karyawan',
            'email_karyawan' => 'required|string|email|max:255|unique:karyawan',
            'password_karyawan' => 'required|string|min:8',
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'id_posisi' => 'nullable|exists:posisi,id_posisi',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Karyawan::create($request->all());

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
            'id_posisi' => 'nullable|exists:posisi,id_posisi',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->only(['nama_karyawan', 'username_karyawan', 'email_karyawan', 'id_departemen', 'id_posisi', 'status']);
        if ($request->filled('password_karyawan')) {
            $data['password_karyawan'] = $request->password_karyawan;
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

    public function indexDepartemen()
    {
        $departemens = Departemen::with('posisis')->get();
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

    public function indexPosisi()
    {
        $posisis = Posisi::with('departemen')->get();
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
        $query = Absensi::with(['karyawan.departemen', 'karyawan.posisi', 'jamKerja']);

        // Filter berdasarkan tanggal
        if ($request->has('tanggal') && $request->tanggal) {
            $query->where('tanggal_absen', $request->tanggal);
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status && $request->status != 'Semua Status') {
            // Status akan dihitung berdasarkan logika di bawah
        }

        $absensis = $query->get();

        // Proses data absensi
        $processedAbsensis = [];
        $stats = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'tidak_hadir' => 0,
        ];

        foreach ($absensis as $absen) {
            $status = $this->determineStatus($absen);
            $totalJam = $this->calculateTotalJam($absen);

            // Filter berdasarkan status jika dipilih
            if ($request->has('status') && $request->status && $request->status != 'Semua Status') {
                if ($status != $request->status) {
                    continue;
                }
            }

            $processedAbsensis[] = [
                'id' => $absen->id_absensi,
                'nama_karyawan' => $absen->karyawan->nama_karyawan ?? 'N/A',
                'posisi' => $absen->karyawan->posisi->nama_posisi ?? 'N/A',
                'tanggal' => $absen->tanggal_absen,
                'jam_masuk' => $absen->jam_masuk,
                'jam_keluar' => $absen->jam_keluar,
                'total_jam' => $totalJam,
                'status' => $status,
                'absen' => $absen, // untuk detail modal
            ];

            // Hitung statistik
            switch ($status) {
                case 'Hadir':
                    $stats['hadir']++;
                    break;
                case 'Terlambat':
                    $stats['terlambat']++;
                    break;
                case 'Izin':
                    $stats['izin']++;
                    break;
                case 'Tidak Hadir':
                    $stats['tidak_hadir']++;
                    break;
            }
        }

        return view('data-absensi', compact('processedAbsensis', 'stats'));
    }

    private function determineStatus($absen)
    {
        if ($absen->keterangan == 'izin') {
            return 'Izin';
        }

        if (!$absen->jam_masuk) {
            return 'Tidak Hadir';
        }

        if ($absen->jamKerja) {
            $jamMasukNormal = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_masuk_normal);
            $jamMasukActual = Carbon::createFromFormat('H:i:s', $absen->jam_masuk);

            if ($jamMasukActual->lte($jamMasukNormal)) {
                return 'Hadir';
            } else {
                return 'Terlambat';
            }
        }

        // Default jika tidak ada jam kerja
        return 'Hadir';
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
            'jam_masuk_normal' => 'required|date_format:H:i',
            'jam_keluar_normal' => 'required|date_format:H:i',
            'toleransi_keterlambatan' => 'required|integer|min:0',
        ]);

        $jamKerja = JamKerja::first();
        if (!$jamKerja) {
            $jamKerja = new JamKerja();
        }

        $jamKerja->update($request->only(['jam_masuk_normal', 'jam_keluar_normal', 'toleransi_keterlambatan']));

        return redirect()->route('pengaturan')->with('success', 'Pengaturan jam kerja berhasil diperbarui.');
    }

    public function indexLokasi()
    {
        $lokasis = LokasiKerja::all();
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
}
