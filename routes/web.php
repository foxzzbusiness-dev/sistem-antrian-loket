<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Route;

// ---- Pengunjung (tanpa login) ----
Route::controller(PenggunaController::class)->group(function () {
    Route::get('/', 'beranda')->name('beranda');
    Route::post('/antrean/{layanan}', 'ambil')->name('antrean.ambil');
    Route::get('/tiket/{antrean}', 'cetak')->name('tiket.cetak');
});

// ---- Display informasi loket (layar TV) ----
Route::view('/call', 'pengguna.call')->name('call');

// ---- Login (dipisah per peran) ----
Route::controller(AuthController::class)->group(function () {
    Route::get('/petugas/login', 'formPetugas')->name('login');
    Route::post('/petugas/login', 'masukPetugas')->middleware('throttle:5,1')->name('petugas.masuk');

    Route::get('/admin/login', 'formAdmin')->name('admin.login');
    Route::post('/admin/login', 'masukAdmin')->middleware('throttle:5,1')->name('admin.masuk');

    Route::post('/keluar', 'keluar')->middleware('auth')->name('keluar');
});

// ---- Portal petugas (hanya role petugas) ----
Route::middleware('peran:petugas')->prefix('petugas')->name('petugas.')
    ->controller(PetugasController::class)->group(function () {
        Route::redirect('/', '/petugas/portal');
        Route::get('/portal', 'portal')->name('portal');
        Route::post('/antrean/panggil', 'panggil')->name('panggil');
        Route::post('/antrean/ulang', 'ulang')->name('ulang');
        Route::post('/antrean/selesai', 'selesai')->name('selesai');
        Route::post('/antrean/{antrean}/batal', 'batal')->name('batal');
    });

// ---- Dashboard admin (hanya role admin) ----
// ---- Dashboard admin (hanya role admin) ----
Route::middleware('peran:admin')->prefix('admin')->name('admin.')
    ->controller(AdminController::class)->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');
        Route::post('/tiket/{antrean}/batal', 'batalTiket')->name('tiket.batal');

        Route::get('/petugas', 'petugas')->name('petugas');
        Route::post('/petugas', 'simpan')->name('petugas.simpan');
        Route::put('/petugas/{user}', 'ubah')->name('petugas.ubah');
        Route::post('/petugas/{user}/status', 'status')->name('petugas.status');
        Route::delete('/petugas/{user}', 'hapus')->name('petugas.hapus');
    });