<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class SekolahHistoryController extends Controller
{
    // Mengambil list user (level 2) berdasarkan NPSN
    public function index(Request $request, $npsn)
    {
        $jumlahDpt = DB::table('tb_siswa_tps as a')
            ->join('tb_sekolah', 'a.npsn', '=', 'tb_sekolah.npsn')
            ->where('a.npsn', $npsn)
            ->select(
                'a.npsn',
                'a.tahun',
                'tb_sekolah.nama_sekolah',
                DB::raw("COUNT(a.id) as jml_dpt"),
            )
            ->groupBy('a.npsn')
            ->groupBy('a.tahun')
            ->groupBy('tb_sekolah.nama_sekolah')
            ->orderBy('a.tahun', 'asc')
            ->get();


        return response()->json([
            'success' => true,
            'data' => $jumlahDpt,
            'npsn'=>$npsn,
            'tahun'=>env('APP_TAHUN_AKTIF')
        ]);
    }
}
