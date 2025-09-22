<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/kelola-karyawan', function () {
    return view('kelola-karyawan');
});

Route::get('/data-absensi', function () {
    return view('data-absensi');
});
