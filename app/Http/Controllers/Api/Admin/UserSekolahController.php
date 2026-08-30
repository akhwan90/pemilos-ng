<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
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
    public function store(Request $request, $npsn, ActivityService $activityService)
    {
        // return response()->json($request->user());    

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

        $userData = [
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'level'    => $request->level, // 2 (Admin Sekolah) atau 3 (Admin TPS)
            'npsn'     => $npsn,
            'id_tps'   => 0, // default di CI3
            'level_4_kewenangan' => null
        ];
        $id = DB::table('tb_admin')->insertGetId($userData);

        $username = $request->user()->username;
        $activityService->logActivity($username, 40, json_encode([
            'id' => $id,
            'username' => $request->username,
            'level' => $request->level,
            'npsn' => $npsn,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => ['id' => $id]
        ]);
    }

    // Mereset password user
    public function update(Request $request, $npsn, $id, ActivityService $activityService)
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
            $username = $request->user()->username;
            $userDiubah = DB::table('tb_admin')->where('id', $id)->first();

            $activityService->logActivity($username, 41, json_encode([
                'id' => $id,
                'username' => $userDiubah->username,
                'npsn' => $npsn,
                'level' => $userDiubah->level
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
    }

    // Menghapus user
    public function destroy(Request $request, $npsn, $id, ActivityService $activityService)
    {
        $userDiubah = DB::table('tb_admin')->where('id', $id)->first();

        $affected = DB::table('tb_admin')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->delete();

        if ($affected) {
            $username = $request->user()->username;

            $activityService->logActivity($username, 42, json_encode([
                'id' => $id,
                'username' => $userDiubah->username,
                'npsn' => $npsn,
                'level' => $userDiubah->level
            ]));


            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
    }
}
