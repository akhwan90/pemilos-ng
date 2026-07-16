<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ========== PUBLIC API (No Auth Required) ==========

// Auth (login admin/sekolah)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

// ========== ADMIN API (Sanctum Auth Required) ==========
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
