<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('dashboard');
});

Route::get('/kelola-karyawan', function () {
    return view('kelola-karyawan');
});

Route::get('/data-absensi', function () {
    return view('data-absensi');
});
Route::get('/laporan', function () {
    return view('laporan');
});
Route::get('/pengaturan', function () {
    return view('pengaturan');
});

