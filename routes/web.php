<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PoliController as AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\ExportController as DokterExportController;
use App\Http\Controllers\Pasien\PoliController as PasienPoliController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController;
use App\Http\Controllers\Pasien\RiwayatController;
use App\Http\Controllers\Dokter\RiwayatPasienController;
use App\Http\Controllers\Pasien\PembayaranController as PasienPembayaranController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Dokter\DashboardController as DokterDashboardController;


// ─── Auth ────────────────────────────────────────────────────────────────────
Route::get('/', fn() => view('auth.login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── ADMIN ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // CRUD
    Route::resource('polis',  AdminPoliController::class);
    Route::resource('dokter', DokterController::class);
    Route::resource('pasien', PasienController::class);
    Route::resource('obat',   ObatController::class);

    // ── Fitur 6: Verifikasi Bukti Pembayaran ──────────────────────────────
    Route::get('/pembayaran',           [AdminPembayaranController::class, 'index'])->name('admin.pembayaran.index');
    Route::post('/pembayaran/{id}/verify', [AdminPembayaranController::class, 'verify'])->name('admin.pembayaran.verify');

    // ── Fitur 5: Export Excel (Admin) ─────────────────────────────────────
    Route::get('/export/dokter', [AdminExportController::class, 'dokter'])->name('admin.export.dokter');
    Route::get('/export/pasien', [AdminExportController::class, 'pasien'])->name('admin.export.pasien');
    Route::get('/export/obat',   [AdminExportController::class, 'obat'])->name('admin.export.obat');
});

// ─── DOKTER ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {

    Route::get('/dashboard', [DokterDashboardController::class, 'index'])->name('dokter.dashboard');

    // Jadwal Periksa
    Route::resource('jadwal-periksa', JadwalPeriksaController::class);

    // Fitur 2: Periksa Pasien
    Route::get('/periksa', [PeriksaPasienController::class, 'index'])->name('periksa-pasien.index');
    Route::get('/periksa/create/{id}', [PeriksaPasienController::class, 'create'])->name('periksa-pasien.create');
    Route::post('/periksa', [PeriksaPasienController::class, 'store'])->name('periksa-pasien.store');

    // Fitur 3: Riwayat Pasien
    Route::get('/riwayat-pasien', [RiwayatPasienController::class, 'index'])->name('dokter.riwayat');
    Route::get('/riwayat-pasien/{id}', [RiwayatPasienController::class, 'show'])->name('dokter.riwayat.show');

    // Fitur 5: Export Excel
    Route::get('/export/jadwal', [DokterExportController::class, 'jadwal'])->name('dokter.export.jadwal');
    Route::get('/export/riwayat-pasien', [DokterExportController::class, 'riwayatPasien'])->name('dokter.export.riwayat');
});

// ─── PASIEN ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {

    // ── Fitur 1: Dashboard Pasien (antrian real-time) ─────────────────────
    Route::get('/dashboard', [PasienDashboardController::class, 'index'])->name('pasien.dashboard');

    // API endpoint polling antrian (dipanggil JS setiap 5 detik)
    Route::get('/antrian-live', [PasienDashboardController::class, 'getAntrianLive'])->name('pasien.antrian.live');

    // Pendaftaran Poli
    Route::get('/daftar',  [PasienPoliController::class, 'get'])->name('pasien.daftar');
    Route::post('/daftar', [PasienPoliController::class, 'submit'])->name('pasien.daftar.submit');

    // ── Fitur 3: Riwayat Pendaftaran Poli ────────────────────────────────
    Route::get('/riwayat',      [RiwayatController::class, 'index'])->name('pasien.riwayat.index');
    Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('pasien.riwayat.show');

    // ── Fitur 6: Upload Bukti Pembayaran ──────────────────────────────────
    Route::get('/pembayaran',                          [PasienPembayaranController::class, 'index'])->name('pasien.pembayaran.index');
    Route::post('/pembayaran/{id_periksa}/upload',     [PasienPembayaranController::class, 'upload'])->name('pasien.pembayaran.upload');
});
