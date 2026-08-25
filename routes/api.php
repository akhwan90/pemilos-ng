<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\DataSekolahController;
use App\Http\Controllers\Api\Admin\UserSekolahController;
use App\Http\Controllers\Api\Admin\JadwalSekolahController;
use App\Http\Controllers\Api\Admin\KandidatSekolahController;
use App\Http\Controllers\Api\Admin\TpsSekolahController;
use App\Http\Controllers\Api\Admin\DataSiswaGlobalController;
use App\Http\Controllers\Api\Admin\AktivitasController;
use App\Http\Controllers\Api\AdminSekolah\IdentitasSekolahController;
use App\Http\Controllers\Api\AdminSekolah\DataSiswaController;
use App\Http\Controllers\Api\AdminSekolah\UploadSiswaController;
use App\Http\Controllers\Api\AdminSekolah\KandidatController;
use App\Http\Controllers\Api\AdminSekolah\DataDptController;
use App\Http\Controllers\Api\AdminSekolah\DokumentasiController;
use App\Http\Controllers\Api\AdminSekolah\DashboardController;
use App\Http\Controllers\Api\AdminSekolah\ApprovalPindahController;
use App\Http\Controllers\Api\AdminTps\AdminTpsController;
use App\Http\Controllers\Api\Bilik\BilikController;
use App\Http\Controllers\Api\PublicController;

// ========== PUBLIC API (No Auth Required) ==========
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::get('/cek-jadwal-public', [AuthController::class, 'cekJadwalPublic'])->middleware('throttle:10,1');

Route::post('/bilik-luar-sekolah/verify', [BilikController::class, 'verifyLuarSekolah'])->middleware('throttle:10,1');

Route::get('/public/sekolah', [PublicController::class, 'sekolah']);
Route::get('/public/sekolah/{npsn}', [PublicController::class, 'detailSekolah']);
Route::get('/public/sekolah/{npsn}/dps', [PublicController::class, 'dataDps']);
Route::get('/public/sekolah/{npsn}/dpt', [PublicController::class, 'dataDpt']);
Route::get('/public/sekolah/{npsn}/tps', [PublicController::class, 'listTps']);
Route::get('/public/arsip/{tahun}', [PublicController::class, 'arsipTahun']);
Route::get('/public/arsip/{tahun}/{npsn}', [PublicController::class, 'arsipHasil']);
Route::get('/public/stats', [PublicController::class, 'stats']);

// ========== ADMIN API (Sanctum Auth Required) ==========
Route::middleware('auth:sanctum')->group(function () {
    // Dashboard Stats Super Admin (Dari project Gesit)
    Route::get('/admin/dashboard/stats', [App\Http\Controllers\Api\Admin\DashboardController::class, 'stats']);

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Admin & Sekolah API
    Route::get('/admin/data-sekolah', [DataSekolahController::class, 'index']);
    Route::post('/admin/data-sekolah', [DataSekolahController::class, 'store']);
    Route::get('/admin/data-sekolah/{npsn}', [DataSekolahController::class, 'show']);
    Route::post('/admin/data-sekolah/{npsn}', [DataSekolahController::class, 'update']);
    Route::delete('/admin/data-sekolah/{npsn}', [DataSekolahController::class, 'destroy']);

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
    Route::get('/admin/data-sekolah/{npsn}/tps/{kd_kelas}/admin', [TpsSekolahController::class, 'getAdmin']);
    Route::post('/admin/data-sekolah/{npsn}/tps/{kd_kelas}/admin', [TpsSekolahController::class, 'storeAdmin']);
    Route::delete('/admin/data-sekolah/{npsn}/tps/{kd_kelas}/admin/{id}', [TpsSekolahController::class, 'destroyAdmin']);
    Route::put('/admin/data-sekolah/{npsn}/tps/{kd_kelas}/admin/{id}/password', [TpsSekolahController::class, 'updateAdminPassword']);

    // Data Siswa Global
    Route::get('/admin/data-siswa-global', [DataSiswaGlobalController::class, 'index']);
    Route::delete('/admin/data-siswa-global/{id}', [DataSiswaGlobalController::class, 'destroy']);

    // Data Aktivitas
    Route::get('/admin/aktivitas', [AktivitasController::class, 'index']);

    Route::prefix('admin/data-user')->group(function() {
        Route::get('/', [App\Http\Controllers\Api\Admin\DataUserController::class, 'index']);
        Route::put('/{id}', [App\Http\Controllers\Api\Admin\DataUserController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Api\Admin\DataUserController::class, 'destroy']);
    });

    // Log Approval Pindah Sekolah
    Route::get('/admin/log-approval', [App\Http\Controllers\Api\Admin\LogApprovalController::class, 'index']);
    Route::post('/admin/log-approval/{id}/approve', [App\Http\Controllers\Api\Admin\LogApprovalController::class, 'approve']);


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
    Route::get('/admin-sekolah/siswa/excel', [DataSiswaController::class, 'excel']);

    // Upload Siswa API
    Route::get('/admin-sekolah/upload-siswa', [UploadSiswaController::class, 'history']);
    Route::post('/admin-sekolah/upload-siswa', [UploadSiswaController::class, 'upload']);
    Route::get('/admin-sekolah/upload-siswa/{id}/logs', [UploadSiswaController::class, 'logs']);

    // Data TPS / Kelas API (Admin Sekolah dipisah untuk menghindari argumen bentrok dengan Super Admin)
    Route::get('/admin-sekolah/tps', [\App\Http\Controllers\Api\AdminSekolah\TpsController::class, 'index']);
    Route::post('/admin-sekolah/tps', [\App\Http\Controllers\Api\AdminSekolah\TpsController::class, 'store']);
    Route::put('/admin-sekolah/tps/{kd_kelas}', [\App\Http\Controllers\Api\AdminSekolah\TpsController::class, 'update']);
    Route::delete('/admin-sekolah/tps/{kd_kelas}', [\App\Http\Controllers\Api\AdminSekolah\TpsController::class, 'destroy']);
    Route::get('/admin-sekolah/tps/{kd_kelas}/admin', [\App\Http\Controllers\Api\AdminSekolah\TpsController::class, 'getAdmin']);
    Route::post('/admin-sekolah/tps/{kd_kelas}/admin', [\App\Http\Controllers\Api\AdminSekolah\TpsController::class, 'storeAdmin']);
    Route::delete('/admin-sekolah/tps/{kd_kelas}/admin/{id}', [\App\Http\Controllers\Api\AdminSekolah\TpsController::class, 'destroyAdmin']);
    Route::put('/admin-sekolah/tps/{kd_kelas}/admin/{id}/password', [\App\Http\Controllers\Api\AdminSekolah\TpsController::class, 'updateAdminPassword']);

    // Data Kandidat Calon (Admin Sekolah khusus CRUD Kandidat Sendiri)
    Route::get('/admin-sekolah/kandidat', [KandidatController::class, 'index']);
    Route::get('/admin-sekolah/kandidat/{id}', [KandidatController::class, 'show']);
    Route::post('/admin-sekolah/kandidat', [KandidatController::class, 'store']); // Create
    Route::post('/admin-sekolah/kandidat/{id}', [KandidatController::class, 'update']); // Update
    Route::delete('/admin-sekolah/kandidat/{id}', [KandidatController::class, 'destroy']);

    // Data DPT (Daftar Pemilih Tetap - tb_siswa_tps)
    Route::get('/admin-sekolah/dpt', [DataDptController::class, 'index']);
    Route::get('/admin-sekolah/dpt/siswa-belum-dpt', [DataDptController::class, 'siswaBelumDpt']);
    Route::get('/admin-sekolah/dpt/tps-aktif', [DataDptController::class, 'listTpsAktif']);
    Route::post('/admin-sekolah/dpt/bulk-insert', [DataDptController::class, 'storeBulk']);
    Route::post('/admin-sekolah/dpt/bulk-delete', [DataDptController::class, 'destroyBulk']);
    Route::post('/admin-sekolah/dpt/generate-token', [DataDptController::class, 'generateToken']);
    Route::post('/admin-sekolah/dpt/cancel-token', [DataDptController::class, 'cancelToken']);

    // Dokumentasi Pelaksanaan Pemilos
    Route::get('/admin-sekolah/dokumentasi', [DokumentasiController::class, 'index']);
    Route::post('/admin-sekolah/dokumentasi', [DokumentasiController::class, 'store']); // Create / Upload Foto
    Route::delete('/admin-sekolah/dokumentasi/{id}', [DokumentasiController::class, 'destroy']);

    // Approval Pindah Sekolah
    Route::get('/admin-sekolah/approval-pindah', [ApprovalPindahController::class, 'index']);
    Route::get('/admin-sekolah/approval-pindah-keluar', [ApprovalPindahController::class, 'indexKeluar']);
    Route::post('/admin-sekolah/approval-pindah/{id}/approve', [ApprovalPindahController::class, 'approve']);

    // Dashboard Info
    Route::get('/admin-sekolah/dashboard', [DashboardController::class, 'index']);

    // ==========================================
    // LEVEL 3: ADMIN TPS API
    // ==========================================
    Route::get('/admin-tps/status-pemilihan', [AdminTpsController::class, 'getStatus']);
    Route::post('/admin-tps/akhiri-pemilihan', [AdminTpsController::class, 'akhiriPemilihan']);
    Route::get('/admin-tps/perangkat', [AdminTpsController::class, 'getPerangkat']);
    Route::post('/admin-tps/perangkat', [AdminTpsController::class, 'savePerangkat']);
    Route::get('/admin-tps/c2', [AdminTpsController::class, 'getC2']);
    Route::post('/admin-tps/c2', [AdminTpsController::class, 'saveC2']);
    Route::get('/admin-tps/hasil-c1', [AdminTpsController::class, 'getHasilC1']);
    Route::post('/admin-tps/upload-c1', [AdminTpsController::class, 'uploadC1']);

    // Bilik Suara API (Khusus Level 3)
    Route::get('/bilik/status', [BilikController::class, 'getStatus']);
    Route::post('/bilik/verify-token', [BilikController::class, 'verifyToken']);
    Route::get('/bilik/calon', [BilikController::class, 'listCalon']);
    Route::post('/bilik/submit-vote', [BilikController::class, 'submitVote']);
});
