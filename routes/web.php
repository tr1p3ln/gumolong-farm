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
use App\Http\Controllers\TugasHarianController;
use App\Http\Controllers\TemplateTugasController;
use App\Http\Controllers\KandangController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Mobile\MobilePKController;
use App\Http\Controllers\Mobile\MobileKKController;
use Illuminate\Support\Facades\Route;

// ── Root: redirect authenticated users to the right home ────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'pengurus_kandang') return redirect()->route('pk.dashboard');
        if ($role === 'kepala_kandang')   return redirect()->route('kk.dashboard');
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ════════════════════════════════════════════════════════════════════════════
//  AUTHENTICATED + VERIFIED — shared base middleware
// ════════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Profile (all roles) ──────────────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ════════════════════════════════════════════════════════════════════════
    //  MOBILE — Pengurus Kandang (PK) routes
    // ════════════════════════════════════════════════════════════════════════
    Route::middleware('role:pengurus_kandang')->prefix('pk')->name('pk.')->group(function () {
        Route::get('/dashboard',  [MobilePKController::class, 'dashboard'])->name('dashboard');
        Route::get('/tugas',      [MobilePKController::class, 'tugas'])->name('tugas');
        Route::get('/timbangan',  [MobilePKController::class, 'timbangan'])->name('timbangan');
        Route::post('/timbangan', [MobilePKController::class, 'storeTimbangan'])->name('timbangan.store');
        Route::get('/kesehatan',  [MobilePKController::class, 'kesehatan'])->name('kesehatan');
        Route::post('/kesehatan', [MobilePKController::class, 'storeKesehatan'])->name('kesehatan.store');
        Route::get('/kelahiran',  [MobilePKController::class, 'kelahiran'])->name('kelahiran');
        Route::post('/kelahiran', [MobilePKController::class, 'storeKelahiran'])->name('kelahiran.store');
    });

    // ════════════════════════════════════════════════════════════════════════
    //  MOBILE — Kepala Kandang (KK) routes
    // ════════════════════════════════════════════════════════════════════════
    Route::middleware('role:kepala_kandang')->prefix('kk')->name('kk.')->group(function () {
        Route::get('/dashboard',           [MobileKKController::class, 'dashboard'])->name('dashboard');
        Route::get('/monitor-tugas',       [MobileKKController::class, 'monitorTugas'])->name('monitor-tugas');
        Route::get('/kesehatan',           [MobileKKController::class, 'kesehatan'])->name('kesehatan');
        Route::post('/kesehatan/{id}',     [MobileKKController::class, 'konfirmasiKesehatan'])->name('kesehatan.konfirmasi');
        Route::get('/reproduksi',          [MobileKKController::class, 'reproduksi'])->name('reproduksi');
        Route::post('/reproduksi/{id}',    [MobileKKController::class, 'konfirmasiKebuntingan'])->name('reproduksi.konfirmasi');
        Route::get('/validasi-timbangan',  [MobileKKController::class, 'validasiTimbangan'])->name('validasi-timbangan');
        Route::post('/validasi-timbangan/{id}', [MobileKKController::class, 'processValidasi'])->name('validasi-timbangan.process');
    });

    // Shared PATCH for task status (used by PK and KK task views)
    Route::patch('/tugas-harian/{id}/status', [TugasHarianController::class, 'updateStatus'])
        ->name('tugas-harian.update-status');

    // Legacy mobile route (kept for backward compat)
    Route::get('/tugas-harian/mobile', [TugasHarianController::class, 'mobile'])
        ->name('tugas-harian.mobile');

    // ════════════════════════════════════════════════════════════════════════
    //  WEB — Super Admin, Admin, Kepala Kandang
    //  Pengurus Kandang: No Access (middleware redirects to mobile)
    // ════════════════════════════════════════════════════════════════════════
    Route::middleware('role:super_admin,admin,kepala_kandang')->group(function () {

        // Dashboard (Kepala Kandang → Limited view, handled in blade)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export-pdf')->middleware('role:super_admin,admin');

        // ── Data Domba ───────────────────────────────────────────────────────
        // READ: all web roles
        Route::prefix('domba')->group(function () {
            Route::get('/generate-ear-tag', [DombaController::class, 'generateEarTag'])
                ->name('domba.generate-ear-tag')
                ->middleware('role:super_admin,admin,kepala_kandang');

            Route::get('/',           [DombaController::class, 'index'])->name('domba.index');
            Route::get('/{earTagId}', [DombaController::class, 'show'])->name('domba.show');

            // WRITE: Super Admin, Admin, Kepala Kandang only (Pengurus = View Only)
            Route::middleware('role:super_admin,admin,kepala_kandang')->group(function () {
                Route::post('/',             [DombaController::class, 'store'])->name('domba.store');
                Route::put('/{earTagId}',    [DombaController::class, 'update'])->name('domba.update');
                Route::delete('/{earTagId}', [DombaController::class, 'destroy'])->name('domba.destroy');
            });
        });

        // ── Inventaris ───────────────────────────────────────────────────────
        Route::get('/obat-vaksin/search-rekam',     [ObatVaksinController::class, 'searchRekam'])->name('obat-vaksin.searchRekam');
        Route::post('/obat-vaksin/pemakaian',       [ObatVaksinController::class, 'storePemakaian'])->name('obat-vaksin.storePemakaian');
        Route::resource('stok-pakan',       StokPakanController::class);
        Route::resource('obat-vaksin',      ObatVaksinController::class);
        

        // ── Monitoring ───────────────────────────────────────────────────────
        Route::resource('pertumbuhan',      PertumbuhanController::class);
        Route::post('/kesehatan/vaksinasi',                                      [KesehatanController::class, 'storeVaksinasi'])->name('kesehatan.vaksinasi.store');
        Route::put('/kesehatan/vaksinasi/{id}',                                  [KesehatanController::class, 'updateVaksinasi'])->name('kesehatan.vaksinasi.update');
        Route::delete('/kesehatan/vaksinasi/{id}',                               [KesehatanController::class, 'destroyVaksinasi'])->name('kesehatan.vaksinasi.destroy');
        Route::patch('/kesehatan/karantina/{earTagId}/pindah',                   [KesehatanController::class, 'pindahKandang'])->name('kesehatan.karantina.pindah');
        Route::delete('/kesehatan/{rekamId}/obat/{pakaiId}',                     [KesehatanController::class, 'destroyPemakaianObat'])->name('kesehatan.obat.destroy');
        Route::resource('kesehatan',        KesehatanController::class)->except(['create', 'edit', 'show']);
        Route::resource('pakan-individual', PakanIndividualController::class);

        // ── Reproduksi ───────────────────────────────────────────────────────
        Route::post('/reproduksi/{id}/konfirmasi',                    [ReproduksiController::class, 'konfirmasi'])->name('reproduksi.konfirmasi');
        Route::post('/reproduksi/{kawin_id}/kelahiran',               [ReproduksiController::class, 'storeKelahiran'])->name('reproduksi.kelahiran.store');
        Route::put('/reproduksi/{kawin_id}/kelahiran/{lahir_id}',     [ReproduksiController::class, 'updateKelahiran'])->name('reproduksi.kelahiran.update');
        Route::delete('/reproduksi/{kawin_id}/kelahiran/{lahir_id}',  [ReproduksiController::class, 'destroyKelahiran'])->name('reproduksi.kelahiran.destroy');
        Route::resource('reproduksi',       ReproduksiController::class)->except(['create', 'edit', 'show']);

        // ── Silsilah: Super Admin, Admin, Kepala Kandang (Pengurus = No Access) ─
        Route::get('/silsilah',                        [SilsilahController::class, 'index'])->name('silsilah.index');
        Route::get('/silsilah/rekomendasi-pejantan',   [SilsilahController::class, 'rekomendasiPejantan'])->name('silsilah.rekomendasi-pejantan');
        Route::get('/silsilah/{earTagId}',             [SilsilahController::class, 'show'])->name('silsilah.show');

        // Inbreeding check & pedigree write: Super Admin & Admin only
        Route::post('/silsilah/cek-inbreeding', [SilsilahController::class, 'cekInbreeding'])
            ->name('silsilah.cek-inbreeding')
            ->middleware('role:super_admin,admin');

        // ── Tugas Harian (web) ───────────────────────────────────────────────
        Route::get('/tugas-harian',                      [TugasHarianController::class, 'index'])->name('tugas-harian.index');
        Route::get('/tugas-harian/{id}',                 [TugasHarianController::class, 'show'])->name('tugas-harian.show');

        // Assign & manage tasks: Super Admin, Admin, Kepala Kandang
        Route::post('/tugas-harian',                     [TugasHarianController::class, 'store'])->name('tugas-harian.store');
        Route::put('/tugas-harian/{id}',                 [TugasHarianController::class, 'update'])->name('tugas-harian.update');
        Route::delete('/tugas-harian/{id}',              [TugasHarianController::class, 'destroy'])->name('tugas-harian.destroy');

        // Bulk reassign: untuk menangani petugas absen/sakit
        Route::post('/tugas-harian/bulk-reassign',       [TugasHarianController::class, 'bulkReassign'])->name('tugas-harian.bulk-reassign');

        // Template tugas rutin + generate: Super Admin & Admin only
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::post('/tugas-harian/generate-rutin',  [TugasHarianController::class, 'generateRutin'])->name('tugas-harian.generate-rutin');
            Route::get('/template-tugas',                [TemplateTugasController::class, 'index'])->name('template-tugas.index');
            Route::post('/template-tugas',               [TemplateTugasController::class, 'store'])->name('template-tugas.store');
            Route::put('/template-tugas/{id}',           [TemplateTugasController::class, 'update'])->name('template-tugas.update');
            Route::patch('/template-tugas/{id}/toggle',  [TemplateTugasController::class, 'toggle'])->name('template-tugas.toggle');
            Route::delete('/template-tugas/{id}',        [TemplateTugasController::class, 'destroy'])->name('template-tugas.destroy');
        });

        // ── Notifikasi ───────────────────────────────────────────────────────
        Route::resource('notifikasi', NotifikasiController::class);

        // ── Stok Pakan: pencatatan masuk/keluar manual (non-pemberian) ─────────
        Route::prefix('stok-pakan')->name('stok-pakan.')->group(function () {
            Route::get('/', [StokPakanController::class, 'index'])
                ->name('index');
            Route::post('/masuk', [StokPakanController::class, 'catatMasuk'])
                ->name('masuk');
            Route::post('keluar', [StokPakanController::class, 'catatKeluar'])
                ->name('keluar');
        });

        // ── Account Management: Super Admin & Admin only ─────────────────────
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::resource('users', UserController::class);
            Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

            // ── Manajemen Kandang ─────────────────────────────────────────────
            Route::get('/kandang',          [KandangController::class, 'index'])->name('kandang.index');
            Route::post('/kandang',         [KandangController::class, 'store'])->name('kandang.store');
            Route::get('/kandang/{id}',     [KandangController::class, 'show'])->name('kandang.show');
            Route::put('/kandang/{id}',     [KandangController::class, 'update'])->name('kandang.update');
            Route::delete('/kandang/{id}',  [KandangController::class, 'destroy'])->name('kandang.destroy');
        });
    });
});

require __DIR__.'/auth.php';
