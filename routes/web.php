<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DombaController;
use App\Http\Controllers\StokPakanController;
use App\Http\Controllers\ObatVaksinController;
use App\Http\Controllers\PertumbuhanController;
use App\Http\Controllers\KesehatanController;
use App\Http\Controllers\PakanIndividualController;
use App\Http\Controllers\ReproduksiController;
use App\Http\Controllers\SilsilahController;
use App\Http\Controllers\DailyTaskController;
use App\Http\Controllers\TugasHarianController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('domba')->group(function () {
        // PENTING: route statis harus sebelum route dinamis {earTagId}
        Route::get('/generate-ear-tag', [DombaController::class, 'generateEarTag'])
             ->name('domba.generate-ear-tag');

        Route::get('/',              [DombaController::class, 'index'])->name('domba.index');
        Route::post('/',             [DombaController::class, 'store'])->name('domba.store');
        Route::get('/{earTagId}',    [DombaController::class, 'show'])->name('domba.show');
        Route::put('/{earTagId}',    [DombaController::class, 'update'])->name('domba.update');
        Route::delete('/{earTagId}', [DombaController::class, 'destroy'])->name('domba.destroy');
    });

    Route::resource('stok-pakan', StokPakanController::class);
    Route::resource('obat-vaksin', ObatVaksinController::class);
    Route::resource('pertumbuhan', PertumbuhanController::class);
    Route::resource('kesehatan', KesehatanController::class);
    Route::resource('pakan-individual', PakanIndividualController::class);
    Route::resource('reproduksi', ReproduksiController::class);
    // MODUL SILSILAH (A-10) — urutan penting: statis sebelum {earTagId}
    Route::get('/silsilah', [SilsilahController::class, 'index'])->name('silsilah.index');
    Route::post('/silsilah/cek-inbreeding', [SilsilahController::class, 'cekInbreeding'])->name('silsilah.cek-inbreeding');
    Route::get('/silsilah/rekomendasi-pejantan', [SilsilahController::class, 'rekomendasiPejantan'])->name('silsilah.rekomendasi-pejantan');
    Route::get('/silsilah/{earTagId}', [SilsilahController::class, 'show'])->name('silsilah.show');
    // MODUL DAILY TASK (A-11 Web + M-03 Mobile) — urutan: statis sebelum {id}
    Route::get('/tugas-harian',                  [TugasHarianController::class, 'index'])->name('tugas-harian.index');
    Route::post('/tugas-harian',                 [TugasHarianController::class, 'store'])->name('tugas-harian.store');
    Route::get('/tugas-harian/mobile',           [TugasHarianController::class, 'mobile'])->name('tugas-harian.mobile');
    Route::get('/tugas-harian/{id}',             [TugasHarianController::class, 'show'])->name('tugas-harian.show');
    Route::put('/tugas-harian/{id}',             [TugasHarianController::class, 'update'])->name('tugas-harian.update');
    Route::patch('/tugas-harian/{id}/status',    [TugasHarianController::class, 'updateStatus'])->name('tugas-harian.update-status');
    Route::delete('/tugas-harian/{id}',          [TugasHarianController::class, 'destroy'])->name('tugas-harian.destroy');
    Route::resource('notifikasi', NotifikasiController::class);
    Route::resource('users', UserController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
