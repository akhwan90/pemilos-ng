<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataSiswaGlobalController extends Controller
{
    public function index(Request $request)
    {
        // Hanya boleh diakses Super Admin (Level 1)
        if ($request->user()->level != 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tahun = env('TAHUN_AKTIF', date('Y'));
        $search = $request->query('cari');
        $filterNpsn = $request->query('npsn');

        $query = DB::table('tb_siswa')
            ->join('tb_sekolah', 'tb_siswa.npsn', '=', 'tb_sekolah.npsn')
            ->select('tb_siswa.*', 'tb_sekolah.nama_sekolah')
            ->where('tb_siswa.status', 1);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('tb_siswa.nm_siswa', 'like', "%{$search}%")
                  ->orWhere('tb_siswa.nisn', 'like', "%{$search}%")
                  ->orWhere('tb_siswa.kelas', 'like', "%{$search}%");
            });
        }

        if (!empty($filterNpsn)) {
            $query->where('tb_siswa.npsn', $filterNpsn);
        }

        $siswa = $query->orderBy('tb_sekolah.nama_sekolah', 'asc')
                      ->orderBy('tb_siswa.kelas', 'asc')
                      ->orderBy('tb_siswa.nm_siswa', 'asc')
                      ->paginate(100);

        return response()->json($siswa);
    }

    public function destroy(Request $request, $id)
    {
        // Hanya boleh diakses Super Admin (Level 1)
        if ($request->user()->level != 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $deleted = DB::table('tb_siswa')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Data siswa berhasil dihapus secara permanen.']);
        }

        return response()->json(['message' => 'Data siswa tidak ditemukan.'], 404);
    }
}
