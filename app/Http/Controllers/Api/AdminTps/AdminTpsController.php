<?php

namespace App\Http\Controllers\Api\AdminTps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTpsController extends Controller
{
    /**
     * Dapatkan status pemilihan TPS saat ini
     */
    public function getStatus(Request $request)
    {
        $user = $request->user();
        
        // Pastikan level 3 (Admin TPS)
        if ($user->level != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access.'
            ], 403);
        }

        $npsn = $user->npsn;
        $tpsId = $user->id_tps;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $tpsSetting = DB::table('tb_tps_setting')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('id_kelas', $tpsId)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'selesai_pemilihan_time' => $tpsSetting ? $tpsSetting->selesai_pemilihan_time : null
            ]
        ]);
    }

    /**
     * Tandai pemilihan di TPS sebagai selesai
     */
    public function akhiriPemilihan(Request $request)
    {
        $user = $request->user();
        
        if ($user->level != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access.'
            ], 403);
        }

        $npsn = $user->npsn;
        $tpsId = $user->id_tps;
        $tahun = env('TAHUN_AKTIF', date('Y'));
        $now = date('Y-m-d H:i:s');

        $tpsSetting = DB::table('tb_tps_setting')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('id_kelas', $tpsId)
            ->first();

        if ($tpsSetting) {
            // Update
            if ($tpsSetting->selesai_pemilihan_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemilihan TPS sudah ditandai selesai sebelumnya.'
                ], 400);
            }

            DB::table('tb_tps_setting')
                ->where('id', $tpsSetting->id)
                ->update([
                    'selesai_pemilihan_time' => $now
                ]);
        } else {
            // Jika belum ada row setting, buat baru
            DB::table('tb_tps_setting')->insert([
                'npsn' => $npsn,
                'tahun' => $tahun,
                'id_kelas' => $tpsId,
                'is_generate_token' => 0,
                'is_cetak_ba' => 0,
                'selesai_pemilihan_time' => $now,
                'created_at' => $now
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pemilihan di TPS ini berhasil diakhiri.',
            'selesai_time' => $now
        ]);
    }
}
