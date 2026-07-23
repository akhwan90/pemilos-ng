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

    /**
     * Dapatkan data Perangkat TPS
     */
    public function getPerangkat(Request $request)
    {
        $user = $request->user();
        if ($user->level != 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $tpsSetting = DB::table('tb_tps_setting')
            ->where('npsn', $user->npsn)
            ->where('tahun', env('TAHUN_AKTIF', date('Y')))
            ->where('id_kelas', $user->id_tps)
            ->first();

        $perangkat = null;
        if ($tpsSetting && $tpsSetting->perangkat_tps) {
            $perangkat = json_decode($tpsSetting->perangkat_tps, true);
        }

        // Default structure
        if (!$perangkat) {
            $perangkat = [
                'ketua' => ['nama' => '', 'identitas' => ''],
                'anggota_1' => ['nama' => '', 'identitas' => ''],
                'anggota_2' => ['nama' => '', 'identitas' => ''],
                'saksi' => []
            ];
        }

        // Backward compatibility if saksi missing from old saved JSON
        if (!isset($perangkat['saksi']) || !is_array($perangkat['saksi'])) {
            $perangkat['saksi'] = [];
        }

        return response()->json([
            'success' => true,
            'data' => $perangkat
        ]);
    }

    /**
     * Simpan data Perangkat TPS
     */
    public function savePerangkat(Request $request)
    {
        $user = $request->user();
        if ($user->level != 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'ketua.nama' => 'nullable|string|max:150',
            'ketua.identitas' => 'nullable|string|max:50',
            'anggota_1.nama' => 'nullable|string|max:150',
            'anggota_1.identitas' => 'nullable|string|max:50',
            'anggota_2.nama' => 'nullable|string|max:150',
            'anggota_2.identitas' => 'nullable|string|max:50',
            'saksi' => 'nullable|array',
            'saksi.*.nama' => 'nullable|string|max:150',
            'saksi.*.identitas' => 'nullable|string|max:50',
            'saksi.*.paslon' => 'nullable|string|max:150',
        ]);

        $npsn = $user->npsn;
        $tpsId = $user->id_tps;
        $tahun = env('TAHUN_AKTIF', date('Y'));
        $now = date('Y-m-d H:i:s');

        // Prepare the dynamic Saksi structure
        $inputSaksi = $request->input('saksi', []);
        if (!is_array($inputSaksi)) {
            $inputSaksi = [];
        }

        $perangkatData = [
            'ketua' => $request->input('ketua', ['nama' => '', 'identitas' => '']),
            'anggota_1' => $request->input('anggota_1', ['nama' => '', 'identitas' => '']),
            'anggota_2' => $request->input('anggota_2', ['nama' => '', 'identitas' => '']),
            'saksi' => $inputSaksi
        ];

        $jsonPerangkat = json_encode($perangkatData);

        $tpsSetting = DB::table('tb_tps_setting')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('id_kelas', $tpsId)
            ->first();

        if ($tpsSetting) {
            DB::table('tb_tps_setting')
                ->where('id', $tpsSetting->id)
                ->update(['perangkat_tps' => $jsonPerangkat]);
        } else {
            DB::table('tb_tps_setting')->insert([
                'npsn' => $npsn,
                'tahun' => $tahun,
                'id_kelas' => $tpsId,
                'is_generate_token' => 0,
                'is_cetak_ba' => 0,
                'perangkat_tps' => $jsonPerangkat,
                'created_at' => $now
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data perangkat TPS berhasil disimpan.',
            'data' => $perangkatData
        ]);
    }

    /**
    * Dapatkan config laporan C2 TPS
    */
    public function getC2(Request $request)
    {
        $user = $request->user();
        if ($user->level != 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $tpsSetting = DB::table('tb_tps_setting')
            ->where('npsn', $user->npsn)
            ->where('tahun', env('TAHUN_AKTIF', date('Y')))
            ->where('id_kelas', $user->id_tps)
            ->first();

        $c2Config = null;
        if ($tpsSetting && $tpsSetting->form_c2_config) {
            $c2Config = json_decode($tpsSetting->form_c2_config, true);
        }

        if (!$c2Config) {
            $c2Config = [
                'ada_kejadian' => false,
                'kejadian' => []
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $c2Config
        ]);
    }

    /**
     * Simpan config laporan C2 TPS
     */
    public function saveC2(Request $request)
    {
        $user = $request->user();
        if ($user->level != 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'ada_kejadian' => 'required|boolean',
            'kejadian' => 'nullable|array',
            'kejadian.*.waktu' => 'nullable|string|max:10',
            'kejadian.*.pelapor' => 'nullable|string|max:150',
            'kejadian.*.uraian' => 'nullable|string'
        ]);

        $npsn = $user->npsn;
        $tpsId = $user->id_tps;
        $tahun = env('TAHUN_AKTIF', date('Y'));
        $now = date('Y-m-d H:i:s');

        // Prepare the config data
        $adaKejadian = filter_var($request->input('ada_kejadian'), FILTER_VALIDATE_BOOLEAN);
        $kejadian = $request->input('kejadian', []);
        
        if (!is_array($kejadian)) {
            $kejadian = [];
        }

        // If not ada_kejadian, force clear the list
        if (!$adaKejadian) {
            $kejadian = [];
        }

        $c2Data = [
            'ada_kejadian' => $adaKejadian,
            'kejadian' => $kejadian
        ];

        $jsonC2 = json_encode($c2Data);

        $tpsSetting = DB::table('tb_tps_setting')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('id_kelas', $tpsId)
            ->first();

        if ($tpsSetting) {
            DB::table('tb_tps_setting')
                ->where('id', $tpsSetting->id)
                ->update(['form_c2_config' => $jsonC2]);
        } else {
            DB::table('tb_tps_setting')->insert([
                'npsn' => $npsn,
                'tahun' => $tahun,
                'id_kelas' => $tpsId,
                'is_generate_token' => 0,
                'is_cetak_ba' => 0,
                'form_c2_config' => $jsonC2,
                'created_at' => $now
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data laporan C2 berhasil disimpan.',
            'data' => $c2Data
        ]);
    }
}
