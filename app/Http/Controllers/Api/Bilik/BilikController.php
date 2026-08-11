<?php

namespace App\Http\Controllers\Api\Bilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class BilikController extends Controller
{
    /**
     * Mengecek status pemilihan apakah sedang dibuka atau tidak.
     */
    public function getStatus(Request $request)
    {
        $user = $request->user();

        if ($user->level != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak! Anda bukan Admin TPS.'
            ], 403);
        }

        $npsn = $user->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $idTps = $user->id_tps;
        if (!$idTps) {
            return response()->json([
                'success' => false,
                'message' => 'ID TPS harus disertakan.'
            ], 422);
        }
        // Cek apakah pemilihan sudah diselesaikan (closed)
        $tpsSetting = DB::table('tb_tps_setting')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('id_kelas', $idTps)
            ->first();

        if ($tpsSetting && !empty($tpsSetting->selesai_pemilihan_time)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal get status pemilihan, karena Waktu pemilihan telah ditutup/diakhiri.'
            ], 422);
        }


        $waktuService = app(\App\Services\WaktuPemilihanService::class);
        $cekWaktu = $waktuService->cekJadwalBuka('pemilihan', $tahun, $npsn);

        return response()->json([
            'success' => true,
            'is_open' => $cekWaktu['is_open'],
            'message' => $cekWaktu['message']
        ]);
    }

    /**
     * Memverifikasi Token Pemilih.
     * Mengembalikan data siswa (DPT) jika token valid untuk TPS ini.
     */
    public function verifyToken(Request $request)
    {
        $user = $request->user();

        // Pastikan hanya TPS Level 3 yang menggunakan endpoint ini
        if ($user->level != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak! Anda bukan Admin TPS.'
            ], 403);
        }

        $request->validate([
            'token' => 'required|string',
            'nisn' => 'nullable|string' // Diperlukan untuk TPS Luar Sekolah
        ]);

        $token = strtoupper($request->token);
        $nisn = $request->nisn;
        $idTps = $user->id_tps;
        $npsn = $user->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        // Cek tipe TPS apakah ini TPS Luar Sekolah
        $tpsInfo = DB::table('tb_kelas')->where('kd_kelas', $idTps)->where('npsn', $npsn)->first();
        $isLuarSekolah = $tpsInfo && isset($tpsInfo->is_tps_luar_sekolah) && $tpsInfo->is_tps_luar_sekolah == 1;

        // --- Tambahan: Pengecekan Jadwal Pemilihan ---
        $waktuService = app(\App\Services\WaktuPemilihanService::class);
        $cekWaktu = $waktuService->cekJadwalBuka('pemilihan', $tahun, $npsn);
        if (!$cekWaktu['is_open']) {
            return response()->json([
                'success' => false,
                'message' => 'Token ditolak: ' . $cekWaktu['message']
            ], 403);
        }
        // --- Akhir Tambahan ---

        if ($isLuarSekolah && empty($nisn)) {
            return response()->json([
                'success' => false,
                'message' => 'NISN wajib diisi untuk TPS Luar Sekolah!'
            ], 422);
        }

        // Cek apakah token ada di TPS tersebut pada tahun ini
        $query = DB::table('tb_siswa_tps as st')
            ->join('tb_siswa as s', 'st.nisn', '=', 's.nisn')
            ->leftJoin('tb_kelas as k', 'st.id_tps', '=', 'k.kd_kelas')
            ->select('st.id as id_siswa_tps', 'st.nisn', 's.nm_siswa', 's.kelas', 'st.pilihan', 'k.nm_kelas as nama_tps', 'k.is_tps_luar_sekolah')
            ->where('st.id_tps', $idTps)
            ->where('st.npsn', $npsn)
            ->where('st.tahun', $tahun)
            ->where('st.token', $token);

        // Jika TPS Luar Sekolah, token DAN nisn harus cocok
        if ($isLuarSekolah) {
            $query->where('st.nisn', $nisn);
        }

        $siswaTps = $query->first();

        if (!$siswaTps) {
            $msg = $isLuarSekolah ? 'Kombinasi NISN dan Token tidak valid!' : 'Token tidak ditemukan di TPS ini!';
            return response()->json([
                'success' => false,
                'message' => $msg
            ], 404);
        }

        // Cek apakah siswa tersebut sudah memilih
        if (!empty($siswaTps->pilihan)) {
            return response()->json([
                'success' => false,
                'message' => 'Token DITOLAK! Pemilih dengan token ini sudah menggunakan hak suaranya.'
            ], 422);
        }

        $waktuLogin = Carbon::now();
        \Illuminate\Support\Facades\Log::info('TOKEN '.$token.' MASUK - WAKTU MULAI: ' . $waktuLogin->format('Y-m-d H:i:s.u'));

        // Insert ke tb_log_pilih
        $logId = DB::table('tb_log_pilih')->insertGetId([
            'npsn' => $npsn,
            'nisn' => $nisn ?? $siswaTps->nisn,
            'waktu_login' => $waktuLogin,
            'id_tps' => $idTps,
            'success' => 0,
            'token' => $token,
            'tahun' => $tahun,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token valid.',
            'siswa' => $siswaTps,
            'log_id' => $logId
        ]);
    }

    /**
     * List Kandidat (Calon) yang tersedia untuk NPSN ini
     */
    public function listCalon(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $calon = DB::table('tb_pilihan')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            // ->where('is_hapus', 0)
            ->orderBy('no', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $calon
        ]);
    }

    /**
     * Menyimpan pilihan pemilih (Submit Vote)
     */
    public function submitVote(Request $request)
    {
        $user = $request->user();

        if ($user->level != 3) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak!'], 403);
        }

        $request->validate([
            'id_siswa_tps' => 'required|integer',
            'id_calon' => 'required|integer',
            'log_id' => 'required|integer'
        ]);

        $idTps = $user->id_tps;
        $npsn = $user->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $idSiswaTps = $request->id_siswa_tps;
        $idCalon = $request->id_calon;
        $logId = $request->log_id;

        // Cek apakah request ini berasal dari token sementara luar sekolah
        $isLuarSekolahToken = $user->currentAccessToken()->can('submit-vote');

        $dptQuery = DB::table('tb_siswa_tps')
            ->where('id', $idSiswaTps)
            ->where('npsn', $npsn)
            ->where('tahun', $tahun);

        if (!$isLuarSekolahToken) {
            // Untuk bilik reguler, pastikan id_tps cocok secara eksak dengan bilik login saat ini
            $dptQuery->where('id_tps', $idTps);
        }

        $dpt = $dptQuery->first();

        if (!$dpt) {
            return response()->json(['success' => false, 'message' => 'Data Pemilih tidak sah.'], 400);
        }

        if (!empty($dpt->pilihan)) {
            return response()->json(['success' => false, 'message' => 'Siswa sudah pernah mencoblos! (Double Vote Protection)'], 422);
        }

        try {
            DB::beginTransaction();

            // Simpan suara
            $waktuSelesai = Carbon::now();
            DB::table('tb_siswa_tps')
                ->where('id', $idSiswaTps)
                ->update([
                    'pilihan' => $idCalon,
                    'waktu_pilih' => $waktuSelesai
                ]);

            \Illuminate\Support\Facades\Log::info('TOKEN '.$dpt->token.' SELESAI MEMILIH - WAKTU SELESAI: ' . $waktuSelesai->format('Y-m-d H:i:s.u'));

            // Update tb_log_pilih
            $logEntry = DB::table('tb_log_pilih')->where('id', $logId)->first();
            if ($logEntry && $logEntry->waktu_login) {
                $waktuLogin = Carbon::parse($logEntry->waktu_login);
                $lamaDetik = $waktuLogin->diffInSeconds($waktuSelesai);

                DB::table('tb_log_pilih')
                    ->where('id', $logId)
                    ->update([
                        'waktu_logout' => $waktuSelesai,
                        'success' => 1,
                        'lama' => $lamaDetik,
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Suara berhasil disimpan. Terima kasih telah berpartisipasi.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('GAGAL SUBMIT VOTE TOKEN '.$dpt->token.': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menyimpan suara. Silakan coba lagi.'
            ], 500);
        }
    }
    /**
     * Verifikasi Pemilih Luar Sekolah (Endpoint Publik)
     * Tidak perlu login admin TPS, pemilih memvalidasi dirinya sendiri menggunakan NISN + Token.
     */
    public function verifyLuarSekolah(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'nisn' => 'required|string'
        ]);

        $token = strtoupper($request->token);
        $nisn = $request->nisn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        // --- Tambahan: Pengecekan Jadwal Pemilihan ---
        // Karena ini endpoint publik, kita perlu tau NPSN sekolah dari NISN tersebut
        $siswa = DB::table('tb_siswa')->where('nisn', $nisn)->where('tahun', $tahun)->first();
        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'NISN tidak ditemukan.'
            ], 404);
        }

        $waktuService = app(\App\Services\WaktuPemilihanService::class);
        $cekWaktu = $waktuService->cekJadwalBuka('pelaksanaan_pemilihan', $tahun, $siswa->npsn);
        if (!$cekWaktu['is_open']) {
            return response()->json([
                'success' => false,
                'message' => 'Token ditolak: ' . $cekWaktu['message']
            ], 403);
        }
        // --- Akhir Tambahan ---

        $waktuLogin = Carbon::now();

        // Cek langsung dari tb_siswa_tps dan pastikan belongs ke tb_kelas yang is_tps_luar_sekolah = 1
        $siswaTps = DB::table('tb_siswa_tps as st')
            ->join('tb_siswa as s', 'st.nisn', '=', 's.nisn')
            ->join('tb_kelas as k', 'st.id_tps', '=', 'k.kd_kelas')
            ->select('st.id as id_siswa_tps', 'st.nisn', 'st.npsn', 's.nm_siswa', 's.kelas', 'st.pilihan', 'k.nm_kelas as nama_tps')
            ->where('st.tahun', $tahun)
            ->where('st.token', $token)
            ->where('st.nisn', $nisn)
            ->where('k.is_tps_luar_sekolah', 1)
            ->first();

        if (!$siswaTps) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi NISN dan Token tidak valid, atau bukan terdaftar sebagai TPS Luar Sekolah.'
            ], 404);
        }

        // Cari Admin TPS (Level 3) yang mengurus sekolah & TPS ini untuk "dipinjam" token sementaranya
        $tpsAdmin = \App\Models\Admin::where('npsn', $siswaTps->npsn)->where('level', 3)->first();
        $tokenString = 'fallback-token-for-' . $siswaTps->npsn;

        if ($tpsAdmin) {
            // Generate token sementara (bearer) yang akan dipakai untuk submitVote
            $tokenObj = $tpsAdmin->createToken('luar-sekolah-vote', ['submit-vote']);
            $tokenString = $tokenObj->plainTextToken;
        }

        \Illuminate\Support\Facades\Log::info('TOKEN LUAR SEKOLAH '.$token.' MASUK - WAKTU MULAI: ' . $waktuLogin->format('Y-m-d H:i:s.u'));

        // Insert ke tb_log_pilih
        $logId = DB::table('tb_log_pilih')->insertGetId([
            'npsn' => $siswaTps->npsn,
            'nisn' => $nisn,
            'waktu_login' => $waktuLogin,
            'id_tps' => $siswaTps->id_tps ?? 0,
            'success' => 0,
            'token' => $token,
            'tahun' => $tahun,
            'created_at' => $waktuLogin,
            'updated_at' => $waktuLogin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token Luar Sekolah diverifikasi',
            'data' => $siswaTps,
            'token' => $tokenString,
            'log_id' => $logId
        ]);
    }
}
