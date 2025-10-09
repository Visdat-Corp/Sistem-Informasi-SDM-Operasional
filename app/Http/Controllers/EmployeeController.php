<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;
use App\Models\LokasiKerja;
use App\Models\Karyawan;
use App\Models\JamKerja;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    /**
     * Return a standardized JSON response
     */
    protected function jsonResponse($data = null, $message = 'Success', $success = true, $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a standardized error response
     */
    protected function errorResponse($message = 'Error occurred', $errors = null, $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Legacy login method for backward compatibility
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email_karyawan' : 'username_karyawan';

        $credentials = [
            $loginField => $request->username,
            'password' => $request->password,
        ];

        if (Auth::guard('karyawan')->attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(['message' => 'Login berhasil']);
        }

        return response()->json(['error' => 'Username/Email atau password salah'], 401);
    }

    /**
     * API login method with enhanced response
     */
    public function apiLogin(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email_karyawan' : 'username_karyawan';

            $credentials = [
                $loginField => $request->username,
                'password' => $request->password,
            ];

            // For stateless API, manually authenticate without session
            $karyawan = Karyawan::where($loginField, $request->username)->first();

            if ($karyawan && Hash::check($request->password, $karyawan->password_karyawan)) {
                $karyawan->load(['departemen', 'posisi']);

                // Revoke all previous tokens to enforce single device login
                $karyawan->tokens()->delete();

                // Generate API token for mobile authentication
                $token = $karyawan->createToken('mobile-app')->plainTextToken;

                $userData = [
                    'user' => [
                        'id' => $karyawan->id_karyawan,
                        'id_karyawan' => $karyawan->id_karyawan, // Add this for mobile app compatibility
                        'name' => $karyawan->nama_karyawan,
                        'email' => $karyawan->email_karyawan,
                        'username' => $karyawan->username_karyawan,
                        'department' => $karyawan->departemen ? $karyawan->departemen->nama_departemen : null,
                        'position' => $karyawan->posisi ? $karyawan->posisi->nama_posisi : null,
                        'status' => $karyawan->status,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ];

                Log::info('Karyawan login success', [
                    'id' => $karyawan->id_karyawan,
                ]);
                return $this->jsonResponse($userData, 'Login successful');
            }

            return $this->errorResponse('Invalid username/email or password', null, 401);
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed', $e->getMessage(), 500);
        }
    }

    /**
     * Get employee profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        try {
            $karyawan = $request->user();
            $karyawan->load(['departemen', 'posisi']);
            
            $profileData = [
                'id' => $karyawan->id_karyawan,
                'name' => $karyawan->nama_karyawan,
                'email' => $karyawan->email_karyawan,
                'username' => $karyawan->username_karyawan,
                'department' => $karyawan->departemen ? [
                    'id' => $karyawan->departemen->id_departemen,
                    'name' => $karyawan->departemen->nama_departemen
                ] : null,
                'position' => $karyawan->posisi ? [
                    'id' => $karyawan->posisi->id_posisi,
                    'name' => $karyawan->posisi->nama_posisi
                ] : null,
                'status' => $karyawan->status,
            ];

            return $this->jsonResponse($profileData, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve profile', $e->getMessage(), 500);
        }
    }

    /**
     * Update employee profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255',
            'current_password' => 'required_with:new_password',
            'new_password' => 'sometimes|required|string|min:6|confirmed',
        ]);

        try {
            $karyawan = $request->user();

            // Verify current password if changing password
            if ($request->has('new_password')) {
                if (!Hash::check($request->current_password, $karyawan->password_karyawan)) {
                    return $this->errorResponse('Current password is incorrect', null, 400);
                }
                $karyawan->password_karyawan = $request->new_password;
            }

            // Update other fields
            if ($request->has('name')) {
                $karyawan->nama_karyawan = $request->name;
            }
            if ($request->has('username')) {
                $karyawan->username_karyawan = $request->username;
            }

            $karyawan->save();

            return $this->jsonResponse(null, 'Profile updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update profile', $e->getMessage(), 500);
        }
    }

    /**
     * Legacy check-in method for backward compatibility
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'overtime' => 'sometimes|boolean',
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
        ];

        if ($existingAbsensi) {
            $existingAbsensi->update($data);
        } else {
            Absensi::create($data);
        }

        return response()->json(['message' => 'Check-in berhasil.']);
    }

    /**
     * API check-in method with enhanced response
     */
    public function apiCheckIn(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'overtime' => 'sometimes|boolean',
        ]);

        try {
            $karyawan = Auth::guard('sanctum')->user();
            if (!$karyawan) {
                return $this->errorResponse('Unauthenticated', null, 401);
            }
            Log::info('User authenticated for check-in: ' . $karyawan->id_karyawan);

            // Determine the date for attendance
            $today = $request->has('tanggal') ? $request->tanggal : Carbon::today()->toDateString();

            // Check if already checked in on the specified date
            $existingAbsensi = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal_absen', $today)
                ->first();

            if ($existingAbsensi && $existingAbsensi->jam_masuk) {
                return $this->errorResponse('Kamu sudah check-in hari ini', null, 400);
            }

            // Validate location (skip if no locations configured)
            $isWithinRadius = false;
            $validLocation = null;
            $lokasiCount = LokasiKerja::count();
            if ($lokasiCount === 0) {
                $isWithinRadius = true;
                Log::info('Skipping location validation (no LokasiKerja configured) for user: ' . $karyawan->id_karyawan);
            } else {
                $lokasiKerjas = LokasiKerja::all();
                foreach ($lokasiKerjas as $lokasi) {
                    if ($lokasi->isWithinRadius($request->latitude, $request->longitude)) {
                        $isWithinRadius = true;
                        $validLocation = $lokasi->lokasi_kerja;
                        break;
                    }
                }

                if (!$isWithinRadius) {
                    Log::info('Location validation failed for user: ' . $karyawan->id_karyawan . ' at ' . $request->latitude . ',' . $request->longitude);
                    return $this->errorResponse('Lokasi kamu berada di luar area kerja', null, 422);
                }
            }

            Log::info('Location validation passed for user: ' . $karyawan->id_karyawan);

            // Handle photo upload
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('attendance_photos', 'public');
            }

            $currentTime = Carbon::now();
            $jamKerja = JamKerja::first();
            $data = [
                'id_karyawan' => $karyawan->id_karyawan,
                'tanggal_absen' => $today,
                'jam_masuk' => $currentTime->toTimeString(),
                'lokasi_absen_masuk' => $request->latitude . ',' . $request->longitude,
                'foto_masuk' => $fotoPath,
                'id_jamKerja' => $jamKerja ? $jamKerja->id_jamKerja : null,
                'is_lembur' => $request->overtime ?? false,
            ];

            Log::info('apiCheckIn payload', ['data' => $data]);

            if ($existingAbsensi) {
                $existingAbsensi->update($data);
                $absensi = $existingAbsensi;
                Log::info('Absensi updated: ' . $absensi->id_absensi);
            } else {
                $absensi = Absensi::create($data);
                Log::info('Absensi created: ' . $absensi->id_absensi);
            }

            // Determine and set status
            $status = $this->determineStatus($absensi);
            $absensi->update(['status' => strtolower($status)]);

            $responseData = [
                'attendance_id' => $absensi->id_absensi,
                'check_in_time' => $currentTime->format('H:i:s'),
                'date' => $today,
                'location' => $validLocation,
                'coordinates' => $request->latitude . ',' . $request->longitude,
                'photo_url' => $fotoPath ? Storage::url($fotoPath) : null,
            ];

            return $this->jsonResponse($responseData, 'Check-in successful');
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\QueryException) {
                $qe = $e;
                Log::error('Check-in DB error', [
                    'sql' => $qe->getSql(),
                    'bindings' => $qe->getBindings(),
                    'message' => $qe->getMessage(),
                ]);
                return $this->errorResponse('Check-in database error', [
                    'sql' => $qe->getSql(),
                    'bindings' => $qe->getBindings(),
                    'message' => $qe->getMessage(),
                ], 500);
            }
            Log::error('Check-in failed', [
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
            return $this->errorResponse('Check-in gagal', $e->getMessage(), 500);
        }
    }

    /**
     * Legacy check-out method for backward compatibility
     */
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

    /**
     * API check-out method with enhanced response
     */
    public function apiCheckOut(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $karyawan = $request->user();
            if (!$karyawan) {
                return $this->errorResponse('Unauthenticated', null, 401);
            }

            $today = Carbon::today()->toDateString();
            $absensi = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal_absen', $today)
                ->whereNotNull('jam_masuk')
                ->first();

            if (!$absensi) {
                return $this->errorResponse('Kamu sudah check in hari ini', null, 400);
            }

            if ($absensi->jam_keluar) {
                return $this->errorResponse('Kamu sudah check out hari ini', null, 400);
            }

            // Validate location for check-out as well (skip if no locations configured)
            $isWithinRadius = false;
            $validLocation = null;
            $lokasiCount = LokasiKerja::count();
            if ($lokasiCount === 0) {
                $isWithinRadius = true;
                Log::info('Skipping location validation (no LokasiKerja configured) for user: ' . $karyawan->id_karyawan);
            } else {
                $lokasiKerjas = LokasiKerja::all();
                
                foreach ($lokasiKerjas as $lokasi) {
                    if ($lokasi->isWithinRadius($request->latitude, $request->longitude)) {
                        $isWithinRadius = true;
                        $validLocation = $lokasi->lokasi_kerja;
                        break;
                    }
                }

                if (!$isWithinRadius) {
                    return $this->errorResponse('Lokasi kamu berada di luar area kerja', null, 422);
                }
            }

            // Handle photo upload
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('attendance_photos', 'public');
            }

            $currentTime = Carbon::now();
            Log::info('apiCheckOut payload', ['update' => [
                'jam_keluar' => $currentTime->toTimeString(),
                'lokasi_absen_keluar' => $request->latitude . ',' . $request->longitude,
                'foto_keluar' => $fotoPath,
            ]]);
            
            $absensi->update([
                'jam_keluar' => $currentTime->toTimeString(),
                'lokasi_absen_keluar' => $request->latitude . ',' . $request->longitude,
                'foto_keluar' => $fotoPath,
            ]);

            // Determine and update status after check-out
            $status = $this->determineStatus($absensi);
            $absensi->update(['status' => strtolower($status)]);

            // Calculate work duration
            $checkInTime = Carbon::parse($absensi->tanggal_absen . ' ' . $absensi->jam_masuk);
            $workDuration = $checkInTime->diffInMinutes($currentTime);
            $workHours = floor($workDuration / 60);
            $workMinutes = $workDuration % 60;

            $responseData = [
                'attendance_id' => $absensi->id_absensi,
                'check_out_time' => $currentTime->format('H:i:s'),
                'date' => $today,
                'location' => $validLocation,
                'coordinates' => $request->latitude . ',' . $request->longitude,
                'work_duration' => [
                    'hours' => $workHours,
                    'minutes' => $workMinutes,
                    'total_minutes' => $workDuration
                ],
                'photo_url' => $fotoPath ? Storage::url($fotoPath) : null,
            ];

            return $this->jsonResponse($responseData, 'Check-out successful');
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\QueryException) {
                $qe = $e;
                Log::error('Check-out DB error', [
                    'sql' => $qe->getSql(),
                    'bindings' => $qe->getBindings(),
                    'message' => $qe->getMessage(),
                ]);
                return $this->errorResponse('Check-out database error', [
                    'sql' => $qe->getSql(),
                    'bindings' => $qe->getBindings(),
                    'message' => $qe->getMessage(),
                ], 500);
            }
            Log::error('Check-out failed', [
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
            return $this->errorResponse('Check-out failed', $e->getMessage(), 500);
        }
    }

    /**
     * API overtime method - updates existing check-in to set overtime
     */
    public function apiOvertime(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'sometimes|string|max:500',
        ]);

        try {
            $karyawan = Auth::guard('sanctum')->user();
            if (!$karyawan) {
                return $this->errorResponse('Unauthenticated', null, 401);
            }

            $today = Carbon::today()->toDateString();
            $absensi = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal_absen', $today)
                ->whereNotNull('jam_masuk')
                ->first();

            if (!$absensi) {
                return $this->errorResponse('Kamu belum check in hari ini', null, 400);
            }

            if ($absensi->is_lembur) {
                return $this->errorResponse('Kamu sudah memilih lembur hari ini', null, 400);
            }

            // Validate location (skip if no locations configured)
            $isWithinRadius = false;
            $validLocation = null;
            $lokasiCount = LokasiKerja::count();
            if ($lokasiCount === 0) {
                $isWithinRadius = true;
                Log::info('Skipping location validation (no LokasiKerja configured) for user: ' . $karyawan->id_karyawan);
            } else {
                $lokasiKerjas = LokasiKerja::all();

                foreach ($lokasiKerjas as $lokasi) {
                    if ($lokasi->isWithinRadius($request->latitude, $request->longitude)) {
                        $isWithinRadius = true;
                        $validLocation = $lokasi->lokasi_kerja;
                        break;
                    }
                }

                if (!$isWithinRadius) {
                    return $this->errorResponse('Lokasi kamu berada di luar area kerja', null, 422);
                }
            }

            // Handle photo upload (optional, for overtime photo)
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('attendance_photos', 'public');
            }

            $currentTime = Carbon::now();

            // Update existing attendance record to set overtime
            $updateData = [
                'is_lembur' => true,
                'keterangan' => $request->keterangan ?? 'Overtime work',
            ];

            // If photo provided, update foto_keluar or add to keterangan
            if ($fotoPath) {
                $updateData['foto_keluar'] = $fotoPath;
                $updateData['lokasi_absen_keluar'] = $request->latitude . ',' . $request->longitude;
            }

            Log::info('apiOvertime update payload', ['updateData' => $updateData]);

            $absensi->update($updateData);

            // Update status
            $status = $this->determineStatus($absensi);
            $absensi->update(['status' => strtolower($status)]);

            $responseData = [
                'attendance_id' => $absensi->id_absensi,
                'overtime_set_time' => $currentTime->format('H:i:s'),
                'date' => $today,
                'location' => $validLocation,
                'coordinates' => $request->latitude . ',' . $request->longitude,
                'notes' => $updateData['keterangan'],
                'photo_url' => $fotoPath ? Storage::url($fotoPath) : null,
            ];

            return $this->jsonResponse($responseData, 'Overtime set successfully');
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\QueryException) {
                $qe = $e;
                Log::error('Overtime DB error', [
                    'sql' => $qe->getSql(),
                    'bindings' => $qe->getBindings(),
                    'message' => $qe->getMessage(),
                ]);
                return $this->errorResponse('Overtime database error', [
                    'sql' => $qe->getSql(),
                    'bindings' => $qe->getBindings(),
                    'message' => $qe->getMessage(),
                ], 500);
            }
            Log::error('Overtime set failed', [
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
            return $this->errorResponse('Overtime set failed', $e->getMessage(), 500);
        }
    }

    /**
     * Get attendance history
     */
    public function getAttendanceHistory(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        try {
            $karyawan = $request->user();
            $limit = $request->limit ?? 20;
            
            $query = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->orderBy('tanggal_absen', 'desc')
                ->orderBy('jam_masuk', 'desc');

            if ($request->start_date) {
                $query->where('tanggal_absen', '>=', $request->start_date);
            }
            
            if ($request->end_date) {
                $query->where('tanggal_absen', '<=', $request->end_date);
            }

            $attendances = $query->paginate($limit);

            $formattedData = $attendances->map(function ($attendance) {
                $workDuration = null;
                $terlambat = '00:00:00';
                $cepatPulang = '00:00:00';

                if ($attendance->jam_masuk && $attendance->jam_keluar) {
                    $checkIn = Carbon::parse($attendance->tanggal_absen . ' ' . $attendance->jam_masuk);
                    $checkOut = Carbon::parse($attendance->tanggal_absen . ' ' . $attendance->jam_keluar);
                    $duration = $checkIn->diffInMinutes($checkOut);
                    $workDuration = [
                        'hours' => floor($duration / 60),
                        'minutes' => $duration % 60,
                        'total_minutes' => $duration
                    ];
                }

                // Calculate terlambat and cepat pulang
                if ($attendance->jamKerja && $attendance->jam_masuk) {
                    $jamMasukNormal = Carbon::createFromFormat('H:i:s', $attendance->jamKerja->jam_masuk_normal);
                    $jamMasukActual = Carbon::createFromFormat('H:i:s', $attendance->jam_masuk);
                    $toleransi = $attendance->jamKerja->toleransi_keterlambatan ?? 0;
                    $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);

                    if ($jamMasukActual->gt($jamMasukNormalWithTolerance)) {
                        $menitTerlambat = $jamMasukNormal->diffInMinutes($jamMasukActual, true) - $toleransi;
                        $jamTerlambat = floor($menitTerlambat / 60);
                        $menitTerlambat %= 60;
                        $terlambat = sprintf('%02d:%02d:%02d', $jamTerlambat, $menitTerlambat, 0);
                    }
                }

                if ($attendance->jamKerja && $attendance->jam_keluar) {
                    $jamKeluarNormal = Carbon::createFromFormat('H:i:s', $attendance->jamKerja->jam_keluar_normal);
                    $jamKeluarActual = Carbon::createFromFormat('H:i:s', $attendance->jam_keluar);
                    $toleransiPulangCepat = $attendance->jamKerja->toleransi_pulang_cepat ?? 0;
                    $jamKeluarNormalWithTolerance = $jamKeluarNormal->copy()->subMinutes($toleransiPulangCepat);

                    if ($jamKeluarActual->lt($jamKeluarNormalWithTolerance)) {
                        $menitCepat = $jamKeluarNormalWithTolerance->diffInMinutes($jamKeluarActual, true);
                        $jamCepat = floor($menitCepat / 60);
                        $menitCepat %= 60;
                        $cepatPulang = sprintf('%02d:%02d:%02d', $jamCepat, $menitCepat, 0);
                    }
                }

                return [
                    'id' => $attendance->id_absensi,
                    'date' => $attendance->tanggal_absen,
                    'check_in_time' => $attendance->jam_masuk,
                    'check_out_time' => $attendance->jam_keluar,
                    'check_in_location' => $attendance->lokasi_absen_masuk,
                    'check_out_location' => $attendance->lokasi_absen_keluar,
                    'check_in_photo' => $attendance->foto_masuk ? Storage::url($attendance->foto_masuk) : null,
                    'check_out_photo' => $attendance->foto_keluar ? Storage::url($attendance->foto_keluar) : null,
                    'notes' => $attendance->keterangan,
                    'work_duration' => $workDuration,
                    'is_lembur' => $attendance->is_lembur,
                    'status' => $attendance->status,
                    'terlambat' => $terlambat,
                    'cepat_pulang' => $cepatPulang,
                ];
            });

            $responseData = [
                'attendances' => $formattedData,
                'pagination' => [
                    'current_page' => $attendances->currentPage(),
                    'last_page' => $attendances->lastPage(),
                    'per_page' => $attendances->perPage(),
                    'total' => $attendances->total(),
                ]
            ];

            return $this->jsonResponse($responseData, 'Attendance history retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve attendance history', $e->getMessage(), 500);
        }
    }

    /**
     * Get today's attendance
     */
    public function getTodayAttendance(Request $request): JsonResponse
    {
        try {
            $karyawan = $request->user();
            $today = Carbon::today()->toDateString();
            
            $attendance = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal_absen', $today)
                ->first();

            if (!$attendance) {
                return $this->jsonResponse([
                    'has_attendance' => false,
                    'can_check_in' => true,
                    'can_check_out' => false,
                ], 'No attendance record for today');
            }

            $workDuration = null;
            if ($attendance->jam_masuk && $attendance->jam_keluar) {
                $checkIn = Carbon::parse($attendance->tanggal_absen . ' ' . $attendance->jam_masuk);
                $checkOut = Carbon::parse($attendance->tanggal_absen . ' ' . $attendance->jam_keluar);
                $duration = $checkIn->diffInMinutes($checkOut);
                $workDuration = [
                    'hours' => floor($duration / 60),
                    'minutes' => $duration % 60,
                    'total_minutes' => $duration
                ];
            }

            $responseData = [
                'has_attendance' => true,
                'can_check_in' => !$attendance->jam_masuk,
                'can_check_out' => $attendance->jam_masuk && !$attendance->jam_keluar && !$attendance->is_lembur,
                'attendance' => [
                    'id' => $attendance->id_absensi,
                    'date' => $attendance->tanggal_absen,
                    'check_in_time' => $attendance->jam_masuk,
                    'check_out_time' => $attendance->jam_keluar,
                    'check_in_location' => $attendance->lokasi_absen_masuk,
                    'check_out_location' => $attendance->lokasi_absen_keluar,
                    'check_in_photo' => $attendance->foto_masuk ? Storage::url($attendance->foto_masuk) : null,
                    'check_out_photo' => $attendance->foto_keluar ? Storage::url($attendance->foto_keluar) : null,
                    'notes' => $attendance->keterangan,
                    'work_duration' => $workDuration,
                ]
            ];

            return $this->jsonResponse($responseData, 'Today\'s attendance retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve today\'s attendance', $e->getMessage(), 500);
        }
    }

    /**
     * Get attendance summary statistics
     */
    public function getAttendanceSummary(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'sometimes|integer|min:1|max:12',
            'year' => 'sometimes|integer|min:2020|max:2030',
        ]);

        try {
            $karyawan = $request->user();
            $month = $request->month ?? Carbon::now()->month;
            $year = $request->year ?? Carbon::now()->year;

            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $attendances = Absensi::with('jamKerja')
                ->where('id_karyawan', $karyawan->id_karyawan)
                ->whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();

            $totalDays = $attendances->count();
            $presentDays = $attendances->whereNotNull('jam_masuk')->count();
            $completeDays = $attendances->whereNotNull('jam_masuk')->whereNotNull('jam_keluar')->count();
            $lateDays = 0;

            // Calculate late days based on jam kerja settings with tolerance
            foreach ($attendances as $attendance) {
                if ($attendance->jam_masuk) {
                    $checkInTime = Carbon::parse($attendance->tanggal_absen . ' ' . $attendance->jam_masuk);
                    $standardTimeString = $attendance->jamKerja ? $attendance->jamKerja->jam_masuk_normal : '08:00:00';
                    $standardTime = Carbon::parse($attendance->tanggal_absen . ' ' . $standardTimeString);

                    // Add tolerance if available
                    $toleransi = $attendance->jamKerja ? $attendance->jamKerja->toleransi_keterlambatan : 0;
                    $standardTime->addMinutes($toleransi);

                    if ($checkInTime->gt($standardTime)) {
                        $lateDays++;
                    }
                }
            }

            // Calculate total work hours
            $totalWorkMinutes = 0;
            foreach ($attendances as $attendance) {
                if ($attendance->jam_masuk && $attendance->jam_keluar) {
                    $checkIn = Carbon::parse($attendance->tanggal_absen . ' ' . $attendance->jam_masuk);
                    $checkOut = Carbon::parse($attendance->tanggal_absen . ' ' . $attendance->jam_keluar);
                    $totalWorkMinutes += $checkIn->diffInMinutes($checkOut);
                }
            }

            $totalWorkHours = floor($totalWorkMinutes / 60);
            $averageWorkHours = $completeDays > 0 ? round($totalWorkHours / $completeDays, 2) : 0;

            $responseData = [
                'period' => [
                    'month' => $month,
                    'year' => $year,
                    'month_name' => $startDate->format('F'),
                ],
                'statistics' => [
                    'total_attendance_days' => $totalDays,
                    'present_days' => $presentDays,
                    'complete_days' => $completeDays,
                    'late_days' => $lateDays,
                    'total_work_hours' => $totalWorkHours,
                    'average_work_hours_per_day' => $averageWorkHours,
                ],
                'attendance_rate' => [
                    'working_days_in_month' => $endDate->diffInDaysFiltered(function (Carbon $date) {
                        return $date->isWeekday();
                    }, $startDate),
                    'attendance_percentage' => $endDate->diffInDaysFiltered(function (Carbon $date) {
                        return $date->isWeekday();
                    }, $startDate) > 0 ? round(($presentDays / $endDate->diffInDaysFiltered(function (Carbon $date) {
                        return $date->isWeekday();
                    }, $startDate)) * 100, 2) : 0,
                ]
            ];

            return $this->jsonResponse($responseData, 'Attendance summary retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve attendance summary', $e->getMessage(), 500);
        }
    }

    /**
     * API logout method
     */
    public function apiLogout(Request $request): JsonResponse
    {
        try {
            // Revoke the current access token
            $user = $request->user();
            if ($user) {
                $user->currentAccessToken()->delete();
            }

            // Also handle session logout for backward compatibility
            Auth::guard('karyawan')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->jsonResponse(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed', $e->getMessage(), 500);
        }
    }

    /**
     * Refresh authentication token
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->errorResponse('Unauthenticated', null, 401);
            }

            // Revoke current token
            $user->currentAccessToken()->delete();

            // Create new token
            $newToken = $user->createToken('mobile-app')->plainTextToken;

            $responseData = [
                'token' => $newToken,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id_karyawan,
                    'name' => $user->nama_karyawan,
                    'email' => $user->email_karyawan,
                    'username' => $user->username_karyawan,
                ]
            ];

            Log::info('Token refreshed for user: ' . $user->id_karyawan);
            return $this->jsonResponse($responseData, 'Token refreshed successfully');
        } catch (\Exception $e) {
            Log::error('Token refresh failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('Token refresh failed', $e->getMessage(), 500);
        }
    }

    /**
     * Validate current authentication token
     */
    public function validateToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->errorResponse('Token is invalid', null, 401);
            }

            $responseData = [
                'valid' => true,
                'user' => [
                    'id' => $user->id_karyawan,
                    'name' => $user->nama_karyawan,
                    'email' => $user->email_karyawan,
                    'username' => $user->username_karyawan,
                ],
                'token_name' => $user->currentAccessToken()->name ?? 'unknown',
                'last_used_at' => $user->currentAccessToken()->last_used_at ?? null,
            ];

            return $this->jsonResponse($responseData, 'Token is valid');
        } catch (\Exception $e) {
            return $this->errorResponse('Token validation failed', $e->getMessage(), 500);
        }
    }

    /**
     * Determine status based on attendance record
     */
    private function determineStatus($absen)
    {
        // If is_lembur is true, set status to lembur
        if ($absen->is_lembur) {
            return 'lembur';
        }

        // If status is already set to manual statuses, return it
        if ($absen->status && in_array(strtolower($absen->status), ['izin', 'sakit', 'cuti', 'dinas luar'])) {
            return ucfirst($absen->status);
        }

        if (!$absen->jam_masuk) {
            return 'tidak hadir';
        }

        if ($absen->jamKerja) {
            $jamMasukNormal = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_masuk_normal);
            $jamMasukActual = Carbon::createFromFormat('H:i:s', $absen->jam_masuk);

            // Tambahkan toleransi keterlambatan
            $toleransi = $absen->jamKerja->toleransi_keterlambatan ?? 0;
            $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);

            $isLateIn = $jamMasukActual->gt($jamMasukNormalWithTolerance);

            if ($isLateIn) {
                // Calculate lateness minutes beyond tolerance
                $menitKeterlambatan = $jamMasukNormal->diffInMinutes($jamMasukActual, false) - $toleransi;
                $absen->update(['menit_keterlambatan' => $menitKeterlambatan]);
            }

            if (!$absen->jam_keluar) {
                // No check-out, status based on check-in only
                return $isLateIn ? 'terlambat' : 'hadir';
            }

            // Has check-out
            $jamKeluarNormal = Carbon::createFromFormat('H:i:s', $absen->jamKerja->jam_keluar_normal);
            $jamKeluarActual = Carbon::createFromFormat('H:i:s', $absen->jam_keluar);
            $toleransiPulangCepat = $absen->jamKerja->toleransi_pulang_cepat ?? 0;
            $jamKeluarNormalWithTolerance = $jamKeluarNormal->copy()->subMinutes($toleransiPulangCepat);

            $isEarlyOut = $jamKeluarActual->lt($jamKeluarNormalWithTolerance);

            if ($isEarlyOut) {
                // Calculate early leave minutes beyond tolerance
                $menitPulangCepat = $jamKeluarNormalWithTolerance->diffInMinutes($jamKeluarActual, false);
                $absen->update(['menit_pulang_cepat' => $menitPulangCepat]);
            }

            if ($isLateIn && $isEarlyOut) {
                return 'tidak konsisten';
            } elseif (!$isLateIn && $isEarlyOut) {
                return 'pulang cepat';
            } elseif ($isLateIn && !$isEarlyOut) {
                return 'terlambat';
            } else {
                return 'hadir';
            }
        }

        // Default jika tidak ada jam kerja
        return 'hadir';
    }


}
