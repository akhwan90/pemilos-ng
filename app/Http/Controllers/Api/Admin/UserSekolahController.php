<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserSekolahController extends Controller
{
    // Mengambil list user (level 2) berdasarkan NPSN
    public function index(Request $request, $npsn)
    {
        $users = DB::table('tb_admin')
            ->where('npsn', $npsn)
            // Di CI3, level 2 adalah admin sekolah
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    // Menambah user baru
    public function store(Request $request, $npsn)
    {
        // Validasi
        $request->validate([
            'username' => 'required|string|min:6|unique:tb_admin,username',   
            'level'    => 'required|in:2,3,4',         
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/[A-Z]/',
                'regex:/[!#_@$]/'
            ],
        ], [
            'password.min' => 'Password minimal harus :min karakter.',
            'password.regex' => 'Password harus mengandung minimal satu huruf besar dan satu karakter spesial (!#_@$).',
        ]);

        $id = DB::table('tb_admin')->insertGetId([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'level'    => $request->level, // 2 (Admin Sekolah) atau 3 (Admin TPS)
            'npsn'     => $npsn,
            'id_tps'   => 0, // default di CI3
            'level_4_kewenangan' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => ['id' => $id]
        ]);
    }

    // Mereset password user
    public function update(Request $request, $npsn, $id)
    {
        $request->validate([       
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/[A-Z]/',
                'regex:/[!#_@$]/'
            ],
        ], [
            'password.min' => 'Password minimal harus :min karakter.',
            'password.regex' => 'Password harus mengandung minimal satu huruf besar dan satu karakter spesial (!#_@$).',
        ]);

        $affected = DB::table('tb_admin')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->update([
                'password' => Hash::make($request->password)
            ]);

        if ($affected) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
    }

    // Menghapus user
    public function destroy($npsn, $id)
    {
        $affected = DB::table('tb_admin')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->delete();

        if ($affected) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
    }
}
