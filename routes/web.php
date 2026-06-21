<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MagangController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ListPerusahaanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PengumumanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. HALAMAN DEPAN
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. AUTHENTICATION (Guest)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/auth/microsoft', [AuthController::class, 'redirectToProvider'])->name('auth.microsoft');
    Route::get('/auth/microsoft/callback', [AuthController::class, 'handleProviderCallback']);
});

// 3. LOGOUT
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');


// 4. ROUTE SETELAH LOGIN (Auth)
Route::middleware(['auth'])->group(function () {

    // --- A. DASHBOARD PENGARAH UMUM ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- B. FITUR UMUM ---
    Route::get('/lowongan', [PengumumanController::class, 'lowongan'])->name('lowongan.index');
    Route::get('/direktori-magang', [ListPerusahaanController::class, 'index'])->name('perusahaan.index');
    Route::get('/direktori-magang/{id}', [ListPerusahaanController::class, 'show'])->name('perusahaan.show');
    Route::post('/direktori-magang/{id}/review', [ListPerusahaanController::class, 'storeReview'])->name('perusahaan.review');
    Route::get('/lowongan/{id}', [PengumumanController::class, 'showLowongan'])->name('lowongan.show');

    // --- C. GROUP ADMIN ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // ROUTE BARU: Riwayat Magang (Gabungan)
        Route::get('/riwayat-magang', [AdminController::class, 'riwayatMagang'])->name('riwayat-magang.index');
        // PERBAIKAN: Ubah nama route dari riwayat.show menjadi riwayat-magang.show
        Route::get('/riwayat-magang/{id}', [AdminController::class, 'showValidasi'])->name('riwayat-magang.show');

        Route::get('/riwayat-magang/export/excel', [AdminController::class, 'exportRiwayatExcel'])->name('riwayat-magang.export.excel');
        Route::get('/riwayat-magang/export/pdf', [AdminController::class, 'exportRiwayatPdf'])->name('riwayat-magang.export.pdf');

        // Data SKP Tetap Terpisah
        Route::get('/skp-list', [AdminController::class, 'skp'])->name('skp');
        Route::get('/skp/{id}', [AdminController::class, 'showSkp'])->name('skp.show');
        Route::patch('/skp/{id}', [AdminController::class, 'updateSkp'])->name('updateSkp');
        Route::get('/skp-list/export/excel', [AdminController::class, 'exportSkpExcel'])->name('skp.export.excel');
        Route::get('/skp-list/export/pdf', [AdminController::class, 'exportSkpPdf'])->name('skp.export.pdf');

        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::delete('/review/{id}', [ListPerusahaanController::class, 'destroyReview'])->name('review.destroy');
        Route::delete('/magang-data/{id}', [ListPerusahaanController::class, 'destroyMagang'])->name('magang.destroy');

        // Manajemen Pengumuman & Lowongan
        Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/create', [PengumumanController::class, 'create'])->name('pengumuman.create');
        Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
        Route::get('/pengumuman/{id}/edit', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
        Route::put('/pengumuman/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
    });

    // --- D. GROUP DOSEN ---
    Route::middleware(['role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DosenController::class, 'index'])->name('dashboard');
        Route::get('/bimbingan', [DosenController::class, 'bimbingan'])->name('bimbingan.index');
        Route::get('/bimbingan/{id}/detail', [DosenController::class, 'detail'])->name('bimbingan.detail');
        Route::get('/bimbingan/{id}/logbook', [DosenController::class, 'logbook'])->name('bimbingan.logbook');
        Route::get('/skp', [DosenController::class, 'skpIndex'])->name('skp.index');
        Route::get('/skp/{id}/respon', [DosenController::class, 'skpRespon'])->name('skp.respon');

        // ROUTE BARU: Riwayat Magang (Gabungan Khusus Dosen)
        Route::get('/riwayat-magang', [DosenController::class, 'riwayatMagang'])->name('riwayat-magang.index');
        // PERBAIKAN: Tambahkan route detail riwayat untuk Dosen
        Route::get('/riwayat-magang/{id}', [DosenController::class, 'showRiwayat'])->name('riwayat-magang.show');

        Route::post('/bimbingan/skp/{id}/approve', [DosenController::class, 'approveJadwalSkp'])->name('bimbingan.skp.approve');
        Route::post('/bimbingan/skp/{id}/reject', [DosenController::class, 'rejectJadwalSkp'])->name('bimbingan.skp.reject');
        Route::post('/bimbingan/logbook/{id}/review', [DosenController::class, 'reviewLogbook'])->name('bimbingan.logbook.review');
    });

    // --- E. GROUP KAPRODI ---
    Route::middleware(['role:kaprodi'])->prefix('kaprodi')->name('kaprodi.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'kaprodi'])->name('dashboard');
        Route::get('/statistik/{kategori}', [DashboardController::class, 'statistikDetail'])->name('statistik.detail');
        Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring.index');
        Route::get('/monitoring/{id}', [AdminController::class, 'monitoringDetail'])->name('monitoring.show');

        // ROUTE BARU: Riwayat Magang (Gabungan Semua Data untuk Kaprodi, memanggil method di AdminController)
        Route::get('/riwayat-magang', [AdminController::class, 'riwayatMagang'])->name('riwayat-magang.index');
        // PERBAIKAN: Tambahkan route detail riwayat untuk Kaprodi
        Route::get('/riwayat-magang/{id}', [AdminController::class, 'showValidasi'])->name('riwayat-magang.show');

        Route::get('/skp-list', [AdminController::class, 'skp'])->name('skp');
        Route::get('/skp/{id}', [AdminController::class, 'showSkp'])->name('skp.show');
        Route::get('/pantauan-skp', [DashboardController::class, 'pantauanSkp'])->name('pantauan-skp');

        Route::get('/pantauan-skp/pdf', [DashboardController::class, 'exportPantauanPdf'])->name('pantauan-skp.pdf');
    });

    // --- F. GROUP MAHASISWA ---
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
        Route::get('/seminar', [MahasiswaController::class, 'seminar'])->name('mahasiswa.seminar');
        Route::post('/seminar', [MahasiswaController::class, 'seminarStore'])->name('mahasiswa.seminar.store');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/daftar', [MagangController::class, 'create'])->name('magang.create');
        Route::post('/daftar', [MagangController::class, 'store'])->name('magang.store');
        Route::prefix('magang/{id}')->name('logbook.')->group(function () {
            Route::get('/logbook', [LogbookController::class, 'index'])->name('index');
            Route::get('/logbook/create', [LogbookController::class, 'create'])->name('create');
            Route::post('/logbook', [LogbookController::class, 'store'])->name('store');
        });
        Route::post('/mahasiswa/seminar/ajukan-jadwal', [MahasiswaController::class, 'ajukanJadwal'])->name('mahasiswa.seminar.ajukan_jadwal');

        // Riwayat Magang Mahasiswa
        Route::get('/riwayat-magang', [MahasiswaController::class, 'riwayatMagang'])->name('mahasiswa.riwayat-magang.index');
        Route::get('/riwayat-magang/{id}/edit', [MahasiswaController::class, 'editMagang'])->name('mahasiswa.riwayat-magang.edit');
        Route::put('/riwayat-magang/{id}', [MahasiswaController::class, 'updateMagang'])->name('mahasiswa.riwayat-magang.update');
    });
});