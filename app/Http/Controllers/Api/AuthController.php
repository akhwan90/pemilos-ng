<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Handle Admin/Sekolah Login API (Sanctum)
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek admin by username
        $admin = Admin::where('username', $request->username)->first();

        // Verifikasi password (Mendukung legacy MD5 dari CI3 dan bcrypt baru)
        if (!$admin) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        $passwordRaw = $request->password;
        $legacyMd5 = md5($passwordRaw);
        
        $isValidPassword = false;
        
        // Kondisi 1: Hash bawaan Laravel (bcrypt)
        if (Hash::check($passwordRaw, $admin->password)) {
            $isValidPassword = true;
        } 
        // Kondisi 2: Legacy MD5 langsung
        else if ($admin->password === $legacyMd5) {
            $isValidPassword = true;
        }

        if (!$isValidPassword) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }
        
        // Cek jika ini level 2 (Admin Sekolah), pastikan sekolahnya tidak dihapus
        $sekolahInfo = null;
        if ((int) $admin->level === 2) {
            $sekolahInfo = Sekolah::where('npsn', $admin->npsn)
                                  ->where('is_delete', 0)
                                  ->first();
                                  
            if (!$sekolahInfo) {
                throw ValidationException::withMessages([
                    'username' => ['Sekolah tidak ditemukan atau sudah dihapus.'],
                ]);
            }
        }

        // Buat token baru via Sanctum
        $token = $admin->createToken('admin-token')->plainTextToken;
        
        Log::info('Login API berhasil: ' . $admin->username);

        // Jika dia Admin TPS (Level 3), ambil info tambahan apakah ini TPS Luar Sekolah
        $isTpsLuarSekolah = 0;
        if ((int) $admin->level === 3 && !empty($admin->id_tps)) {
            $tpsInfo = DB::table('tb_kelas')->where('kd_kelas', $admin->id_tps)->first();
            if ($tpsInfo) {
                $isTpsLuarSekolah = $tpsInfo->is_tps_luar_sekolah ?? 0;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'level' => (int) $admin->level,
                    'npsn' => $admin->npsn,
                    'id_tps' => $admin->id_tps,
                    'is_tps_luar_sekolah' => $isTpsLuarSekolah
                ],
                'sekolah' => $sekolahInfo, // Akan null jika bukan level 2
                'token' => $token
            ]
        ]);
    }
    
    /**
     * Get Current Logged In Admin Profile
     */
    public function me(Request $request)
    {
        $admin = $request->user();
        $sekolahInfo = null;
        
        if ((int) $admin->level === 2) {
            $sekolahInfo = Sekolah::where('npsn', $admin->npsn)->first();
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $admin,
                'sekolah' => $sekolahInfo
            ]
        ]);
    }

    /**
     * Handle Logout API (Revoke Token)
     */
    public function logout(Request $request)
    {
        // Revoke token yang sedang dipakai ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
