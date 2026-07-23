<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WaktuPemilihanService;

class PublicController extends Controller
{
    protected $waktuPemilihanService;

    public function __construct(WaktuPemilihanService $waktuPemilihanService)
    {
        $this->waktuPemilihanService = $waktuPemilihanService;
    }
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

        $kandidat = [];
        $cekKampanye = $this->waktuPemilihanService->cekJadwalBuka('kampanye', $tahun, $npsn);
        
        if ($cekKampanye['is_open'] || strpos($cekKampanye['message'], 'berakhir') !== false) {
            $kandidat = DB::table('tb_pilihan')
                ->select('id', 'npsn', 'nama', 'no', 'photo', 'photo_wakil')
                ->where('npsn', $npsn)
                ->where('tahun', $tahun)
                ->orderBy('no', 'asc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => $sekolah,
                'kandidat' => $kandidat,
                'is_kampanye_buka' => ($cekKampanye['is_open'] || strpos($cekKampanye['message'], 'berakhir') !== false)
            ]
        ]);
    }
    
    /**
    * API Pendukung: Data DPS Sekolah
    */
    public function dataDps(Request $request, $npsn)
    {
        $tahun = env('TAHUN_AKTIF', date('Y'));
        
        $cek = $this->waktuPemilihanService->cekJadwalBuka('pengumuman_data_dps', $tahun, $npsn);
        
        // Membaca statusnya: Jika status belum open tapi juga bukan 'berakhir' (alias: belum diset atau belum masuk waktu mulai)
        if (!$cek['is_open'] && strpos($cek['message'], 'berakhir') === false) {
            return response()->json([
                'success' => false,
                'message' => 'Data Pemilih Sementara (DPS) belum dibuka.',
                'is_buka' => false
            ], 403);
        }

        $search = $request->query('cari');

        $query = DB::table('tb_siswa')
            ->select('nisn', 'nm_siswa', 'kelas', 'jk')
            ->where('npsn', $npsn)
            ->where('status', 1); // 1 = Aktif

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nm_siswa', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $dps = $query->orderBy('kelas')->orderBy('nm_siswa')->paginate(50);

        return response()->json([
            'success' => true,
            'is_buka' => true,
            'data' => $dps
        ]);
    }

    /**
     * API Pendukung: Data DPT Sekolah
     */
    public function dataDpt(Request $request, $npsn)
    {
        $tahun = env('TAHUN_AKTIF', date('Y'));
        
        $cek = $this->waktuPemilihanService->cekJadwalBuka('pengumuman_data_dpt', $tahun, $npsn);
        
        if (!$cek['is_open'] && strpos($cek['message'], 'berakhir') === false) {
            return response()->json([
                'success' => false,
                'message' => 'Data Pemilih Tetap (DPT) belum diumumkan.',
                'is_buka' => false
            ], 403);
        }

        $search = $request->query('cari');
        $tpsId = $request->query('tps_id');

        $query = DB::table('tb_siswa_tps as st')
            ->join('tb_siswa as s', 'st.nisn', '=', 's.nisn')
            ->leftJoin('tb_kelas as k', 'st.id_tps', '=', 'k.kd_kelas')
            ->select('st.nisn', 's.nm_siswa', 's.jk', 's.kelas as nama_kelas_asal', 'k.nm_kelas as nama_tps')
            ->where('st.npsn', $npsn)
            ->where('st.tahun', $tahun);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('s.nm_siswa', 'like', "%{$search}%")
                  ->orWhere('st.nisn', 'like', "%{$search}%");
            });
        }
        
        if (!empty($tpsId)) {
            $query->where('st.id_tps', $tpsId);
        }

        $dpt = $query->orderBy('k.nm_kelas')->orderBy('s.nm_siswa')->paginate(50);

        return response()->json([
            'success' => true,
            'is_buka' => true,
            'data' => $dpt
        ]);
    }

    /**
     * Get list TPS / Kelas untuk filter dropdown DPT (Public)
     */
    public function listTps($npsn)
    {
        $tps = DB::table('tb_kelas')
            ->where('npsn', $npsn)
            ->where('is_hapus', 0)
            ->select('kd_kelas', 'nm_kelas')
            ->orderBy('nm_kelas')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tps
        ]);
    }
}
