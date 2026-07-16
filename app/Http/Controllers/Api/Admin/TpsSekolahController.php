<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TpsSekolahController extends Controller
{
    // List TPS (tb_kelas)
    public function index($npsn)
    {
        $tps = DB::table('tb_kelas')
            ->where('npsn', $npsn)
            ->get(); // Tampilkan semuanya agar bisa di-edit status is_hapus-nya

        return response()->json([
            'success' => true,
            'data' => $tps
        ]);
    }

    // Menambah TPS Baru
    public function store(Request $request, $npsn)
    {
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

    // Mengupdate Data TPS
    public function update(Request $request, $npsn, $kd_kelas)
    {
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
            // Bisa jadi tidak ada perubahan data (0 rows affected), kita anggap sukses saja
        }

        return response()->json([
            'success' => true,
            'message' => 'Data TPS berhasil diperbarui'
        ]);
    }

    // Soft delete TPS
    public function destroy($npsn, $kd_kelas)
    {
        // Set is_hapus = 1
        $affected = DB::table('tb_kelas')
            ->where('kd_kelas', $kd_kelas)
            ->where('npsn', $npsn)
            ->update(['is_hapus' => 1]);

        if ($affected) {
            return response()->json([
                'success' => true,
                'message' => 'TPS berhasil dihapus'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'TPS tidak ditemukan'], 404);
    }
}
