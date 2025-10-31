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
     * API check-in method with enhanced response
     */
    public function apiCheckIn(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'overtime' => 'sometimes|boolean',
            'attendance_type' => 'sometimes|string|in:normal,lembur,dinas_luar',
        ]);

        try {
            $karyawan = Auth::guard('sanctum')->user();
            if (!$karyawan) {
                return $this->errorResponse('Unauthenticated', null, 401);
            }
            
            // Load lokasiKerja relationship
            $karyawan->load('lokasiKerja');
            
            Log::info('User authenticated for check-in: ' . $karyawan->id_karyawan);

            // Determine the date for attendance
            $today = $request->has('tanggal') ? $request->tanggal : Carbon::today()->toDateString();
            $todayCarbon = Carbon::parse($today);

            // Check if today is weekend (Saturday or Sunday) or scheduled holiday
            $isWeekend = $todayCarbon->isWeekend();
            $isHoliday = \App\Models\JadwalPengecualian::isHoliday($today);
            $holidayName = \App\Models\JadwalPengecualian::getHolidayName($today);
            
            // Determine attendance type
            $attendanceType = $request->attendance_type ?? 'normal';
            $isLembur = $request->overtime ?? ($attendanceType === 'lembur');
            
            // VALIDATION: On weekends or holidays, only overtime (lembur) is allowed
            if (($isWeekend || $isHoliday) && !$isLembur) {
                $liburMessage = $isWeekend ? 'hari libur (Sabtu/Minggu)' : "hari libur ({$holidayName})";
                return $this->errorResponse(
                    "Hari ini adalah {$liburMessage}. Anda hanya dapat melakukan absensi dengan memilih 'Lembur'.",
                    [
                        'is_weekend' => $isWeekend,
                        'is_holiday' => $isHoliday,
                        'holiday_name' => $holidayName,
                        'message' => 'Silakan pilih opsi Lembur untuk melakukan absensi pada hari libur.'
                    ],
                    422
                );
            }

            // Check if already checked in on the specified date
            $existingAbsensi = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal_absen', $today)
                ->first();

            if ($existingAbsensi && $existingAbsensi->jam_masuk) {
                return $this->errorResponse('Kamu sudah check-in hari ini', null, 400);
            }

            $isDinasLuar = $attendanceType === 'dinas_luar';
            
            // VALIDATION: Check if overtime is allowed based on jam_lembur setting
            if ($isLembur && !($isWeekend || $isHoliday)) {
                // Get jam kerja settings
                $jamKerja = JamKerja::first();
                
                if (!$jamKerja || !$jamKerja->jam_lembur) {
                    return $this->errorResponse(
                        'Pengaturan jam lembur belum dikonfigurasi. Silakan hubungi admin.',
                        null,
                        422
                    );
                }
                
                $currentTime = Carbon::now();
                $jamLemburMulai = Carbon::parse($today . ' ' . $jamKerja->jam_lembur);
                
                // Check if current time is before overtime start time
                if ($currentTime->lt($jamLemburMulai)) {
                    return $this->errorResponse(
                        "Lembur hanya dapat dilakukan setelah jam {$jamKerja->jam_lembur}. Waktu sekarang: {$currentTime->format('H:i:s')}.",
                        [
                            'jam_lembur_mulai' => $jamKerja->jam_lembur,
                            'waktu_sekarang' => $currentTime->format('H:i:s'),
                            'message' => 'Silakan tunggu hingga jam lembur dimulai atau pilih absensi normal.'
                        ],
                        422
                    );
                }
                
                Log::info('Overtime validation passed - current time: ' . $currentTime->format('H:i:s') . ' >= jam_lembur: ' . $jamKerja->jam_lembur);
            }
            
            // Skip location validation for overtime (lembur) and dinas luar
            $skipLocationValidation = $isLembur || $isDinasLuar;

            // Validate location (skip if overtime/dinas luar)
            $isWithinRadius = false;
            $validLocation = null;
            
            if ($skipLocationValidation) {
                $isWithinRadius = true;
                Log::info('Skipping location validation for ' . $attendanceType . ' - user: ' . $karyawan->id_karyawan);
            } else {
                // Check if karyawan has assigned work location (area kerja)
                if (!$karyawan->lokasiKerja) {
                    Log::warning('No work location assigned for user: ' . $karyawan->id_karyawan);
                    return $this->errorResponse(
                        'Anda belum memiliki lokasi kerja yang ditentukan. Silakan hubungi admin untuk mengatur lokasi kerja Anda.',
                        null,
                        422
                    );
                }
                
                Log::info('Checking location for user ' . $karyawan->id_karyawan . ' against area kerja: ' . $karyawan->lokasiKerja->lokasi_kerja);
                
                // Use karyawan's specific work location for validation - ONLY check assigned location
                try {
                    if ($karyawan->lokasiKerja->isWithinRadius($request->latitude, $request->longitude)) {
                        $isWithinRadius = true;
                        $validLocation = $karyawan->lokasiKerja->lokasi_kerja;
                        Log::info('Location validated against karyawan area kerja: ' . $validLocation);
                    } else {
                        // Calculate actual distance for better error message
                        $distance = $karyawan->lokasiKerja->getDistanceFrom($request->latitude, $request->longitude);
                        $radius = $karyawan->lokasiKerja->radius;
                        
                        Log::info('Location validation failed - outside karyawan area kerja for user: ' . $karyawan->id_karyawan . ' at ' . $request->latitude . ',' . $request->longitude);
                        
                        $errorMessage = sprintf(
                            'Lokasi Anda berada di luar area kerja "%s". Jarak Anda: %.1f meter (maksimal: %.0f meter). Silakan kembali ke area kerja untuk melakukan check-in.',
                            $karyawan->lokasiKerja->lokasi_kerja,
                            $distance,
                            $radius
                        );
                        
                        return $this->errorResponse($errorMessage, [
                            'distance' => round($distance, 1),
                            'max_radius' => $radius,
                            'location_name' => $karyawan->lokasiKerja->lokasi_kerja
                        ], 422);
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking location radius: ' . $e->getMessage());
                    return $this->errorResponse('Gagal memvalidasi lokasi. Silakan coba lagi.', $e->getMessage(), 500);
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
                'is_lembur' => $isLembur,
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
            // If dinas luar, set status explicitly and auto-fill checkout
            if ($isDinasLuar) {
                $absensi->update(['status' => 'dinas luar']);
                
                // Auto checkout untuk dinas luar - set jam_keluar sesuai jam pulang normal
                $jamKerja = JamKerja::first();
                if ($jamKerja && $jamKerja->jam_keluar_normal) {
                    $tanggalAbsen = Carbon::parse($today);
                    $jamKeluarNormal = Carbon::parse($jamKerja->jam_keluar_normal);
                    $autoCheckoutTime = $tanggalAbsen->setTime($jamKeluarNormal->hour, $jamKeluarNormal->minute, $jamKeluarNormal->second);
                    
                    $absensi->update([
                        'jam_keluar' => $autoCheckoutTime->toTimeString(),
                        'lokasi_absen_keluar' => $request->latitude . ',' . $request->longitude,
                    ]);
                    
                    Log::info('Auto checkout set for dinas luar: ' . $autoCheckoutTime->toTimeString());
                }
            } else {
                $status = $this->determineStatus($absensi);
                $absensi->update(['status' => strtolower($status)]);
            }

            // Detect if late check-in
            $isLate = false;
            $lateMinutes = 0;
            if ($jamKerja && $jamKerja->jam_masuk_normal && !$isDinasLuar && !$isLembur) {
                $jamMasukNormal = Carbon::parse($jamKerja->jam_masuk_normal);
                $toleransi = $jamKerja->toleransi_keterlambatan ?? 0;
                $batasWaktu = $jamMasukNormal->copy()->addMinutes($toleransi);
                
                if ($currentTime->gt($batasWaktu)) {
                    $isLate = true;
                    $lateMinutes = $currentTime->diffInMinutes($jamMasukNormal);
                }
            }

            $responseData = [
                'attendance_id' => $absensi->id_absensi,
                'check_in_time' => $currentTime->format('H:i:s'),
                'date' => $today,
                'location' => $validLocation,
                'coordinates' => $request->latitude . ',' . $request->longitude,
                'photo_url' => $fotoPath ? Storage::url($fotoPath) : null,
                'is_late' => $isLate,
                'late_minutes' => $lateMinutes,
                'normal_check_in_time' => $jamKerja && $jamKerja->jam_masuk_normal ? $jamKerja->jam_masuk_normal : null,
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
            $todayCarbon = Carbon::parse($today);
            
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

            // Check if today is weekend (Saturday or Sunday) or scheduled holiday
            $isWeekend = $todayCarbon->isWeekend();
            $isHoliday = \App\Models\JadwalPengecualian::isHoliday($today);
            $holidayName = \App\Models\JadwalPengecualian::getHolidayName($today);
            
            // VALIDATION: On weekends or holidays, only lembur attendance is allowed
            if (($isWeekend || $isHoliday) && !$absensi->is_lembur) {
                $liburMessage = $isWeekend ? 'hari libur (Sabtu/Minggu)' : "hari libur ({$holidayName})";
                return $this->errorResponse(
                    "Hari ini adalah {$liburMessage}. Anda hanya dapat melakukan check-out jika absensi Anda bertipe 'Lembur'.",
                    [
                        'is_weekend' => $isWeekend,
                        'is_holiday' => $isHoliday,
                        'holiday_name' => $holidayName,
                        'message' => 'Check-out tidak diizinkan untuk absensi normal pada hari libur.'
                    ],
                    422
                );
            }

            // Check if this is lembur or dinas luar attendance to skip location validation
            $skipLocationValidation = $absensi->is_lembur || (strtolower($absensi->status) === 'dinas luar');

            // Validate location for check-out as well (skip if lembur/dinas luar)
            $isWithinRadius = false;
            $validLocation = null;
            
            if ($skipLocationValidation) {
                $isWithinRadius = true;
                $validLocation = $absensi->is_lembur ? 'Lembur (Bebas Lokasi)' : 'Dinas Luar (Bebas Lokasi)';
                Log::info('Skipping location validation for checkout (lembur/dinas luar) for user: ' . $karyawan->id_karyawan);
            } else {
                // Load karyawan's work location
                $karyawan->load('lokasiKerja');
                
                // Check if karyawan has assigned work location
                if (!$karyawan->lokasiKerja) {
                    Log::warning('No work location assigned for user on checkout: ' . $karyawan->id_karyawan);
                    return $this->errorResponse(
                        'Anda belum memiliki lokasi kerja yang ditentukan. Silakan hubungi admin untuk mengatur lokasi kerja Anda.',
                        null,
                        422
                    );
                }
                
                Log::info('Checking checkout location for user ' . $karyawan->id_karyawan . ' against area kerja: ' . $karyawan->lokasiKerja->lokasi_kerja);
                
                // Use karyawan's specific work location for validation - ONLY check assigned location
                try {
                    if ($karyawan->lokasiKerja->isWithinRadius($request->latitude, $request->longitude)) {
                        $isWithinRadius = true;
                        $validLocation = $karyawan->lokasiKerja->lokasi_kerja;
                        Log::info('Checkout location validated against karyawan area kerja: ' . $validLocation);
                    } else {
                        // Calculate actual distance for better error message
                        $distance = $karyawan->lokasiKerja->getDistanceFrom($request->latitude, $request->longitude);
                        $radius = $karyawan->lokasiKerja->radius;
                        
                        Log::info('Check-out location validation failed - outside karyawan area kerja for user: ' . $karyawan->id_karyawan . ' at ' . $request->latitude . ',' . $request->longitude);
                        
                        $errorMessage = sprintf(
                            'Lokasi Anda berada di luar area kerja "%s". Jarak Anda: %.1f meter (maksimal: %.0f meter). Silakan kembali ke area kerja untuk melakukan check-out.',
                            $karyawan->lokasiKerja->lokasi_kerja,
                            $distance,
                            $radius
                        );
                        
                        return $this->errorResponse($errorMessage, [
                            'distance' => round($distance, 1),
                            'max_radius' => $radius,
                            'location_name' => $karyawan->lokasiKerja->lokasi_kerja
                        ], 422);
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking checkout location radius: ' . $e->getMessage());
                    return $this->errorResponse('Gagal memvalidasi lokasi. Silakan coba lagi.', $e->getMessage(), 500);
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

            // Detect if early check-out
            $isEarly = false;
            $earlyMinutes = 0;
            $jamKerja = JamKerja::first();
            if ($jamKerja && $jamKerja->jam_keluar_normal) {
                $jamKeluarNormal = Carbon::parse($jamKerja->jam_keluar_normal);
                $toleransi = $jamKerja->toleransi_pulang_cepat ?? 0;
                $batasWaktu = $jamKeluarNormal->copy()->subMinutes($toleransi);
                
                if ($currentTime->lt($batasWaktu)) {
                    $isEarly = true;
                    $earlyMinutes = $jamKeluarNormal->diffInMinutes($currentTime);
                }
            }

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
                'is_early' => $isEarly,
                'early_minutes' => $earlyMinutes,
                'normal_check_out_time' => $jamKerja && $jamKerja->jam_keluar_normal ? $jamKerja->jam_keluar_normal : null,
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
            'month' => 'sometimes|integer|min:1|max:12',
            'year' => 'sometimes|integer|min:2020|max:2100',
        ]);

        try {
            $karyawan = $request->user();
            if (!$karyawan) {
                return $this->errorResponse('Unauthenticated', null, 401);
            }
            
            $limit = $request->limit ?? 30;
            
            // Build query based on filter
            $query = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->with(['jamKerja']);
            
            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = $request->start_date;
                $endDate = $request->end_date;
                $query->whereBetween('tanggal_absen', [$startDate, $endDate]);
            } 
            // Filter by month and year
            elseif ($request->has('month') && $request->has('year')) {
                $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($request->year, $request->month, 1)->endOfMonth();
                $query->whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()]);
            } 
            // Filter by year only
            elseif ($request->has('year')) {
                $startDate = Carbon::createFromDate($request->year, 1, 1)->startOfYear();
                $endDate = Carbon::createFromDate($request->year, 12, 31)->endOfYear();
                $query->whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()]);
            }
            // Default: Last 30 days
            else {
                $startDate = Carbon::today()->subDays(30);
                $endDate = Carbon::today();
                $query->whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()]);
            }
            
            $query->orderBy('tanggal_absen', 'desc')
                  ->orderBy('jam_masuk', 'desc');

            $attendances = $query->paginate($limit);

            // If no data found
            if ($attendances->isEmpty()) {
                return $this->jsonResponse([
                    'attendances' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => $limit,
                        'total' => 0,
                    ]
                ], 'Tidak ada data absensi untuk periode yang dipilih');
            }

            $formattedData = $attendances->map(function ($attendance) use ($karyawan) {
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
                
                // Get location name only
                $lokasiMasuk = null;
                $lokasiKeluar = null;
                if ($karyawan->lokasiKerja) {
                    $lokasiMasuk = $karyawan->lokasiKerja->lokasi_kerja;
                    $lokasiKeluar = $karyawan->lokasiKerja->lokasi_kerja;
                }
                
                // Determine status
                $status = $this->determineHistoryStatus($attendance);

                return [
                    'id' => $attendance->id_absensi,
                    'date' => $attendance->tanggal_absen,
                    'day_name' => Carbon::parse($attendance->tanggal_absen)->locale('id')->isoFormat('dddd'),
                    'check_in_time' => $attendance->jam_masuk,
                    'check_out_time' => $attendance->jam_keluar,
                    'check_in_location' => $lokasiMasuk,
                    'check_out_location' => $lokasiKeluar,
                    'check_in_photo' => $attendance->foto_masuk ? Storage::url($attendance->foto_masuk) : null,
                    'check_out_photo' => $attendance->foto_keluar ? Storage::url($attendance->foto_keluar) : null,
                    'notes' => $attendance->keterangan,
                    'work_duration' => $workDuration,
                    'is_lembur' => $attendance->is_lembur,
                    'status' => $status,
                    'terlambat' => $terlambat,
                    'cepat_pulang' => $cepatPulang,
                    'override_request' => $attendance->override_request,
                    'override_status' => $attendance->override_status,
                    'override_reason' => $attendance->override_reason,
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
            Log::error('Error in getAttendanceHistory: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve attendance history', $e->getMessage(), 500);
        }
    }
    
    /**
     * Get base status without override considerations (for history)
     */
    private function getBaseHistoryStatus($attendance)
    {
        // Check if weekend or holiday
        $tanggalAbsen = Carbon::parse($attendance->tanggal_absen);
        if ($tanggalAbsen->isWeekend()) {
            if (!$attendance->jam_masuk) {
                return 'Libur';
            }
            if ($attendance->is_lembur) {
                return 'Lembur';
            }
        }
        
        // Check holiday from jadwal_pengecualian
        $isHoliday = \App\Models\JadwalPengecualian::isHoliday($attendance->tanggal_absen);
        if ($isHoliday) {
            if (!$attendance->jam_masuk) {
                return 'Libur';
            }
            if ($attendance->is_lembur) {
                return 'Lembur';
            }
        }

        // If is_lembur is true, set status to lembur
        if ($attendance->is_lembur) {
            return 'Lembur';
        }

        // If status is already set to manual statuses, return it
        if ($attendance->status && in_array(strtolower($attendance->status), ['izin', 'sakit', 'cuti', 'dinas luar'])) {
            return ucfirst($attendance->status);
        }

        if (!$attendance->jam_masuk) {
            return 'Tidak Hadir';
        }

        if ($attendance->jamKerja) {
            $jamMasukNormal = Carbon::createFromFormat('H:i:s', $attendance->jamKerja->jam_masuk_normal);
            $jamMasukActual = Carbon::createFromFormat('H:i:s', $attendance->jam_masuk);

            // Tambahkan toleransi keterlambatan
            $toleransi = $attendance->jamKerja->toleransi_keterlambatan ?? 0;
            $jamMasukNormalWithTolerance = $jamMasukNormal->copy()->addMinutes($toleransi);

            $isLateIn = $jamMasukActual->gt($jamMasukNormalWithTolerance);

            if (!$attendance->jam_keluar) {
                // No check-out, status based on check-in only
                return $isLateIn ? 'Terlambat' : 'Hadir';
            }

            // Has check-out
            $jamKeluarNormal = Carbon::createFromFormat('H:i:s', $attendance->jamKerja->jam_keluar_normal);
            $jamKeluarActual = Carbon::createFromFormat('H:i:s', $attendance->jam_keluar);
            $toleransiPulangCepat = $attendance->jamKerja->toleransi_pulang_cepat ?? 0;
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

    /**
     * Determine status for history display
     */
    private function determineHistoryStatus($attendance)
    {
        // Check override request status first
        if ($attendance->override_request) {
            if ($attendance->override_status === 'pending') {
                // If override is pending, show status with pending note
                $baseStatus = $this->getBaseHistoryStatus($attendance);
                return $baseStatus . ' (Menunggu Approval)';
            } elseif ($attendance->override_status === 'rejected') {
                // If override is rejected, show status with rejected note
                $baseStatus = $this->getBaseHistoryStatus($attendance);
                return $baseStatus . ' (Override Ditolak)';
            }
            // If approved, continue with normal status determination
        }

        // Get base status for all other cases
        return $this->getBaseHistoryStatus($attendance);
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
            'year' => 'sometimes|integer|min:2020|max:2100',
        ]);

        try {
            $karyawan = $request->user();
            if (!$karyawan) {
                return $this->errorResponse('Unauthenticated', null, 401);
            }
            
            $month = $request->month ?? Carbon::now()->month;
            $year = $request->year ?? Carbon::now()->year;

            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            
            // Don't count future dates
            if ($endDate->isFuture()) {
                $endDate = Carbon::today();
            }

            $attendances = Absensi::with('jamKerja')
                ->where('id_karyawan', $karyawan->id_karyawan)
                ->whereBetween('tanggal_absen', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();

            // Statistics counters
            $stats = [
                'hadir' => 0,
                'terlambat' => 0,
                'lembur' => 0,
                'tidak_hadir' => 0,
                'libur' => 0,
                'izin' => 0,
                'sakit' => 0,
                'cuti' => 0,
            ];
            
            $totalWorkMinutes = 0;
            $completeDays = 0;

            // Process each attendance record
            foreach ($attendances as $attendance) {
                $status = $this->determineHistoryStatus($attendance);
                
                // Count by status
                $statusLower = strtolower($status);
                if ($statusLower === 'hadir') {
                    $stats['hadir']++;
                } elseif ($statusLower === 'terlambat') {
                    $stats['terlambat']++;
                } elseif ($statusLower === 'lembur') {
                    $stats['lembur']++;
                } elseif ($statusLower === 'tidak hadir') {
                    $stats['tidak_hadir']++;
                } elseif ($statusLower === 'libur') {
                    $stats['libur']++;
                } elseif ($statusLower === 'izin') {
                    $stats['izin']++;
                } elseif ($statusLower === 'sakit') {
                    $stats['sakit']++;
                } elseif ($statusLower === 'cuti') {
                    $stats['cuti']++;
                }
                
                // Calculate work hours
                if ($attendance->jam_masuk && $attendance->jam_keluar) {
                    $checkIn = Carbon::parse($attendance->tanggal_absen . ' ' . $attendance->jam_masuk);
                    $checkOut = Carbon::parse($attendance->tanggal_absen . ' ' . $attendance->jam_keluar);
                    $totalWorkMinutes += $checkIn->diffInMinutes($checkOut);
                    $completeDays++;
                }
            }

            $totalWorkHours = floor($totalWorkMinutes / 60);
            $totalWorkMinutesRemainder = $totalWorkMinutes % 60;
            $averageWorkHours = $completeDays > 0 ? round($totalWorkMinutes / $completeDays / 60, 2) : 0;

            // Count working days (exclude weekends and holidays)
            $workingDays = 0;
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                $isWeekend = $currentDate->isWeekend();
                $isHoliday = \App\Models\JadwalPengecualian::isHoliday($currentDate->toDateString());
                
                if (!$isWeekend && !$isHoliday) {
                    $workingDays++;
                }
                
                $currentDate->addDay();
            }
            
            // Calculate attendance percentage (only for working days)
            $presentDays = $stats['hadir'] + $stats['terlambat'];
            $attendancePercentage = $workingDays > 0 ? round(($presentDays / $workingDays) * 100, 2) : 0;

            $responseData = [
                'period' => [
                    'month' => $month,
                    'year' => $year,
                    'month_name' => Carbon::create($year, $month, 1)->locale('id')->monthName,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'statistics' => [
                    'total_records' => $attendances->count(),
                    'hadir' => $stats['hadir'],
                    'terlambat' => $stats['terlambat'],
                    'lembur' => $stats['lembur'],
                    'tidak_hadir' => $stats['tidak_hadir'],
                    'libur' => $stats['libur'],
                    'izin' => $stats['izin'],
                    'sakit' => $stats['sakit'],
                    'cuti' => $stats['cuti'],
                    'complete_days' => $completeDays,
                    'total_work_hours' => $totalWorkHours,
                    'total_work_minutes' => $totalWorkMinutesRemainder,
                    'total_work_time_formatted' => sprintf('%d jam %d menit', $totalWorkHours, $totalWorkMinutesRemainder),
                    'average_work_hours_per_day' => $averageWorkHours,
                ],
                'attendance_rate' => [
                    'working_days_in_month' => $workingDays,
                    'present_days' => $presentDays,
                    'attendance_percentage' => $attendancePercentage,
                ]
            ];

            return $this->jsonResponse($responseData, 'Attendance summary retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error in getAttendanceSummary: ' . $e->getMessage());
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
     * Get base status without override considerations
     */
    private function getBaseStatus($absen)
    {
        // Check if weekend or holiday
        $tanggalAbsen = Carbon::parse($absen->tanggal_absen);
        if ($tanggalAbsen->isWeekend()) {
            if (!$absen->jam_masuk) {
                return 'libur';
            }
            if ($absen->is_lembur) {
                return 'lembur';
            }
        }
        
        // Check holiday from jadwal_pengecualian
        $isHoliday = \App\Models\JadwalPengecualian::isHoliday($absen->tanggal_absen);
        if ($isHoliday) {
            if (!$absen->jam_masuk) {
                return 'libur';
            }
            if ($absen->is_lembur) {
                return 'lembur';
            }
        }

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

    /**
     * Determine status based on attendance record
     */
    private function determineStatus($absen)
    {
        // Check override request status first
        if ($absen->override_request) {
            if ($absen->override_status === 'pending') {
                $baseStatus = $this->getBaseStatus($absen);
                return $baseStatus . ' (menunggu approval)';
            } elseif ($absen->override_status === 'rejected') {
                $baseStatus = $this->getBaseStatus($absen);
                return $baseStatus . ' (override ditolak)';
            }
            // If approved, continue with normal status determination
        }

        // Get base status for all other cases
        return $this->getBaseStatus($absen);
    }

    /**
     * API method untuk request override ke Manager SDM
     * Digunakan ketika karyawan terlambat check-in atau pulang cepat
     */
    public function apiRequestOverride(Request $request): JsonResponse
    {
        // Log incoming request dengan detail lengkap
        Log::info('Override request received - FULL DEBUG', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'all_input' => $request->all(),
        ]);

        $request->validate([
            'id_absensi' => 'required|exists:absensi,id_absensi',
            'reason' => 'required|string|min:10|max:500',
            'override_type' => 'required|string|in:late_check_in,early_check_out',
        ]);

        try {
            $karyawan = Auth::guard('sanctum')->user();
            if (!$karyawan) {
                Log::warning('Override request without authentication');
                return $this->errorResponse('Unauthenticated', null, 401);
            }

            $absensi = Absensi::findOrFail($request->id_absensi);

            // Verify this absensi belongs to the authenticated employee
            if ($absensi->id_karyawan !== $karyawan->id_karyawan) {
                return $this->errorResponse('Anda tidak memiliki akses ke data absensi ini.', null, 403);
            }

            Log::info('Processing override request', [
                'karyawan_id' => $karyawan->id_karyawan,
                'absensi_id' => $absensi->id_absensi,
                'tanggal_absen' => $absensi->tanggal_absen,
                'override_type' => $request->override_type,
                'reason_length' => strlen($request->reason),
            ]);

            // Check if already has pending override request
            if ($absensi->override_request && $absensi->override_status === 'pending') {
                Log::warning('Duplicate override request attempt', [
                    'absensi_id' => $absensi->id_absensi,
                    'karyawan_id' => $karyawan->id_karyawan,
                ]);
                return $this->errorResponse('Anda sudah memiliki permintaan override yang sedang diproses untuk tanggal ini.', null, 400);
            }

            if ($absensi->override_request && $absensi->override_status === 'approved') {
                Log::warning('Override request for already approved attendance', [
                    'absensi_id' => $absensi->id_absensi,
                    'karyawan_id' => $karyawan->id_karyawan,
                ]);
                return $this->errorResponse('Permintaan override untuk tanggal ini sudah disetujui sebelumnya.', null, 400);
            }

            // Validate override type matches the actual problem
            $overrideType = $request->override_type;
            $overrideTypeText = $overrideType === 'late_check_in' ? 'Terlambat Check In' : 'Pulang Cepat';

            // Update attendance with override request
            $absensi->update([
                'override_request' => true,
                'override_reason' => "[{$overrideTypeText}] " . $request->reason,
                'override_status' => 'pending',
                'override_requested_at' => now(),
                'override_type' => $overrideType,
            ]);

            Log::info('Override request created successfully', [
                'absensi_id' => $absensi->id_absensi,
                'karyawan_id' => $karyawan->id_karyawan,
                'override_type' => $overrideType,
            ]);

            return $this->jsonResponse([
                'absensi_id' => $absensi->id_absensi,
                'status' => 'pending',
                'override_type' => $overrideType,
                'message' => 'Permintaan override berhasil dikirim ke Manager SDM',
            ], 'Permintaan override telah dikirim. Menunggu persetujuan Manager SDM.', true, 201);

        } catch (\Exception $e) {
            Log::error('Error creating override request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse('Terjadi kesalahan saat membuat permintaan override.', $e->getMessage(), 500);
        }
    }

    /**
     * DEBUG ENDPOINT - Test override request untuk melihat data yang diterima
     * Endpoint ini akan dihapus setelah debugging selesai
     */
    public function debugOverrideRequest(Request $request): JsonResponse
    {
        Log::info('DEBUG Override Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'all_input' => $request->all(),
            'json_input' => $request->json()->all(),
            'raw_content' => $request->getContent(),
        ]);

        return $this->jsonResponse([
            'received_data' => [
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'all_input' => $request->all(),
                'json_input' => $request->json()->all(),
                'raw_content' => $request->getContent(),
                'has_id_absensi' => $request->has('id_absensi'),
                'has_reason' => $request->has('reason'),
                'id_absensi_value' => $request->input('id_absensi'),
                'reason_value' => $request->input('reason'),
            ],
            'authentication' => [
                'is_authenticated' => $request->user() !== null,
                'user_id' => $request->user()?->id_karyawan,
            ]
        ], 'Debug data received successfully');
    }


}
