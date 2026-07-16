<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class DataDptController extends Controller
{
    // List DPT (tb_siswa_tps yang dijoin ke tb_siswa)
    public function index(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));
        $search = $request->query('cari');
        $filterTps = $request->query('tps_id');

        $query = DB::table('tb_siswa_tps as st')
            ->join('tb_siswa as s', 'st.nisn', '=', 's.nisn')
            ->leftJoin('tb_kelas as k', 'st.id_tps', '=', 'k.kd_kelas')
            ->where('st.npsn', $npsn)
            ->where('st.tahun', $tahun)
            ->select(
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

        if ($filterTps) {
            $query->where('st.id_tps', $filterTps);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('st.nisn', 'like', "%{$search}%")
                  ->orWhere('s.nm_siswa', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(30)
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
        }

        if (count($dataInsert) > 0) {
            DB::table('tb_siswa_tps')->insert($dataInsert);
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

        $request->validate([
            'ids' => 'required|array|min:1' // Array dari id tb_siswa_tps
        ]);

        DB::table('tb_siswa_tps')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data DPT berhasil dihapus'
        ]);
    }
}
