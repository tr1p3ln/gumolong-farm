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
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('domba', DombaController::class);
    Route::resource('stok-pakan', StokPakanController::class);
    Route::resource('obat-vaksin', ObatVaksinController::class);
    Route::resource('pertumbuhan', PertumbuhanController::class);
    Route::resource('kesehatan', KesehatanController::class);
    Route::resource('pakan-individual', PakanIndividualController::class);
    Route::resource('reproduksi', ReproduksiController::class);
    Route::resource('silsilah', SilsilahController::class);
    Route::resource('daily-task', DailyTaskController::class);
    Route::resource('notifikasi', NotifikasiController::class);
    Route::resource('users', UserController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
