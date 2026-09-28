<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class SekolahDptController extends Controller
{
    // Mengambil list user (level 2) berdasarkan NPSN
    public function index(Request $request, $npsn)
    {
        $tahun = env('TAHUN_AKTIF', date('Y'));
        $search = $request->query('cari');
        $filterTps = $request->query('tps_id');
        $belumMemilih = $request->query('belum_memilih') === 'true';

        // Hitung rekapan (sebelum limit dan offset untuk pagination)
        $rekapQuery = DB::table('tb_siswa_tps')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun);

        if ($request->user()->level == 3) {
            $rekapQuery->where('id_tps', $request->user()->id_tps);
        } else if ($filterTps) {
            $rekapQuery->where('id_tps', $filterTps);
        }

        $totalPemilih = $rekapQuery->count();
        $sudahMemilih = (clone $rekapQuery)->whereNotNull('pilihan')->count();
        $belumMemilihCount = $totalPemilih - $sudahMemilih;

        $query = DB::table('tb_siswa_tps as st')
            ->join('tb_siswa as s', 'st.nisn', '=', 's.nisn')
            ->leftJoin('tb_kelas as k', 'st.id_tps', '=', 'k.kd_kelas')
            ->where('st.npsn', $npsn)
            ->where('st.tahun', $tahun);

        // Jika user adalah Admin TPS (Level 3), paksa filter query ke TPS-nya sendiri
        if ($request->user()->level == 3) {
            $query->where('st.id_tps', $request->user()->id_tps);
        } else if ($filterTps) {
            // Hanya izinkan filter by parameter jika dia Admin Sekolah (Level 2)
            $query->where('st.id_tps', $filterTps);
        }

        if ($belumMemilih) {
            $query->whereNull('st.pilihan');
        }

        $query->select(
                'st.id',
                'st.nisn',
                's.nm_siswa',
                's.jk',
                's.kelas as nama_kelas_asal',
                'st.id_tps',
                'k.nm_kelas as nama_tps',
                'st.token',
                'st.pilihan',
                'st.waktu_pilih'
            );

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('s.nm_siswa', 'like', '%' . $search . '%')
                  ->orWhere('st.nisn', 'like', '%' . $search . '%');
            });
        }

        // $limit = $request->query('limit', 30);
        // $data = $query->paginate($limit);
        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'rekap' => [
                'total' => $totalPemilih,
                'sudah_memilih' => $sudahMemilih,
                'belum_memilih' => $belumMemilihCount
            ]
        ]);
    }
}
