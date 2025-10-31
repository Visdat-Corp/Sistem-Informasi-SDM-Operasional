<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminController::class, 'login']);
Route::post('/logout', [AdminController::class, 'logout']);

Route::middleware('auth:admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);

    Route::get('/kelola-karyawan', [AdminController::class, 'indexKaryawan'])->name('kelola-karyawan');
    Route::post('/kelola-karyawan', [AdminController::class, 'storeKaryawan'])->name('store-karyawan');
    Route::put('/kelola-karyawan/{id}', [AdminController::class, 'updateKaryawan'])->name('update-karyawan');
    Route::delete('/kelola-karyawan/{id}', [AdminController::class, 'destroyKaryawan'])->name('destroy-karyawan');

    Route::get('/kelola-departemen', [AdminController::class, 'indexDepartemen'])->name('kelola-departemen');
    Route::post('/kelola-departemen', [AdminController::class, 'storeDepartemen'])->name('store-departemen');
    Route::put('/kelola-departemen/{id}', [AdminController::class, 'updateDepartemen'])->name('update-departemen');
    Route::delete('/kelola-departemen/{id}', [AdminController::class, 'destroyDepartemen'])->name('destroy-departemen');

    Route::get('/kelola-posisi', [AdminController::class, 'indexPosisi'])->name('kelola-posisi');
    Route::post('/kelola-posisi', [AdminController::class, 'storePosisi'])->name('store-posisi');
    Route::put('/kelola-posisi/{id}', [AdminController::class, 'updatePosisi'])->name('update-posisi');
    Route::delete('/kelola-posisi/{id}', [AdminController::class, 'destroyPosisi'])->name('destroy-posisi');

    Route::get('/get-posisi/{id_departemen}', [AdminController::class, 'getPosisiByDepartemen'])->name('get-posisi');

    Route::get('/kelola-lokasi', [AdminController::class, 'indexLokasi'])->name('kelola-lokasi');
    Route::post('/kelola-lokasi', [AdminController::class, 'storeLokasi'])->name('store-lokasi');
    Route::put('/kelola-lokasi/{id}', [AdminController::class, 'updateLokasi'])->name('update-lokasi');
    Route::delete('/kelola-lokasi/{id}', [AdminController::class, 'destroyLokasi'])->name('destroy-lokasi');

    Route::get('/kelola-jadwal-pengecualian', [AdminController::class, 'indexJadwalPengecualian'])->name('kelola-jadwal-pengecualian');
    Route::post('/kelola-jadwal-pengecualian', [AdminController::class, 'storeJadwalPengecualian'])->name('store-jadwal-pengecualian');
    Route::put('/kelola-jadwal-pengecualian/{id}', [AdminController::class, 'updateJadwalPengecualian'])->name('update-jadwal-pengecualian');
    Route::delete('/kelola-jadwal-pengecualian/{id}', [AdminController::class, 'destroyJadwalPengecualian'])->name('destroy-jadwal-pengecualian');

    Route::get('/data-absensi', [AdminController::class, 'indexAbsensi'])->name('data-absensi');
    Route::put('/data-absensi/{id_absensi}/status', [AdminController::class, 'updateAbsensiStatus'])->name('update-absensi-status');
    Route::post('/data-absensi/{id}/approve-override', [AdminController::class, 'approveOverrideRequest'])->name('approve-override');
    Route::post('/data-absensi/{id}/reject-override', [AdminController::class, 'rejectOverrideRequest'])->name('reject-override');
    
    Route::get('/override-requests', [AdminController::class, 'indexOverrideRequests'])->name('override-requests');
    
    Route::get('/laporan', [AdminController::class, 'indexLaporan'])->name('laporan');
    Route::post('/laporan/update-status', [AdminController::class, 'updateLaporanStatus'])->name('laporan.update-status');
    Route::get('/laporan/export', [AdminController::class, 'exportLaporan'])->name('laporan.export');
    Route::get('/laporan/export-lembur', [AdminController::class, 'exportLaporanLembur'])->name('laporan.export-lembur');
    Route::get('/pengaturan', [AdminController::class, 'indexPengaturan'])->name('pengaturan');
    Route::post('/pengaturan', [AdminController::class, 'updatePengaturan'])->name('update-pengaturan');
});



