<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class SekolahAktivitasController extends Controller
{
    // Mengambil list user (level 2) berdasarkan NPSN
    public function index(Request $request, $npsn)
    {
        $aktivitass = DB::table('activity as a')
            ->join('tb_admin as b', 'a.username', '=', 'b.username')
            ->where('b.npsn', $npsn)
            ->whereYear('a.waktu', intval(env('APP_TAHUN_AKTIF')))
            // Di CI3, level 2 adalah admin sekolah
            ->get();

        $aktivitass->transform(function ($item) {
            $item->nama_aktifitas = config("aktivitas.{$item->id_aktifitas}", 'Unknown (' . $item->id_aktifitas . ')');
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $aktivitass,
            'npsn'=>$npsn,
            'tahun'=>env('APP_TAHUN_AKTIF')
        ]);
    }
}
