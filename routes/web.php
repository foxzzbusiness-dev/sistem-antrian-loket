<?php

use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PetugasAuthController;
use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Route;

// ---- Pengunjung (tanpa login) ----
Route::controller(PenggunaController::class)->group(function () {
    Route::get('/', 'beranda')->name('beranda');
    Route::get('/layanan', 'layanan')->name('layanan');
    Route::get('/panduan-antrean', 'panduan')->name('panduan');
    Route::post('/antrean', 'ambil')->name('antrean.ambil');
});

// ---- Petugas ----
Route::get('/petugas/login', [PetugasAuthController::class, 'form'])->name('login');
Route::post('/petugas/login', [PetugasAuthController::class, 'masuk'])
    ->middleware('throttle:5,1')->name('petugas.masuk');

Route::middleware('auth')->group(function () {
    Route::post('/petugas/keluar', [PetugasAuthController::class, 'keluar'])->name('petugas.keluar');

    Route::prefix('petugas')->name('petugas.')->controller(PetugasController::class)->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');
        Route::post('/antrean/panggil', 'panggil')->name('panggil');
        Route::post('/antrean/ulang', 'ulang')->name('ulang');
        Route::post('/antrean/selesai', 'selesai')->name('selesai');
        Route::post('/antrean/{antrean}/batal', 'batal')->name('batal');
    });
});