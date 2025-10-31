<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceValidationController extends Controller
{
    /**
     * Check today's attendance status to prevent double attendance
     * 
     * Endpoint: GET /api/v1/attendance/check-today
     * Query Parameter: date (yyyy-mm-dd format)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkToday(Request $request)
    {
        try {
            // Get authenticated user
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get date from request, default to today
            $date = $request->query('date', Carbon::now()->format('Y-m-d'));
            
            // Validate date format
            try {
                Carbon::createFromFormat('Y-m-d', $date);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Use YYYY-MM-DD'
                ], 400);
            }

            // Get attendance records for the specified date
            $attendances = Absensi::where('id_karyawan', $user->id_karyawan)
                                  ->whereDate('tanggal', $date)
                                  ->get();

            // Initialize status flags
            $hasCheckedIn = false;
            $hasCheckedOut = false;
            $hasLembur = false;
            $hasDinasLuar = false;

            // Check each attendance record
            foreach ($attendances as $attendance) {
                // Check for regular check-in (normal attendance)
                if ($attendance->jam_masuk && $attendance->jenis_absensi !== 'lembur' && $attendance->jenis_absensi !== 'dinas_luar') {
                    $hasCheckedIn = true;
                }

                // Check for check-out
                if ($attendance->jam_keluar) {
                    $hasCheckedOut = true;
                }

                // Check for overtime (lembur)
                if ($attendance->is_lembur || $attendance->jenis_absensi === 'lembur') {
                    $hasLembur = true;
                }

                // Check for outside duty (dinas luar)
                if ($attendance->jenis_absensi === 'dinas_luar') {
                    $hasDinasLuar = true;
                }
            }

            // Log for debugging
            Log::info('Attendance check for user ' . $user->id_karyawan . ' on date ' . $date, [
                'has_checked_in' => $hasCheckedIn,
                'has_checked_out' => $hasCheckedOut,
                'has_lembur' => $hasLembur,
                'has_dinas_luar' => $hasDinasLuar,
                'records_count' => $attendances->count()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance status retrieved successfully',
                'data' => [
                    'date' => $date,
                    'has_checked_in' => $hasCheckedIn,
                    'has_checked_out' => $hasCheckedOut,
                    'has_lembur' => $hasLembur,
                    'has_dinas_luar' => $hasDinasLuar,
                    'total_records' => $attendances->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in checkToday: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check attendance status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed attendance history for a specific date
     * 
     * Endpoint: GET /api/v1/attendance/detail-today
     * Query Parameter: date (yyyy-mm-dd format)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailToday(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $date = $request->query('date', Carbon::now()->format('Y-m-d'));

            // Get all attendance records with details
            $attendances = Absensi::where('id_karyawan', $user->id_karyawan)
                                  ->whereDate('tanggal', $date)
                                  ->select([
                                      'id',
                                      'tanggal',
                                      'jam_masuk',
                                      'jam_keluar',
                                      'jenis_absensi',
                                      'is_lembur',
                                      'latitude',
                                      'longitude',
                                      'foto',
                                      'status',
                                      'keterangan',
                                      'created_at'
                                  ])
                                  ->orderBy('created_at', 'desc')
                                  ->get();

            return response()->json([
                'success' => true,
                'message' => 'Detailed attendance retrieved successfully',
                'data' => [
                    'date' => $date,
                    'attendances' => $attendances,
                    'count' => $attendances->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in detailToday: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get detailed attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate if user can perform a specific attendance type
     * 
     * Endpoint: POST /api/v1/attendance/validate
     * Body: { "date": "2025-10-30", "jenis_absensi": "cek_in" }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateAttendance(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Validate request
            $validated = $request->validate([
                'date' => 'required|date_format:Y-m-d',
                'jenis_absensi' => 'required|in:cek_in,cek_out,lembur,dinas_luar'
            ]);

            $date = $validated['date'];
            $jenisAbsensi = $validated['jenis_absensi'];

            // Get attendance records
            $attendances = Absensi::where('id_karyawan', $user->id_karyawan)
                                  ->whereDate('tanggal', $date)
                                  ->get();

            // Check status
            $hasCheckedIn = false;
            $hasCheckedOut = false;
            $hasLembur = false;
            $hasDinasLuar = false;

            foreach ($attendances as $attendance) {
                if ($attendance->jam_masuk && $attendance->jenis_absensi !== 'lembur' && $attendance->jenis_absensi !== 'dinas_luar') {
                    $hasCheckedIn = true;
                }
                if ($attendance->jam_keluar) {
                    $hasCheckedOut = true;
                }
                if ($attendance->is_lembur || $attendance->jenis_absensi === 'lembur') {
                    $hasLembur = true;
                }
                if ($attendance->jenis_absensi === 'dinas_luar') {
                    $hasDinasLuar = true;
                }
            }

            // Validation logic
            $canProceed = true;
            $blockMessage = '';

            switch ($jenisAbsensi) {
                case 'cek_in':
                    if ($hasCheckedIn) {
                        $canProceed = false;
                        $blockMessage = 'Anda sudah melakukan Cek In hari ini';
                    }
                    break;

                case 'cek_out':
                    if ($hasCheckedOut) {
                        $canProceed = false;
                        $blockMessage = 'Anda sudah melakukan Cek Out hari ini';
                    } elseif (!$hasCheckedIn) {
                        $canProceed = false;
                        $blockMessage = 'Anda belum melakukan Cek In hari ini';
                    }
                    break;

                case 'lembur':
                    if ($hasLembur) {
                        $canProceed = false;
                        $blockMessage = 'Anda sudah melakukan absensi Lembur hari ini';
                    }
                    break;

                case 'dinas_luar':
                    if ($hasDinasLuar) {
                        $canProceed = false;
                        $blockMessage = 'Anda sudah melakukan absensi Dinas Luar hari ini';
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $canProceed ? 'Validation passed' : 'Validation failed',
                'data' => [
                    'can_proceed' => $canProceed,
                    'block_message' => $blockMessage,
                    'jenis_absensi' => $jenisAbsensi,
                    'date' => $date,
                    'current_status' => [
                        'has_checked_in' => $hasCheckedIn,
                        'has_checked_out' => $hasCheckedOut,
                        'has_lembur' => $hasLembur,
                        'has_dinas_luar' => $hasDinasLuar,
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error in validateAttendance: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to validate attendance: ' . $e->getMessage()
            ], 500);
        }
    }
}
