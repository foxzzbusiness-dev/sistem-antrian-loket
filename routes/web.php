<?php

use App\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;

Route::controller(PenggunaController::class)->group(function () {
    Route::get('/', 'beranda')->name('beranda');
    Route::get('/layanan', 'layanan')->name('layanan');
    Route::get('/panduan-antrean', 'panduan')->name('panduan');
    Route::post('/antrean', 'ambil')->name('antrean.ambil');
});