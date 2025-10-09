<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\LokasiKerja;
use App\Models\Departemen;
use App\Models\Posisi;
use App\Models\Admin;
use App\Models\JamKerja;

class ApiController extends Controller
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
     * Get all work locations
     */
    public function getLocations(): JsonResponse
    {
        try {
            $locations = LokasiKerja::select('id_lokasi', 'lokasi_kerja', 'latitude', 'longitude', 'radius')->get();
            
            return $this->jsonResponse($locations, 'Locations retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve locations', $e->getMessage(), 500);
        }
    }

    /**
     * Get all departments
     */
    public function getDepartments(): JsonResponse
    {
        try {
            $departments = Departemen::select('id_departemen', 'nama_departemen')->get();
            
            return $this->jsonResponse($departments, 'Departments retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve departments', $e->getMessage(), 500);
        }
    }

    /**
     * Get positions by department
     */
    public function getPositions($departmentId = null): JsonResponse
    {
        try {
            $query = Posisi::select('id_posisi', 'nama_posisi', 'id_departemen');
            
            if ($departmentId) {
                $query->where('id_departemen', $departmentId);
            }
            
            $positions = $query->get();
            
            return $this->jsonResponse($positions, 'Positions retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve positions', $e->getMessage(), 500);
        }
    }

    /**
     * Validate if location is within allowed work areas
     */
    public function validateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            $isWithinRadius = false;
            $validLocation = null;
            
            $lokasiKerjas = LokasiKerja::all();
            
            foreach ($lokasiKerjas as $lokasi) {
                if ($lokasi->isWithinRadius($latitude, $longitude)) {
                    $isWithinRadius = true;
                    $validLocation = [
                        'id' => $lokasi->id_lokasi,
                        'name' => $lokasi->lokasi_kerja,
                        'distance' => $this->calculateDistance(
                            $lokasi->latitude, 
                            $lokasi->longitude, 
                            $latitude, 
                            $longitude
                        )
                    ];
                    break;
                }
            }

            $responseData = [
                'is_valid' => $isWithinRadius,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location' => $validLocation
            ];

            if ($isWithinRadius) {
                return $this->jsonResponse($responseData, 'Location is within allowed work area');
            } else {
                return $this->jsonResponse($responseData, 'Location is outside allowed work area', false, 403);
            }
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to validate location', $e->getMessage(), 500);
        }
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371000; // Earth radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Admin login method
     */
    public function adminLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if (Auth::guard('admin')->attempt($credentials)) {
                $request->session()->regenerate();
                
                $admin = Auth::guard('admin')->user();
                
                // Revoke all previous tokens to enforce single device login
                $admin->tokens()->delete();

                // Generate API token for mobile authentication
                $token = $admin->createToken('mobile-app')->plainTextToken;
                
                $adminData = [
                    'user' => [
                        'id' => $admin->id_admin,
                        'name' => $admin->nama_admin,
                        'email' => $admin->email,
                        'role' => 'admin',
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ];

                return $this->jsonResponse($adminData, 'Admin login successful');
            }

            return $this->errorResponse('Invalid email or password', null, 401);
        } catch (\Exception $e) {
            return $this->errorResponse('Admin login failed', $e->getMessage(), 500);
        }
    }

    /**
     * Get application settings and configuration
     */
    public function getSettings(): JsonResponse
    {
        try {
            // Fetch jam kerja from database
            $jamKerja = JamKerja::first();

            $workHours = [
                'start_time' => $jamKerja ? $jamKerja->jam_masuk_normal : '08:00:00',
                'end_time' => $jamKerja ? $jamKerja->jam_keluar_normal : '17:00:00',
                'break_duration' => 60, // minutes
                'overtime_start_time' => $jamKerja && $jamKerja->jam_lembur ? $jamKerja->jam_lembur : '17:00:00',
            ];

            $settings = [
                'app_name' => config('app.name', 'Admin Absensi'),
                'app_version' => '1.0.0',
                'timezone' => config('app.timezone', 'Asia/Singapore'),
                'work_hours' => $workHours,
                'attendance_rules' => [
                    'late_threshold_minutes' => $jamKerja ? $jamKerja->toleransi_keterlambatan : 15,
                    'early_departure_threshold_minutes' => $jamKerja ? $jamKerja->toleransi_pulang_cepat : 30,
                    'overtime_start_time' => $workHours['overtime_start_time'],
                    'max_work_hours_per_day' => $jamKerja && $jamKerja->total_jam ? $jamKerja->total_jam : 12,
                ],
                'photo_requirements' => [
                    'max_size_mb' => 2,
                    'allowed_formats' => ['jpeg', 'jpg', 'png'],
                    'required_for_checkin' => false,
                    'required_for_checkout' => false,
                ],
                'location_settings' => [
                    'validation_required' => true,
                    'default_radius_meters' => 100,
                    'gps_accuracy_required_meters' => 50,
                ],
                'api_info' => [
                    'version' => 'v1',
                    'base_url' => url('/api/v1'),
                    'documentation_url' => url('/api/docs'),
                ],
            ];

            return $this->jsonResponse($settings, 'Settings retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve settings', $e->getMessage(), 500);
        }
    }

    /**
     * Get work hours from database
     */
    public function getJamKerja(): JsonResponse
    {
        try {
            $jamKerja = JamKerja::first();

            if (!$jamKerja) {
                return $this->jsonResponse(null, 'Jam kerja belum diatur', false, 404);
            }

            return $this->jsonResponse($jamKerja, 'Jam kerja berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve jam kerja', $e->getMessage(), 500);
        }
    }

    /**
     * Update work hours settings
     */
    public function updateJamKerja(Request $request): JsonResponse
    {
        $request->validate([
            'jam_masuk_normal' => 'required|regex:/^[0-9]{1,2}:[0-5][0-9](:[0-5][0-9])?$/',
            'jam_keluar_normal' => 'required|regex:/^[0-9]{1,2}:[0-5][0-9](:[0-5][0-9])?$/',
            'toleransi_keterlambatan' => 'required|integer|min:0',
            'toleransi_pulang_cepat' => 'required|integer|min:0',
            'jam_lembur' => 'nullable|regex:/^[0-9]{1,2}:[0-5][0-9](:[0-5][0-9])?$/',
            'total_jam' => 'nullable|integer|min:1',
        ]);

        try {
            $jamKerja = JamKerja::first();
            if (!$jamKerja) {
                $jamKerja = new JamKerja();
            }

            $jamKerja->fill($request->only(['jam_masuk_normal', 'jam_keluar_normal', 'toleransi_keterlambatan', 'toleransi_pulang_cepat', 'jam_lembur', 'total_jam']));
            $jamKerja->save();

            return $this->jsonResponse($jamKerja, 'Jam kerja berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update jam kerja', $e->getMessage(), 500);
        }
    }

    /**
     * Get server time
     */
    public function getServerTime(): JsonResponse
    {
        try {
            $serverTime = now()->toISOString();
            $timestamp = now()->timestamp * 1000; // milliseconds for JavaScript/Android

            $data = [
                'server_time' => $serverTime,
                'timestamp' => $timestamp,
                'timezone' => config('app.timezone', 'UTC'),
            ];

            return $this->jsonResponse($data, 'Server time retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve server time', $e->getMessage(), 500);
        }
    }
}
