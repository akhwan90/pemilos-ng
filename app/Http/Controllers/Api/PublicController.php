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

    /**
     * Get data arsip berdasarkan tahun (Sama dengan Arsip.php -> tahun() di CodeIgniter)
     */
    public function arsipTahun($tahun)
    {
        if ($tahun >= 2022) {
            $rawQuery = "SELECT 
            tb_sekolah.npsn,
            tb_sekolah.nama_sekolah,
            tb_sekolah.jenjang,
            (SELECT COUNT(id) FROM tb_siswa_tps WHERE npsn = tb_sekolah.npsn AND tahun = ?) AS jml_dpt,
            (SELECT COUNT(id) FROM tb_siswa_tps WHERE npsn = tb_sekolah.npsn AND tahun = ? AND pilihan IS NOT NULL) AS jml_dpt_memilih,
            ((SELECT COUNT(id) FROM tb_siswa_tps WHERE npsn = tb_sekolah.npsn AND tahun = ? AND pilihan IS NOT NULL)/(SELECT COUNT(id) FROM tb_siswa_tps WHERE npsn = tb_sekolah.npsn AND tahun = ?)) * 100 AS persentase,
            (SELECT COUNT(id) FROM tb_pilihan WHERE npsn = tb_sekolah.npsn AND tahun = ?) AS jml_calon
            FROM tb_sekolah
            WHERE tb_sekolah.is_delete = 0
            ORDER BY persentase DESC, jml_dpt DESC
            ";

            $data = DB::select($rawQuery, [$tahun, $tahun, $tahun, $tahun, $tahun]);
            
            foreach($data as &$item) {
                if($item->persentase !== null) {
                    if($item->persentase !== null && $item->persentase <= 1.01) { $item->persentase = ((float)$item->persentase) * 100; }
                }
            }
        } else {
            $rawQuery = "SELECT 
            a.npsn,
            a.nama_sekolah,
            a.jenjang,
            (SELECT jml_dpt FROM rekap_per_sekolah WHERE id_sekolah = a.npsn AND tahun = ?) AS jml_dpt,
            (SELECT SUM(jumlah_total_suara) FROM rekap_hasil WHERE id_sekolah = a.npsn AND tahun = ? GROUP BY id_sekolah) AS jml_dpt_memilih,
            (SELECT SUM(jml_calon) FROM rekap_per_sekolah WHERE id_sekolah = a.npsn AND tahun = ? GROUP BY id_sekolah) AS jml_calon,
            ((SELECT SUM(jumlah_total_suara) FROM rekap_hasil WHERE id_sekolah = a.npsn AND tahun = ? GROUP BY id_sekolah)/(SELECT jml_dpt FROM rekap_per_sekolah WHERE id_sekolah = a.npsn AND tahun = ?)) AS persentase
            FROM tb_sekolah a
            WHERE a.is_delete = 0
            ORDER BY persentase DESC, jml_dpt DESC
            ";
            
            $data = DB::select($rawQuery, [$tahun, $tahun, $tahun, $tahun, $tahun]);
            
            foreach($data as &$item) {
                if($item->persentase !== null) {
                    if($item->persentase !== null && $item->persentase <= 1.01) { $item->persentase = ((float)$item->persentase) * 100; }
                }
            }
        }
        
        // Pagination manual
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 50);
        $offset = ($page - 1) * $perPage;
        
        $total = count($data);
        $items = array_slice($data, $offset, $perPage);

        return response()->json([
            'success' => true,
            'data' => $items,
            'total' => $total,
            'current_page' => (int)$page,
            'per_page' => (int)$perPage
        ]);
    }

    /**
     * Get hasil arsip berdasarkan tahun dan npsn (Sama dengan Arsip.php -> hasil() di CodeIgniter)
     */
    public function arsipHasil($tahun, $npsn)
    {
        $sekolah = DB::table('tb_sekolah')->where('npsn', $npsn)->first();
        
        if (!$sekolah) {
            return response()->json([
                'success' => false,
                'message' => 'Sekolah tidak ditemukan'
            ], 404);
        }

        if ($tahun >= 2022) {
            $rawQuery = "SELECT 
            tb_pilihan.no,
            tb_pilihan.nama,
            tb_pilihan.photo,
            (SELECT COUNT(id) FROM tb_siswa_tps WHERE pilihan = tb_pilihan.id) AS jml_pemilih
            FROM tb_pilihan
            WHERE tahun = ? AND npsn = ? 
            ORDER BY jml_pemilih DESC
            ";
            $hasil = DB::select($rawQuery, [$tahun, $npsn]);
            $getJumlahDpt = "SELECT COUNT(id) AS jml_dpt FROM tb_siswa_tps WHERE npsn = ? AND tahun = ?";
            $queryGetJumlahDpt = DB::selectOne($getJumlahDpt, [$npsn, $tahun]);
        } else {
            $getJumlahDpt = "SELECT jml_dpt AS jml_dpt FROM rekap_per_sekolah WHERE id_sekolah = ? AND tahun = ?";
            $queryGetJumlahDpt = DB::selectOne($getJumlahDpt, [$npsn, $tahun]);
            $rawQuery = "SELECT 
            tb_pilihan.no,
            tb_pilihan.nama,
            tb_pilihan.photo,
            (SELECT jumlah_total_suara FROM rekap_hasil WHERE id_pilihan = tb_pilihan.id) AS jml_pemilih
            FROM tb_pilihan
            WHERE tahun = ? AND npsn = ? 
            ORDER BY jml_pemilih DESC
            ";
            $hasil = DB::select($rawQuery, [$tahun, $npsn]);
            $getJumlahDpt = "SELECT jml_dpt AS jml_dpt FROM rekap_per_sekolah WHERE id_sekolah = ? AND tahun = ?";
            $queryGetJumlahDpt = DB::selectOne($getJumlahDpt, [$npsn, $tahun]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => $sekolah,
                'hasil' => $hasil,
                'jumlahDpt' => $queryGetJumlahDpt
            ]
        ]);
    }
}
