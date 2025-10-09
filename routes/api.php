<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API v1 routes
Route::prefix('v1')->group(function () {
    
    // Public routes (no authentication required)
    Route::post('/employee/login', [EmployeeController::class, 'apiLogin']);
    Route::post('/admin/login', [ApiController::class, 'adminLogin']);
    Route::get('/locations', [ApiController::class, 'getLocations']);
    Route::get('/departments', [ApiController::class, 'getDepartments']);
    Route::get('/positions/{departmentId?}', [ApiController::class, 'getPositions']);
    Route::get('/settings', [ApiController::class, 'getSettings']);
    Route::get('/jam-kerja', [ApiController::class, 'getJamKerja']);
    Route::post('/settings/jam-kerja', [ApiController::class, 'updateJamKerja']);
    Route::get('/server-time', [ApiController::class, 'getServerTime']);
    
    // Protected routes (require authentication)
    Route::middleware('api.auth')->group(function () {
        
        // Employee profile
        Route::get('/employee/profile', [EmployeeController::class, 'getProfile']);
        Route::put('/employee/profile', [EmployeeController::class, 'updateProfile']);
        
        // Token management
        Route::post('/employee/refresh-token', [EmployeeController::class, 'refreshToken']);
        Route::post('/employee/validate-token', [EmployeeController::class, 'validateToken']);
        
        // Attendance
        Route::post('/attendance/check-in', [EmployeeController::class, 'apiCheckIn']);
        Route::post('/attendance/check-out', [EmployeeController::class, 'apiCheckOut']);
        Route::post('/attendance/overtime', [EmployeeController::class, 'apiOvertime']);
        
        // Attendance history
        Route::get('/attendance/history', [EmployeeController::class, 'getAttendanceHistory']);
        Route::get('/attendance/today', [EmployeeController::class, 'getTodayAttendance']);
        Route::get('/attendance/summary', [EmployeeController::class, 'getAttendanceSummary']);
        
        // Location validation
        Route::post('/location/validate', [ApiController::class, 'validateLocation']);
        
        // Logout
        Route::post('/employee/logout', [EmployeeController::class, 'apiLogout']);
    });
    
    // Fallback route for API
    Route::fallback(function () {
        return response()->json([
            'success' => false,
            'message' => 'API endpoint not found',
            'error' => 'Not Found'
        ], 404);
    });
});

// Legacy routes for backward compatibility (without v1 prefix)
Route::post('/karyawan/login', [EmployeeController::class, 'login']);
Route::middleware('auth:karyawan')->group(function () {
    Route::post('/check-in', [EmployeeController::class, 'checkIn']);
    Route::post('/check-out', [EmployeeController::class, 'checkOut']);
});
