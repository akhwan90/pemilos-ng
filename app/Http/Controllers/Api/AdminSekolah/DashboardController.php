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

        // Ambil label & deskripsi dari config
        $jenisConfig = config('pemilos.jenis_jadwal', []);

        foreach ($jadwal as $j) {
            $j->label = $jenisConfig[$j->jenis]['label'] ?? $j->jenis;
            $j->deskripsi = $jenisConfig[$j->jenis]['deskripsi'] ?? '';

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

        // Hitung statistik
        $jml_siswa = DB::table('tb_siswa')->where('npsn', $npsn)->where('status', 1)->count();
        $jml_tps = DB::table('tb_kelas')->where('npsn', $npsn)->where('is_hapus', 0)->count();
        $jml_kandidat = DB::table('tb_pilihan')->where('npsn', $npsn)->where('tahun', $tahun)->count();
        $jml_dpt = DB::table('tb_siswa_tps')->where('npsn', $npsn)->where('tahun', $tahun)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => $sekolah ? $sekolah->nama_sekolah : 'Unknown',
                'jadwal' => $jadwal,
                'stats' => [
                    'jml_siswa' => $jml_siswa,
                    'jml_tps' => $jml_tps,
                    'jml_kandidat' => $jml_kandidat,
                    'jml_dpt' => $jml_dpt
                ]
            ]
        ]);
    }
}
