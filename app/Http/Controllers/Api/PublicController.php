<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Get daftar sekolah yang aktif (is_delete = 0)
     */
    public function sekolah(Request $request)
    {
        $search = $request->query('cari');

        $query = DB::table('tb_sekolah')
            ->select('npsn', 'nama_sekolah', 'alamat_sekolah', 'logo')
            ->where('is_delete', 0);

        if (!empty($search)) {
            $query->where('nama_sekolah', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%");
        }

        $sekolah = $query->orderBy('nama_sekolah', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $sekolah
        ]);
    }

    /**
     * Get detail sekolah dan daftar kandidat aktifnya (Publik)
     */
    public function detailSekolah($npsn)
    {
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $sekolah = DB::table('tb_sekolah')
            ->select('npsn', 'nama_sekolah', 'alamat_sekolah', 'logo')
            ->where('npsn', $npsn)
            ->where('is_delete', 0)
            ->first();

        if (!$sekolah) {
            return response()->json([
                'success' => false,
                'message' => 'Sekolah tidak ditemukan'
            ], 404);
        }

        $kandidat = DB::table('tb_pilihan')
            ->select('id', 'npsn', 'nama', 'no', 'photo', 'photo_wakil')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->orderBy('no', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => $sekolah,
                'kandidat' => $kandidat
            ]
        ]);
    }
}
