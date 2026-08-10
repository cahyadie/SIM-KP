<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\ListPerusahaanController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\MagangController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// -------------------------------------------------------------------------
// AUTHENTICATION
// -------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/auth/microsoft', [AuthController::class, 'redirectToProvider'])->name('auth.microsoft');
    Route::get('/auth/microsoft/callback', [AuthController::class, 'handleProviderCallback']);
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------------------------------------------------------------
// FITUR UMUM (Semua role yang sudah login)
// -------------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/lowongan', [PengumumanController::class, 'lowongan'])->name('lowongan.index');
    Route::get('/lowongan/{id}', [PengumumanController::class, 'showLowongan'])->name('lowongan.show');

    Route::get('/direktori-magang', [ListPerusahaanController::class, 'index'])->name('perusahaan.index');
    Route::get('/direktori-magang/{id}', [ListPerusahaanController::class, 'show'])->name('perusahaan.show');
    Route::post('/direktori-magang/{id}/review', [ListPerusahaanController::class, 'storeReview'])->name('perusahaan.review');

    // ADMIN
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        Route::get('/riwayat-magang', [AdminController::class, 'riwayatMagang'])->name('riwayat-magang.index');
        Route::get('/riwayat-magang/{id}', [AdminController::class, 'showValidasi'])->name('riwayat-magang.show');
        Route::get('/riwayat-magang/export/excel', [AdminController::class, 'exportRiwayatExcel'])->name('riwayat-magang.export.excel');
        Route::get('/riwayat-magang/export/pdf', [AdminController::class, 'exportRiwayatPdf'])->name('riwayat-magang.export.pdf');

        Route::get('/skp-list', [AdminController::class, 'skp'])->name('skp');
        Route::get('/skp-list/export/excel', [AdminController::class, 'exportSkpExcel'])->name('skp.export.excel');
        Route::get('/skp-list/export/pdf', [AdminController::class, 'exportSkpPdf'])->name('skp.export.pdf');
        Route::get('/skp/{id}', [AdminController::class, 'showSkp'])->name('skp.show');
        Route::patch('/skp/{id}', [AdminController::class, 'updateSkp'])->name('updateSkp');

        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::delete('/review/{id}', [ListPerusahaanController::class, 'destroyReview'])->name('review.destroy');
        Route::delete('/magang-data/{id}', [ListPerusahaanController::class, 'destroyMagang'])->name('magang.destroy');

        Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/create', [PengumumanController::class, 'create'])->name('pengumuman.create');
        Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::get('/pengumuman/{id}/edit', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
        Route::put('/pengumuman/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
        Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
    });

    // DOSEN
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DosenController::class, 'index'])->name('dashboard');
        Route::get('/bimbingan', [DosenController::class, 'bimbingan'])->name('bimbingan.index');
        Route::get('/bimbingan/{id}/detail', [DosenController::class, 'detail'])->name('bimbingan.detail');
        Route::get('/bimbingan/{id}/logbook', [DosenController::class, 'logbook'])->name('bimbingan.logbook');
        Route::get('/skp', [DosenController::class, 'skpIndex'])->name('skp.index');
        Route::get('/skp/{id}/respon', [DosenController::class, 'skpRespon'])->name('skp.respon');
        Route::post('/bimbingan/skp/{id}/approve', [DosenController::class, 'approveJadwalSkp'])->name('bimbingan.skp.approve');
        Route::post('/bimbingan/skp/{id}/reject', [DosenController::class, 'rejectJadwalSkp'])->name('bimbingan.skp.reject');
        Route::post('/bimbingan/logbook/{id}/review', [DosenController::class, 'reviewLogbook'])->name('bimbingan.logbook.review');

        Route::get('/riwayat-magang', [DosenController::class, 'riwayatMagang'])->name('riwayat-magang.index');
        Route::get('/riwayat-magang/{id}', [DosenController::class, 'showRiwayat'])->name('riwayat-magang.show');
    });

    // NOTIFIKASI (dosen & kaprodi)
    Route::middleware('role:dosen,kaprodi')->prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::post('/read-all', [NotifikasiController::class, 'readAll'])->name('read-all');
        Route::get('/', [NotifikasiController::class, 'index'])->name('index');
        Route::get('/{id}/go', [NotifikasiController::class, 'go'])->name('go');
        Route::post('/{id}/read', [NotifikasiController::class, 'read'])->name('read');
    });

    // KAPRODI
    Route::middleware('role:kaprodi')->prefix('kaprodi')->name('kaprodi.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kaprodi'])->name('dashboard');
        Route::get('/statistik/{kategori}', [DashboardController::class, 'statistikDetail'])->name('statistik.detail');
        Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring.index');
        Route::get('/monitoring/{id}', [AdminController::class, 'monitoringDetail'])->name('monitoring.show');

        Route::get('/riwayat-magang', [AdminController::class, 'riwayatMagang'])->name('riwayat-magang.index');
        Route::get('/riwayat-magang/{id}', [AdminController::class, 'showValidasi'])->name('riwayat-magang.show');

        Route::get('/skp-list', [AdminController::class, 'skp'])->name('skp');
        Route::get('/skp/{id}', [AdminController::class, 'showSkp'])->name('skp.show');
        Route::get('/pantauan-skp', [DashboardController::class, 'pantauanSkp'])->name('pantauan-skp');
        Route::get('/pantauan-skp/pdf', [DashboardController::class, 'exportPantauanPdf'])->name('pantauan-skp.pdf');
    });

    // MAHASISWA
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/seminar', [MahasiswaController::class, 'seminar'])->name('seminar');
        Route::post('/seminar', [MahasiswaController::class, 'seminarStore'])->name('seminar.store');
        Route::post('/seminar/ajukan-jadwal', [MahasiswaController::class, 'ajukanJadwal'])->name('seminar.ajukan_jadwal');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/daftar', [MagangController::class, 'create'])->name('magang.create');
        Route::post('/daftar', [MagangController::class, 'store'])->name('magang.store');

        Route::get('/riwayat-magang', [MahasiswaController::class, 'riwayatMagang'])->name('riwayat-magang.index');
        Route::get('/riwayat-magang/{id}/edit', [MahasiswaController::class, 'editMagang'])->name('riwayat-magang.edit');
        Route::put('/riwayat-magang/{id}', [MahasiswaController::class, 'updateMagang'])->name('riwayat-magang.update');

        Route::prefix('magang/{id}')->name('logbook.')->group(function () {
            Route::get('/logbook', [LogbookController::class, 'index'])->name('index');
            Route::get('/logbook/create', [LogbookController::class, 'create'])->name('create');
            Route::post('/logbook', [LogbookController::class, 'store'])->name('store');
        });
    });
});
