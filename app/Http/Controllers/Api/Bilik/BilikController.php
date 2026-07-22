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
            'token' => 'required|string'
        ]);

        $token = strtoupper($request->token);
        $idTps = $user->id_tps;
        $npsn = $user->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        // Cek apakah token ada di TPS tersebut pada tahun ini
        $siswaTps = DB::table('tb_siswa_tps as st')
            ->join('tb_siswa as s', 'st.nisn', '=', 's.nisn')
            ->leftJoin('tb_kelas as k', 'st.id_tps', '=', 'k.kd_kelas')
            ->select('st.id as id_siswa_tps', 'st.nisn', 's.nm_siswa', 's.kelas', 'st.pilihan', 'k.nm_kelas as nama_tps')
            ->where('st.id_tps', $idTps)
            ->where('st.npsn', $npsn)
            ->where('st.tahun', $tahun)
            ->where('st.token', $token)
            ->first();

        if (!$siswaTps) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan di TPS ini!'
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
}
