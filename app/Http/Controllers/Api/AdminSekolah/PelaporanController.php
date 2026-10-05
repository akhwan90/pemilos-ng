<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelaporanController extends Controller
{
    public function getPelaporan(Request $request)
    {
        $user = $request->user();
        if ($user->level != 2) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Access.'], 403);
        }

        $npsn = $user->npsn;
        $tahun = env('APP_TAHUN_AKTIF', date('Y'));

        $setting = DB::table('tb_sekolah_settings')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->first();

        return response()->json([
            'success' => true,
            'pelaporan' => $setting ? $setting->pelaporan : null
        ]);
    }

    public function savePelaporan(Request $request)
    {
        $user = $request->user();
        
        if ($user->level != 2) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Access.'], 403);
        }

        $npsn = $user->npsn;
        $tahun = env('APP_TAHUN_AKTIF', date('Y'));
        
        $request->validate([
            'pelaporan' => 'required|json',
        ]);

        $exists = DB::table('tb_sekolah_settings')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->exists();

        if ($exists) {
            DB::table('tb_sekolah_settings')
                ->where('npsn', $npsn)
                ->where('tahun', $tahun)
                ->update([
                    'pelaporan' => $request->pelaporan,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('tb_sekolah_settings')->insert([
                'npsn' => $npsn,
                'tahun' => $tahun,
                'pelaporan' => $request->pelaporan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pelaporan berhasil disimpan.'
        ]);
    }
}
