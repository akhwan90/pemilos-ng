<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $tahun = env('TAHUN_AKTIF', date('Y'));

        // Statistik Total
        $totalSekolah = DB::table('tb_sekolah')->where('is_delete', 0)->count();
        $totalSiswa = DB::table('tb_siswa')->where('tahun', $tahun)->whereIn('status', [1, 4])->count();
        $totalKandidat = DB::table('tb_pilihan')->where('tahun', $tahun)->count();
        $totalTps = DB::table('tb_kelas')->where('is_hapus', 0)->count();

        // Hitung partisipasi (Berapa yang sudah memilih)
        $totalDpt = DB::table('tb_siswa_tps')->where('tahun', $tahun)->count();
        $sudahPilih = DB::table('tb_siswa_tps')->where('tahun', $tahun)->whereNotNull('waktu_pilih')->count();
        
        $persentasePartisipasi = $totalDpt > 0 ? round(($sudahPilih / $totalDpt) * 100, 1) : 0;

        // Progress Jadwal Pemilihan (Global)
        $jadwalAktif = DB::table('tb_setting_waktu_pemilihan')
            ->where('tahun', $tahun)
            ->whereNull('npsn')
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'totals' => [
                    'sekolah' => $totalSekolah,
                    'siswa_aktif' => $totalSiswa,
                    'kandidat' => $totalKandidat,
                    'tps' => $totalTps
                ],
                'partisipasi' => [
                    'total_dpt' => $totalDpt,
                    'sudah_memilih' => $sudahPilih,
                    'belum_memilih' => $totalDpt - $sudahPilih,
                    'persentase' => $persentasePartisipasi
                ],
                'jadwal_berlangsung' => $jadwalAktif
            ]
        ]);
    }
}
