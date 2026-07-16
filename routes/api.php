<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\DataSekolahController;

// ========== PUBLIC API (No Auth Required) ==========

// Auth (login admin/sekolah)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

// ========== ADMIN API (Sanctum Auth Required) ==========
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Data Sekolah (List Data Pemilihan / Progress Pemilihan Sekolah)
    Route::get('/admin/data-sekolah', [DataSekolahController::class, 'index']);
});
