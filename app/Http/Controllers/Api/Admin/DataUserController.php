<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataUserController extends Controller
{
    public function index(Request $request)
    {
        // Hanya boleh diakses Super Admin (Level 1)
        if ($request->user()->level != 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $search = $request->query('cari');
        $filterLevel = $request->query('level');

        $query = DB::table('tb_admin')
        ->leftJoin('tb_sekolah', 'tb_admin.npsn', '=', 'tb_sekolah.npsn')
        ->leftJoin('tb_kelas', 'tb_admin.id_tps', '=', 'tb_kelas.kd_kelas')
        ->select(
            'tb_admin.id',
            'tb_admin.username',
            'tb_admin.level',
            'tb_admin.npsn',
            'tb_admin.id_tps',
            'tb_admin.level_4_kewenangan',
            'tb_sekolah.nama_sekolah',
            'tb_kelas.nm_kelas',
            DB::raw("IF(LEFT(password, 1) = '$', 1, 0) AS status_password")
        )
        ;

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('tb_admin.username', 'like', "%{$search}%")
                ;
            });
        }

        if (!empty($filterLevel)) {
            $query->where('tb_admin.level', intval($filterLevel));
        }

        $siswa = $query->orderBy('tb_admin.npsn', 'asc')
                      ->orderBy('tb_admin.level', 'asc')
                      ->orderBy('tb_admin.username', 'asc')
                      ->paginate(100)
                      ;

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
