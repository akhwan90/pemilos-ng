<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\DataSekolahController;
use App\Http\Controllers\Api\Admin\UserSekolahController;
use App\Http\Controllers\Api\Admin\JadwalSekolahController;
use App\Http\Controllers\Api\Admin\KandidatSekolahController;
use App\Http\Controllers\Api\Admin\TpsSekolahController;
use App\Http\Controllers\Api\AdminSekolah\IdentitasSekolahController;

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
});
