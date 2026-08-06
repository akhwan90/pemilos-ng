<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Services\GenerateTokenService;
use App\Services\WaktuPemilihanService;

class DataDptController extends Controller
{
    protected $generateTokenService;
    protected $waktuPemilihanService;

    public function __construct(GenerateTokenService $generateTokenService, WaktuPemilihanService $waktuPemilihanService)
    {
        $this->generateTokenService = $generateTokenService;
        $this->waktuPemilihanService = $waktuPemilihanService;
    }
    // List DPT (tb_siswa_tps yang dijoin ke tb_siswa)
    public function index(Request $request)
    {
        $npsn = $request->user()->npsn;
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

        $limit = $request->query('limit', 30);
        $data = $query->paginate($limit);

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

    // List siswa yang belum masuk DPT
    public function siswaBelumDpt(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        // Ambil NISN yang SUDAH masuk DPT pada tahun ini
        $nisnSudahDpt = DB::table('tb_siswa_tps')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->pluck('nisn');

        // Cari siswa (tb_siswa) yang tidak ada di dalam $nisnSudahDpt (Belum DPT) dan berstatus aktif
        $siswaBelumDpt = DB::table('tb_siswa')
            ->where('npsn', $npsn)
            ->whereIn('status', [1, 4]) // Siswa aktif
            ->whereNotIn('nisn', $nisnSudahDpt)
            ->select('id', 'nisn', 'nm_siswa', 'kelas', 'jk')
            ->orderBy('kelas')
            ->orderBy('nm_siswa')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $siswaBelumDpt
        ]);
    }

    // List TPS (tb_kelas) yang aktif (is_hapus = 0)
    public function listTpsAktif(Request $request)
    {
        $npsn = $request->user()->npsn;

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

    // Proses Bulk Insert ke DPT
    public function storeBulk(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $cek = $this->waktuPemilihanService->cekJadwalBuka('input_data_dpt', $tahun, $npsn);
        if (!$cek['is_open']) {
            return response()->json(['success' => false, 'message' => 'Penambahan DPT ditolak: ' . $cek['message']], 403);
        }

        $request->validate([
            'id_tps' => 'required|integer',
            'siswa_nisn' => 'required|array|min:1' // Array dari nisn siswa
        ]);

        $id_tps = $request->id_tps;
        $nisnList = $request->siswa_nisn;

        // Validasi tambahan agar tidak terjadi duplikasi race condition
        $nisnSudahDpt = DB::table('tb_siswa_tps')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->whereIn('nisn', $nisnList)
            ->pluck('nisn')
            ->toArray();

        $dataInsert = [];
        $nisnOk = [];
        foreach ($nisnList as $nisn) {
            // Skip yang sudah masuk dpt
            if (in_array($nisn, $nisnSudahDpt)) continue;

            // Perlu JK karena tb_siswa_tps punya field jk yg not null
            $siswa = DB::table('tb_siswa')->where('npsn', $npsn)->where('nisn', $nisn)->first();
            if (!$siswa) continue;

            $dataInsert[] = [
                'npsn' => $npsn,
                'nisn' => $nisn,
                'id_tps' => $id_tps,
                'tahun' => $tahun,
                'jk' => $siswa->jk,
                'token' => null,
                'pilihan' => null,
                'waktu_pilih' => null
            ];

            $nisnOk[] = $nisn;
        }

        if (count($dataInsert) > 0) {
            DB::table('tb_siswa_tps')->insert($dataInsert);

            $activityService = new \App\Services\ActivityService();
            $activityService->logActivity($request->user()->username, 28, json_encode([
                'tps' => $id_tps,
                'jumlah' => count($dataInsert),
                'nisn' => $nisnOk,
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => count($dataInsert) . ' siswa berhasil ditambahkan ke dalam DPT TPS.'
        ]);
    }

    // Hapus dari DPT (Satuan/Bulk)
    public function destroyBulk(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $cek = $this->waktuPemilihanService->cekJadwalBuka('input_data_dpt', $tahun, $npsn);
        if (!$cek['is_open']) {
            return response()->json(['success' => false, 'message' => 'Penghapusan DPT ditolak: ' . $cek['message']], 403);
        }

        $request->validate([
            'ids' => 'required|array|min:1' // Array dari id tb_siswa_tps
        ]);

        // Cek apakah ada di antara ID yang dipilih sudah memiliki token (sudah generate token)
        $hasToken = DB::table('tb_siswa_tps')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->whereIn('id', $request->ids)
            ->whereNotNull('token')
            ->exists();

        if ($hasToken) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus DPT! Terdapat siswa yang sudah di-generate tokennya.'
            ], 422);
        }

        DB::table('tb_siswa_tps')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->whereIn('id', $request->ids)
            ->delete();


        $activityService = new \App\Services\ActivityService();
        $activityService->logActivity($request->user()->username, 18, json_encode([
            'jumlah' => count($request->ids),
            'nisn' => $request->ids
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Data DPT berhasil dihapus'
        ]);
    }

    /**
     * Endpoint untuk Generate Token DPT per TPS
     */
    public function generateToken(Request $request)
    {
        $user = $request->user();
        $npsn = $user->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));


        $cek = $this->waktuPemilihanService->cekJadwalBuka('generate_token', $tahun, $npsn);
        if (!$cek['is_open']) {
            return response()->json(['success' => false, 'message' => 'Gagal generate token: ' . $cek['message']], 403);
        }

        // Jika user adalah admin sekolah (level 2), mereka harus menyertakan id_tps di body
        // Jika user adalah admin tps (level 3), id_tps otomatis diambil dari field id_tps mereka sendiri
        $idTps = $user->level == 3 ? $user->id_tps : $request->input('id_tps');

        if (!$idTps) {
            return response()->json([
                'success' => false,
                'message' => 'ID TPS harus disertakan.'
            ], 422);
        }

        $result = $this->generateTokenService->generateForTps($idTps, $npsn, $tahun, $user);

        if ($result['success']) {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }

    /**
     * Endpoint untuk Batal Generate Token DPT per TPS
     */
    public function cancelToken(Request $request)
    {
        $user = $request->user();
        $npsn = $user->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));


        $cek = $this->waktuPemilihanService->cekJadwalBuka('generate_token', $tahun, $npsn);
        if (!$cek['is_open']) {
            return response()->json(['success' => false, 'message' => 'Gagal generate token: ' . $cek['message']], 403);
        }

        $idTps = $user->level == 3 ? $user->id_tps : $request->input('id_tps');

        if (!$idTps) {
            return response()->json([
                'success' => false,
                'message' => 'ID TPS harus disertakan.'
            ], 422);
        }

        // Cek apakah ada siswa di TPS ini yang sudah terlanjur memilih.
        // Jika ada, maka rollback token tidak diizinkan.
        $hasVoted = DB::table('tb_siswa_tps')
            ->where('id_tps', $idTps)
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->whereNotNull('pilihan')
            ->exists();

        if ($hasVoted) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan token! Terdapat pemilih di TPS ini yang sudah menggunakan hak pilihnya (mencoblos).'
            ], 422);
        }

        $result = $this->generateTokenService->cancelForTps($idTps, $npsn, $tahun, $user);

        if ($result['success']) {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }
}
