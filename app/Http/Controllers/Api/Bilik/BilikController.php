<?php

namespace App\Http\Controllers\Api\Bilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BilikController extends Controller
{
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

        return response()->json([
            'success' => true,
            'message' => 'Token valid.',
            'siswa' => $siswaTps
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
            'id_calon' => 'required|integer'
        ]);

        $idTps = $user->id_tps;
        $npsn = $user->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $idSiswaTps = $request->id_siswa_tps;
        $idCalon = $request->id_calon;

        // Validasi ekstra:
        // 1. Pastikan siswa_tps ini memang berada di TPS yang me-request ini
        // 2. Pastikan belum pernah milih sebelumnya (Double Vote Protection)
        $dpt = DB::table('tb_siswa_tps')
            ->where('id', $idSiswaTps)
            ->where('id_tps', $idTps)
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->first();

        if (!$dpt) {
            return response()->json(['success' => false, 'message' => 'Data Pemilih tidak sah.'], 400);
        }

        if (!empty($dpt->pilihan)) {
            return response()->json(['success' => false, 'message' => 'Siswa sudah pernah mencoblos! (Double Vote Protection)'], 422);
        }

        // Simpan suara
        DB::table('tb_siswa_tps')
            ->where('id', $idSiswaTps)
            ->update([
                'pilihan' => $idCalon,
                'waktu_pilih' => Carbon::now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Suara berhasil disimpan. Terima kasih telah berpartisipasi.'
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Token Luar Sekolah diverifikasi',
            'data' => $siswaTps,
            'token' => $tokenString
        ]);
    }
}
