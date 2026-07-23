<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataSiswaController extends Controller
{
    public function index(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));
        $search = $request->query('cari');
        $filterKelas = $request->query('kelas');

        $query = DB::table('tb_siswa')
            ->where('npsn', $npsn)
            // ->where('tahun', $tahun)
            ->where('status', 1); // 1 = Aktif, 0 = Terhapus/Nonaktif

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nm_siswa', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if (!empty($filterKelas)) {
            $query->where('kelas', $filterKelas);
        }

        // Pagination
        $siswa = $query->orderBy('kelas')->orderBy('nm_siswa')->paginate(100);

        return response()->json([
            'success' => true,
            'data' => $siswa
        ]);
    }

    public function listKelas(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $kelas = DB::table('tb_siswa')
            ->select('kelas')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('status', 1)
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->pluck('kelas');

        return response()->json([
            'success' => true,
            'data' => $kelas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:100|unique:tb_siswa,nisn',
            'nm_siswa' => 'required|string|max:200',
            'jk' => 'required|integer|in:1,2',
            'kelas' => 'required|string|max:50',
            'difabel' => 'required|integer'
        ]);

        $npsn = $request->user()->npsn;

        DB::table('tb_siswa')->insert([
            'nisn' => $request->nisn,
            'nm_siswa' => $request->nm_siswa,
            'jk' => $request->jk,
            'kelas' => $request->kelas,
            'difabel' => $request->difabel,
            'npsn' => $npsn,
            'tahun' => env('TAHUN_AKTIF', date('Y')),
            'status' => 1,
            'create_at' => date('Y-m-d H:i:s')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Siswa berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        $npsn = $request->user()->npsn;

        $request->validate([
            'nisn' => 'required|string|max:100|unique:tb_siswa,nisn,' . $id,
            'nm_siswa' => 'required|string|max:200',
            'jk' => 'required|integer|in:1,2',
            'kelas' => 'required|string|max:50',
            'difabel' => 'required|integer'
        ]);

        $affected = DB::table('tb_siswa')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->update([
                'nisn' => $request->nisn,
                'nm_siswa' => $request->nm_siswa,
                'jk' => $request->jk,
                'kelas' => $request->kelas,
                'difabel' => $request->difabel,
            ]);

        if ($affected === 0) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan atau tidak ada perubahan'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Siswa berhasil diperbarui'
        ]);
    }

    private function tryDeleteSiswa($id, $npsn, $user_id, $alasan_hapus)
    {
        $siswa = DB::table('tb_siswa')->where('id', $id)->where('npsn', $npsn)->first();
        if (!$siswa) {
            return false;
        }

        $tahunAktif = env('TAHUN_AKTIF', date('Y'));
        $sudahMasukDpt = DB::table('tb_siswa_tps')
            ->where('nisn', $siswa->nisn)
            ->where('tahun', $tahunAktif)
            ->exists();

        if ($sudahMasukDpt) {
            return false;
        }

        return DB::table('tb_siswa')
            ->where('id', $id)
            ->update([
                'status' => $alasan_hapus,
                'npsn' => null, // Lepas ikatan dengan sekolah
                'hapus_time' => date('Y-m-d H:i:s'),
                'hapus_user_id' => $user_id
            ]) > 0;
    }

    public function destroy(Request $request, $id)
    {
        $npsn = $request->user()->npsn;
        $user_id = $request->user()->id; // id admin login

        $request->validate([
            'alasan_hapus' => 'required|integer|in:2,3' // 2: Lulus, 3: Pindah
        ]);

        $success = $this->tryDeleteSiswa($id, $npsn, $user_id, $request->alasan_hapus);

        if (!$success) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan atau siswa sudah masuk di TPS tahun aktif sehingga tidak bisa dihapus.'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Siswa berhasil dihapus'
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $npsn = $request->user()->npsn;
        $user_id = $request->user()->id;

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'alasan_hapus' => 'required|integer|in:2,3' // 2: Lulus, 3: Pindah
        ]);

        $affected = 0;
        $failed = 0;

        foreach ($request->ids as $id) {
            if ($this->tryDeleteSiswa($id, $npsn, $user_id, $request->alasan_hapus)) {
                $affected++;
            } else {
                $failed++;
            }
        }

        $msg = "{$affected} siswa berhasil dihapus.";
        if ($failed > 0) {
            $msg .= "\n"."{$failed} siswa gagal dihapus (mungkin sudah masuk di TPS tahun aktif).";
        }

        return response()->json([
            'success' => true,
            'message' => $msg
        ], ($failed > 0 ? 422 : 200));
    }
}
