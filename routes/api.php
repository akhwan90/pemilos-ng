<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\DataSekolahController;
use App\Http\Controllers\Api\Admin\UserSekolahController;
use App\Http\Controllers\Api\Admin\JadwalSekolahController;
use App\Http\Controllers\Api\Admin\KandidatSekolahController;
use App\Http\Controllers\Api\Admin\TpsSekolahController;
use App\Http\Controllers\Api\AdminSekolah\IdentitasSekolahController;
use App\Http\Controllers\Api\AdminSekolah\DataSiswaController;
use App\Http\Controllers\Api\AdminSekolah\UploadSiswaController;
use App\Http\Controllers\Api\AdminSekolah\KandidatController;

// ========== PUBLIC API (No Auth Required) ==========

// Auth (login admin/sekolah)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

// ========== ADMIN API (Sanctum Auth Required) ==========
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Admin & Sekolah API
    Route::get('/admin/data-sekolah', [DataSekolahController::class, 'index']);
    Route::post('/admin/data-sekolah', [DataSekolahController::class, 'store']);
    Route::get('/admin/data-sekolah/{npsn}', [DataSekolahController::class, 'show']);
    Route::post('/admin/data-sekolah/{npsn}', [DataSekolahController::class, 'update']); // Pakai POST agar bisa upload logo
    
    // User Sekolah (Level 2) API
    Route::get('/admin/data-sekolah/{npsn}/users', [UserSekolahController::class, 'index']);
    Route::post('/admin/data-sekolah/{npsn}/users', [UserSekolahController::class, 'store']);
    Route::put('/admin/data-sekolah/{npsn}/users/{id}', [UserSekolahController::class, 'update']);
    Route::delete('/admin/data-sekolah/{npsn}/users/{id}', [UserSekolahController::class, 'destroy']);

    // Jadwal Sekolah API
    Route::get('/admin/data-sekolah/{npsn}/jadwal', [JadwalSekolahController::class, 'index']);
    Route::post('/admin/data-sekolah/{npsn}/jadwal', [JadwalSekolahController::class, 'store']);

    // Kandidat Sekolah API
    Route::get('/admin/data-sekolah/{npsn}/kandidat', [KandidatSekolahController::class, 'index']);
    Route::get('/admin/data-sekolah/{npsn}/kandidat/{id}', [KandidatSekolahController::class, 'show']);
    Route::post('/admin/data-sekolah/{npsn}/kandidat/{id}', [KandidatSekolahController::class, 'update']);
    Route::delete('/admin/data-sekolah/{npsn}/kandidat/{id}', [KandidatSekolahController::class, 'destroy']);

    // TPS / Kelas Sekolah API
    Route::get('/admin/data-sekolah/{npsn}/tps', [TpsSekolahController::class, 'index']);
    Route::post('/admin/data-sekolah/{npsn}/tps', [TpsSekolahController::class, 'store']);
    Route::put('/admin/data-sekolah/{npsn}/tps/{kd_kelas}', [TpsSekolahController::class, 'update']);
    Route::delete('/admin/data-sekolah/{npsn}/tps/{kd_kelas}', [TpsSekolahController::class, 'destroy']);

    // ==========================================
    // LEVEL 2: ADMIN SEKOLAH API
    // ==========================================
    Route::get('/admin-sekolah/identitas', [IdentitasSekolahController::class, 'show']);
    Route::post('/admin-sekolah/identitas', [IdentitasSekolahController::class, 'update']);

    // Data Siswa API
    Route::get('/admin-sekolah/siswa', [DataSiswaController::class, 'index']);
    Route::get('/admin-sekolah/siswa/kelas', [DataSiswaController::class, 'listKelas']);
    Route::post('/admin-sekolah/siswa', [DataSiswaController::class, 'store']);
    Route::post('/admin-sekolah/siswa/bulk-delete', [DataSiswaController::class, 'bulkDestroy']);
    Route::put('/admin-sekolah/siswa/{id}', [DataSiswaController::class, 'update']);
    Route::post('/admin-sekolah/siswa/{id}/delete', [DataSiswaController::class, 'destroy']);

    // Upload Siswa API
    Route::get('/admin-sekolah/upload-siswa', [UploadSiswaController::class, 'history']);
    Route::post('/admin-sekolah/upload-siswa', [UploadSiswaController::class, 'upload']);
    Route::get('/admin-sekolah/upload-siswa/{id}/logs', [UploadSiswaController::class, 'logs']);

    // Data TPS / Kelas API (Admin Sekolah share logic with Super Admin TpsSekolahController)
    Route::get('/admin-sekolah/tps', [TpsSekolahController::class, 'index']);
    Route::post('/admin-sekolah/tps', [TpsSekolahController::class, 'store']);
    Route::put('/admin-sekolah/tps/{kd_kelas}', [TpsSekolahController::class, 'update']);
    Route::delete('/admin-sekolah/tps/{kd_kelas}', [TpsSekolahController::class, 'destroy']);

    // Data Kandidat Calon (Admin Sekolah khusus CRUD Kandidat Sendiri)
    Route::get('/admin-sekolah/kandidat', [KandidatController::class, 'index']);
    Route::get('/admin-sekolah/kandidat/{id}', [KandidatController::class, 'show']);
    Route::post('/admin-sekolah/kandidat', [KandidatController::class, 'store']); // Create
    Route::post('/admin-sekolah/kandidat/{id}', [KandidatController::class, 'update']); // Update
    Route::delete('/admin-sekolah/kandidat/{id}', [KandidatController::class, 'destroy']);
});
