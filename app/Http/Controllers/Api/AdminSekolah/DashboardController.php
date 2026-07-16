<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));
        
        // Ambil data jenjang dari tb_sekolah
        $sekolah = DB::table('tb_sekolah')->where('npsn', $npsn)->first();
        $jenjang = $sekolah ? $sekolah->jenjang : null;

        // Ambil jadwal pemilihan untuk sekolah ini ATAU jadwal global jenjang
        $jadwal = DB::table('tb_setting_waktu_pemilihan')
            ->where('tahun', $tahun)
            ->where(function ($query) use ($jenjang, $npsn) {
                $query->where('npsn', $npsn)
                      ->orWhere(function ($q) use ($jenjang) {
                          $q->whereNull('npsn')
                            ->where('jenjang', $jenjang);
                      });
            })
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        // Cek status aktif
        $now = Carbon::now()->startOfDay();
        
        foreach ($jadwal as $j) {
            $mulai = Carbon::parse($j->waktu_mulai)->startOfDay();
            $selesai = Carbon::parse($j->waktu_selesai)->endOfDay();
            
            if ($now->between($mulai, $selesai)) {
                $j->status = 'aktif';
            } elseif ($now->lt($mulai)) {
                $j->status = 'akan_datang';
            } else {
                $j->status = 'selesai';
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => $sekolah ? $sekolah->nm_sekolah : 'Unknown',
                'jadwal' => $jadwal
            ]
        ]);
    }
}
