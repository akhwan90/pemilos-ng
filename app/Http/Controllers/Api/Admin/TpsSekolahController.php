<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Middleware yang membatasi hak akses CRUD TPS sesuai NPSN admin sekolah
// =========================================================================

class TpsSekolahController extends Controller
{
    // List TPS (tb_kelas)
    public function index(Request $request, $npsn = null)
    {
        // Jika level 2 (Admin Sekolah), timpa $npsn dari URL dengan npsn miliknya
        if ($request->user()->level == 2) {
            $npsn = $request->user()->npsn;
        }

        $tps = DB::table('tb_kelas')
            ->where('npsn', $npsn)
            ->get(); 

        return response()->json([
            'success' => true,
            'data' => $tps
        ]);
    }

    // Menambah TPS Baru
    public function store(Request $request, $npsn = null)
    {
        if ($request->user()->level == 2) {
            $npsn = $request->user()->npsn;
        }

        $request->validate([
            'nm_kelas' => 'required|string|max:32',
            'is_tps_luar_sekolah' => 'required|boolean'
        ]);

        $id = DB::table('tb_kelas')->insertGetId([
            'npsn' => $npsn,
            'nm_kelas' => $request->nm_kelas,
            'is_tps_luar_sekolah' => $request->is_tps_luar_sekolah ? 1 : 0,
            'is_generate_token' => 0, // default
            'is_cetak_ba' => 0,
            'is_hapus' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data TPS berhasil ditambahkan',
            'data' => ['kd_kelas' => $id]
        ]);
    }

    // Edit TPS
    public function update(Request $request, $npsn = null, $kd_kelas)
    {
        if ($request->user()->level == 2) {
            $npsn = $request->user()->npsn;
        }

        $request->validate([
            'nm_kelas' => 'required|string|max:32',
            'is_tps_luar_sekolah' => 'required|boolean',
            'is_hapus' => 'required|boolean'
        ]);

        $affected = DB::table('tb_kelas')
            ->where('kd_kelas', $kd_kelas)
            ->where('npsn', $npsn)
            ->update([
                'nm_kelas' => $request->nm_kelas,
                'is_tps_luar_sekolah' => $request->is_tps_luar_sekolah ? 1 : 0,
                'is_hapus' => $request->is_hapus ? 1 : 0
            ]);

        if ($affected === 0) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan atau tidak ada perubahan'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data TPS / Kelas berhasil diperbarui'
        ]);
    }

    // Soft Delete TPS
    public function destroy(Request $request, $npsn = null, $kd_kelas)
    {
        if ($request->user()->level == 2) {
            $npsn = $request->user()->npsn;
        }

        $affected = DB::table('tb_kelas')
            ->where('kd_kelas', $kd_kelas)
            ->where('npsn', $npsn)
            ->update([
                'is_hapus' => 1
            ]);
        if ($affected) {
            return response()->json([
                'success' => true,
                'message' => 'TPS berhasil dihapus'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'TPS tidak ditemukan'], 404);
    }
}
