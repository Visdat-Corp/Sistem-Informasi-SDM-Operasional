<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use App\Models\LokasiKerja;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $karyawan = Auth::guard('karyawan')->user();

        // Check if already checked in today
        $today = Carbon::today()->toDateString();
        $existingAbsensi = Absensi::where('id_karyawan', $karyawan->id_karyawan)
            ->where('tanggal_absen', $today)
            ->first();

        if ($existingAbsensi && $existingAbsensi->jam_masuk) {
            return response()->json(['error' => 'Anda sudah check-in hari ini.'], 400);
        }

        // Validate location
        $isWithinRadius = false;
        $lokasiKerjas = LokasiKerja::all();
        foreach ($lokasiKerjas as $lokasi) {
            if ($lokasi->isWithinRadius($request->latitude, $request->longitude)) {
                $isWithinRadius = true;
                break;
            }
        }

        if (!$isWithinRadius) {
            return response()->json(['error' => 'Lokasi Anda di luar area yang ditentukan.'], 403);
        }

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos', 'public');
        }

        $data = [
            'id_karyawan' => $karyawan->id_karyawan,
            'tanggal_absen' => $today,
            'jam_masuk' => Carbon::now()->toTimeString(),
            'lokasi_absen_masuk' => $request->latitude . ',' . $request->longitude,
            'foto_masuk' => $fotoPath,
            'mode' => 'WFO', // Assuming WFO for now
        ];

        if ($existingAbsensi) {
            $existingAbsensi->update($data);
        } else {
            Absensi::create($data);
        }

        return response()->json(['message' => 'Check-in berhasil.']);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $karyawan = Auth::guard('karyawan')->user();

        $today = Carbon::today()->toDateString();
        $absensi = Absensi::where('id_karyawan', $karyawan->id_karyawan)
            ->where('tanggal_absen', $today)
            ->whereNotNull('jam_masuk')
            ->first();

        if (!$absensi) {
            return response()->json(['error' => 'Anda belum check-in hari ini.'], 400);
        }

        if ($absensi->jam_keluar) {
            return response()->json(['error' => 'Anda sudah check-out hari ini.'], 400);
        }

        // Validate location for check-out as well
        $isWithinRadius = false;
        $lokasiKerjas = LokasiKerja::all();
        foreach ($lokasiKerjas as $lokasi) {
            if ($lokasi->isWithinRadius($request->latitude, $request->longitude)) {
                $isWithinRadius = true;
                break;
            }
        }

        if (!$isWithinRadius) {
            return response()->json(['error' => 'Lokasi Anda di luar area yang ditentukan.'], 403);
        }

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos', 'public');
        }

        $absensi->update([
            'jam_keluar' => Carbon::now()->toTimeString(),
            'lokasi_absen_keluar' => $request->latitude . ',' . $request->longitude,
            'foto_keluar' => $fotoPath,
        ]);

        return response()->json(['message' => 'Check-out berhasil.']);
    }
}
