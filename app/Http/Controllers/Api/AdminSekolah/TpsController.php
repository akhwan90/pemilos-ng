<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TpsController extends Controller
{
    // List TPS (tb_kelas)
    public function index(Request $request)
    {
        $npsn = $request->user()->npsn;

        $tps = DB::table('tb_kelas')
            ->where('npsn', $npsn)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tps
        ]);
    }

    // Menambah TPS Baru
    public function store(Request $request)
    {
        $npsn = $request->user()->npsn;

        $request->validate([
            'nm_kelas' => 'required|string|max:32',
            'is_tps_luar_sekolah' => 'required|boolean'
        ]);

        $id = DB::table('tb_kelas')->insertGetId([
            'npsn' => $npsn,
            'nm_kelas' => $request->nm_kelas,
            'is_tps_luar_sekolah' => $request->is_tps_luar_sekolah ? 1 : 0,
            'is_generate_token'=>0,
            'is_hapus' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'TPS berhasil ditambahkan',
            'data' => ['kd_kelas' => $id]
        ]);
    }

    // Update Data TPS
    public function update(Request $request, $kd_kelas)
    {
        $npsn = $request->user()->npsn;

        $request->validate([
            'nm_kelas' => 'required|string|max:32',
            'is_tps_luar_sekolah' => 'required|boolean',
            'is_hapus' => 'required|boolean'
        ]);

        $affected = DB::table('tb_kelas')
            ->where('kd_kelas', $kd_kelas)
            ->where('npsn', $npsn)
            ->update([
                'nm_kelas' => $request->nm_kelas,
                'is_tps_luar_sekolah' => $request->is_tps_luar_sekolah ? 1 : 0,
                'is_hapus' => $request->is_hapus ? 1 : 0
            ]);

        if ($affected) {
            return response()->json([
                'success' => true,
                'message' => 'Data TPS berhasil diperbarui'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'TPS tidak ditemukan atau tidak ada perubahan'], 404);
    }

    // Menghapus TPS (Soft Delete / Hapus Permanen tergantung kebutuhan, saat ini diset soft delete is_hapus=1)
    public function destroy(Request $request, $kd_kelas)
    {
        $npsn = $request->user()->npsn;

        $affected = DB::table('tb_kelas')
            ->where('kd_kelas', $kd_kelas)
            ->where('npsn', $npsn)
            ->update([
                'is_hapus' => 1
            ]);

        if ($affected) {
            return response()->json([
                'success' => true,
                'message' => 'TPS berhasil dihapus'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'TPS tidak ditemukan'], 404);
    }

    // Get Admin TPS
    public function getAdmin(Request $request, $kd_kelas)
    {
        $npsn = $request->user()->npsn;

        $admins = DB::table('tb_admin')
            ->where('npsn', $npsn)
            ->where('level', 3)
            ->where('id_tps', $kd_kelas)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $admins
        ]);
    }

    // Store Admin TPS
    public function storeAdmin(Request $request, $kd_kelas)
    {
        $npsn = $request->user()->npsn;

        $request->validate([
            'username' => 'required|string|min:4|unique:tb_admin,username',
            'password' => 'required|string|min:4',
        ]);

        $id = DB::table('tb_admin')->insertGetId([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'level' => 3,
            'npsn' => $npsn,
            'id_tps' => $kd_kelas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin TPS berhasil ditambahkan',
            'data' => ['id' => $id]
        ]);
    }

    // Delete Admin TPS
    public function destroyAdmin(Request $request, $kd_kelas, $id)
    {
        $npsn = $request->user()->npsn;

        $affected = DB::table('tb_admin')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->where('level', 3)
            ->where('id_tps', $kd_kelas)
            ->delete();

        if ($affected) {
            return response()->json([
                'success' => true,
                'message' => 'Admin TPS berhasil dihapus'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Admin TPS tidak ditemukan'], 404);
    }

    // Update Password Admin TPS
    public function updateAdminPassword(Request $request, $kd_kelas, $id)
    {
        $npsn = $request->user()->npsn;

        $request->validate([
            'password' => 'required|string|min:4',
        ]);

        $affected = DB::table('tb_admin')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->where('level', 3)
            ->where('id_tps', $kd_kelas)
            ->update([
                'password' => Hash::make($request->password)
            ]);

        if ($affected) {
            return response()->json([
                'success' => true,
                'message' => 'Password Admin TPS berhasil diubah'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Admin TPS tidak ditemukan atau password sama'], 404);
    }
}
