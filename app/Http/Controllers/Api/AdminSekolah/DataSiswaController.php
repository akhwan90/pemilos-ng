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

        $query = DB::table('tb_siswa')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('status', 1); // 1 = Aktif, 0 = Terhapus/Nonaktif

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nm_siswa', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        // Pagination
        $siswa = $query->orderBy('kelas')->orderBy('nm_siswa')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $siswa
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

    public function destroy(Request $request, $id)
    {
        $npsn = $request->user()->npsn;
        $user_id = $request->user()->id; // id admin login

        $affected = DB::table('tb_siswa')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->update([
                'status' => 0, // Soft delete
                'hapus_time' => date('Y-m-d H:i:s'),
                'hapus_user_id' => $user_id
            ]);

        if ($affected === 0) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Siswa berhasil dihapus'
        ]);
    }
}
