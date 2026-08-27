<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class DataUserController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('tb_admin')
            ->leftJoin('tb_sekolah', 'tb_admin.npsn', '=', 'tb_sekolah.npsn')
            ->leftJoin('tb_kelas', 'tb_admin.id_tps', '=', 'tb_kelas.kd_kelas')
            ->select(
                'tb_admin.id',
                'tb_admin.username',
                'tb_admin.level',
                'tb_admin.password',
                'tb_sekolah.nama_sekolah',
                'tb_kelas.nm_kelas'
            );

        if ($request->has('cari') && !empty($request->cari)) {
            $query->where('tb_admin.username', 'like', '%' . $request->cari . '%');
        }

        if ($request->has('level') && !empty($request->level)) {
            $query->where('tb_admin.level', $request->level);
        }

        $query->where('tb_admin.id', '!=', $request->user()->id);

        $users = $query->orderBy('tb_admin.level', 'asc')
                       ->orderBy('tb_admin.username', 'asc')
                       ->paginate(100);

        $users->getCollection()->transform(function ($item) {
            $isBcrypt = str_starts_with($item->password, '$2y$');
            return [
                'id' => $item->id,
                'username' => $item->username,
                'level' => $item->level,
                'nama_sekolah' => $item->nama_sekolah ?? '-',
                'nm_kelas' => $item->nm_kelas ?? '-',
                'status_password' => $isBcrypt ? 'Sudah (Bcrypt)' : 'Belum (MD5/Plain)',
                'level_4_kewenangan' => $item->level == 4 ? 'Semua Sekolah' : null
            ];
        });

        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'password' => [
                'nullable',
                'string',
                'min:6',
                'regex:/[A-Z]/',
                'regex:/[!#_@$]/'
            ],
        ], [
            'password.min' => 'Password minimal harus :min karakter.',
            'password.regex' => 'Password harus mengandung minimal satu huruf besar dan satu karakter spesial (!#_@$).',
        ]);

        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Data user tidak ditemukan.',
            ], 404);
        }

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
            $admin->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diperbarui.',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Data user tidak ditemukan.',
            ], 404);
        }

        if ($admin->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa menghapus akun Anda sendiri.',
            ], 400);
        }

        $admin->tokens()->delete();
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.',
        ]);
    }
}
