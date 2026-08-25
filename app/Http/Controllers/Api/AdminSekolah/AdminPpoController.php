<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPpoController extends Controller
{
    /**
     * Dapatkan status pemilihan TPS saat ini
     */
    public function getStatus(Request $request)
    {
        $user = $request->user();

        // Pastikan level 3 (Admin TPS)
        if ($user->level != 2) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access.'
            ], 403);
        }

        $npsn = $user->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $tpsSetting = DB::table('tb_sekolah_settings')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'selesai_pemilihan_time' => $tpsSetting ? $tpsSetting->selesai_at : null
            ]
        ]);
    }

    /**
     * Tandai pemilihan di TPS sebagai selesai
     */
    public function akhiriPemilihan(Request $request)
    {
        $user = $request->user();

        if ($user->level != 2) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access.'
            ], 403);
        }

        $npsn = $user->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));
        $now = now();

        $tpsSetting = DB::table('tb_sekolah_settings')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->first();



        $getTps = DB::table('tb_kelas')
            ->where('npsn', $npsn)
            ->where('is_hapus', 0)
            ->get();

        $hasilTps = [];
        // 3. Jika belum di-generate (pertama kali buka setelah klik Selesai Pemilihan), hitung dan simpan
        foreach ($getTps as $tps) {
            $tpsId = $tps->kd_kelas;

            // b. Hitung Suara Masuk & DPT
            $totalDpt = DB::table('tb_siswa_tps')
                ->where('npsn', $npsn)
                ->where('id_tps', $tpsId)
                ->where('tahun', $tahun)
                ->select(
                    DB::raw('CAST(SUM(CASE WHEN jk = 1 THEN 1 ELSE 0 END) AS SIGNED) as jumlah_l'),
                    DB::raw('CAST(SUM(CASE WHEN jk = 2 THEN 1 ELSE 0 END) AS SIGNED) as jumlah_p'),
                    DB::raw('CAST(COUNT(id) AS SIGNED) as total')
                )
                ->get()
                ->toArray();

            $suaraMasuk = DB::table('tb_siswa_tps')
                ->where('npsn', $npsn)
                ->where('id_tps', $tpsId)
                ->where('tahun', $tahun)
                ->whereNotNull('waktu_pilih')
                ->select(
                    DB::raw('CAST(SUM(CASE WHEN jk = 1 THEN 1 ELSE 0 END) AS SIGNED) as jumlah_l'),
                    DB::raw('CAST(SUM(CASE WHEN jk = 2 THEN 1 ELSE 0 END) AS SIGNED) as jumlah_p'),
                    DB::raw('CAST(COUNT(id) AS SIGNED) as total')
                )
                ->get()
                ->toArray();

            $difable = DB::table('tb_siswa_tps')
                ->where('npsn', $npsn)
                ->where('id_tps', $tpsId)
                ->where('tahun', $tahun)
                ->where('difabel', '!=', 0)
                ->select(
                    DB::raw('CAST(IFNULL(SUM(CASE WHEN jk = 1 THEN 1 ELSE 0 END), 0) AS SIGNED) as jumlah_l'),
                    DB::raw('CAST(IFNULL(SUM(CASE WHEN jk = 2 THEN 1 ELSE 0 END), 0) AS SIGNED) as jumlah_p'),
                    DB::raw('CAST(COUNT(id) AS SIGNED) as total')
                )
                ->get()
                ->toArray();

            $difableMemilih = DB::table('tb_siswa_tps')
                ->where('npsn', $npsn)
                ->where('id_tps', $tpsId)
                ->where('tahun', $tahun)
                ->whereNotNull('waktu_pilih')
                ->where('difabel', '!=', 0)
                ->select(
                    DB::raw('CAST(IFNULL(SUM(CASE WHEN jk = 1 THEN 1 ELSE 0 END), 0) AS SIGNED) as jumlah_l'),
                    DB::raw('CAST(IFNULL(SUM(CASE WHEN jk = 2 THEN 1 ELSE 0 END), 0) AS SIGNED) as jumlah_p'),
                    DB::raw('CAST(COUNT(id) AS SIGNED) as total')
                )
                ->get()
                ->toArray();

            $perolehanPaslon = DB::table('tb_siswa_tps')
                ->join('tb_pilihan as b', 'tb_siswa_tps.pilihan', '=', 'b.id')
                ->where('tb_siswa_tps.tahun', $tahun)
                ->where('tb_siswa_tps.id_tps', $tpsId)
                ->where('tb_siswa_tps.npsn', $npsn)
                ->select(
                    'b.nama',
                    'b.no',
                    DB::raw('CAST(SUM(CASE WHEN tb_siswa_tps.jk = 1 THEN 1 ELSE 0 END) AS SIGNED) as jumlah_l'),
                    DB::raw('CAST(SUM(CASE WHEN tb_siswa_tps.jk = 2 THEN 1 ELSE 0 END) AS SIGNED) as jumlah_p'),
                    DB::raw('CAST(COUNT(tb_siswa_tps.id) AS SIGNED) as total')
                )
                ->groupBy('b.id', 'b.nama', 'b.no')
                ->get()
                ->toArray();

            // c. Hitung Statistik
            $totalDptJumlah = array_sum(array_column($totalDpt, 'total'));
            $suaraMasukJumlah = array_sum(array_column($suaraMasuk, 'total')); // is_memilih != null
            $totalSuaraSah = array_sum(array_column($perolehanPaslon, 'total'));

            $hasilLengkap = [
                'statistik' => [
                    'total_dpt' => $totalDptJumlah,
                    'suara_masuk' => $suaraMasukJumlah,
                    'suara_sah' => $totalSuaraSah,
                    'suara_tidak_sah' => $suaraMasukJumlah - $totalSuaraSah,
                    'tidak_memilih' => $totalDptJumlah - $suaraMasukJumlah
                ],
                'total_dpt' => $totalDpt,
                'suara_masuk' => $suaraMasuk,
                'perolehan_paslon' => $perolehanPaslon,
                'difabel' => $difable,
                'difabel_memilih' => $difableMemilih,
                'generated_at' => date('Y-m-d H:i:s')
            ];

            $hasilTps[] = [
                'tps_id' => $tpsId,
                'nama_tps' => $tps->nm_kelas,
                'is_tps_luar_sekolah' => $tps->is_tps_luar_sekolah,
                'hasil' => $hasilLengkap,
            ];
        }

        if ($tpsSetting) {
            // Update
            if ($tpsSetting->selesai_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemilihan TPS sudah ditandai selesai sebelumnya.'
                ], 400);
            }

            // Simpan snapshot hasil JSON secara permanen agar nilainya terkunci saat pemilihan selesai
            DB::table('tb_sekolah_settings')
                ->where('id', $tpsSetting->id)
                ->update([
                    'selesai_at' => $now,
                    'hasil' => json_encode($hasilTps),
                    'updated_at' => $now
                ]);

        } else {            
            // Jika belum ada row setting, buat baru
            DB::table('tb_sekolah_settings')->insert([
                'npsn' => $npsn,
                'tahun' => $tahun,
                'selesai_at' => $now,
                'hasil' => json_encode($hasilTps),
                'created_at' => $now
            ]);
        }

        $activityService = new \App\Services\ActivityService();
        $activityService->logActivity($user->username, 30, json_encode([
            'npsn' => $npsn,
            'tahun' => $tahun,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Pemilihan di Sekolah ini berhasil diakhiri.',
            'selesai_time' => $now
        ]);
    }

    /**
     * Dapatkan data Perangkat TPS
     */
    public function getPerangkat(Request $request)
    {
        $user = $request->user();
        if ($user->level != 2) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $tpsSetting = DB::table('tb_sekolah_settings')
            ->where('npsn', $user->npsn)
            ->where('tahun', env('TAHUN_AKTIF', date('Y')))
            ->first();

        $perangkat = null;
        if ($tpsSetting && $tpsSetting->ppo) {
            $perangkat = json_decode($tpsSetting->ppo, true);
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
        if ($user->level != 2) {
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
        $tahun = env('TAHUN_AKTIF', date('Y'));
        $now = now();

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

        $tpsSetting = DB::table('tb_sekolah_settings')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->first();

        if ($tpsSetting) {
            DB::table('tb_sekolah_settings')
                ->where('id', $tpsSetting->id)
                ->update(['ppo' => $jsonPerangkat]);
        } else {
            DB::table('tb_sekolah_settings')->insert([
                'npsn' => $npsn,
                'tahun' => $tahun,
                'ppo'=>$jsonPerangkat,
                'created_at' => $now
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data anggota PPO berhasil disimpan.',
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

        // Ambil juga info TPS dan Perangkat TPS agar tidak perlu request terpisah
        $namaKelas = DB::table('tb_kelas')->where('kd_kelas', $user->id_tps)->value('nm_kelas');
        $perangkatTps = null;
        if ($tpsSetting && $tpsSetting->perangkat_tps) {
            $perangkatTps = json_decode($tpsSetting->perangkat_tps, true);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'c2_config' => $c2Config,
                'tps_info' => [
                    'nama_kelas' => $namaKelas,
                    'tahun' => env('TAHUN_AKTIF', date('Y'))
                ],
                'perangkat_tps' => $perangkatTps
            ]
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

    /**
     * Dapatkan Laporan Hasil C1 (Hanya bisa diakses jika pemilihan telah diakhiri)
     */
    public function getHasilC1(Request $request)
    {
        $user = $request->user();
        if ($user->level != 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $npsn = $user->npsn;
        $tpsId = $user->id_tps;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $tpsSetting = DB::table('tb_tps_setting')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('id_kelas', $tpsId)
            ->first();

        // 1. Pastikan pemilihan sudah selesai
        if (!$tpsSetting || !$tpsSetting->selesai_pemilihan_time) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan Hasil C1 belum tersedia. Anda harus mengakhiri pemilihan terlebih dahulu pada menu Selesai Pemilihan.'
            ], 400);
        }

        $hasilLengkap = json_decode($tpsSetting->hasil, true);

        // Ambil info nama kelas (TPS)
        $namaKelas = DB::table('tb_kelas')->where('kd_kelas', $tpsId)->value('nm_kelas');

        $perangkatTps = null;
        if ($tpsSetting->perangkat_tps) {
            $perangkatTps = json_decode($tpsSetting->perangkat_tps, true);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tps' => $namaKelas,
                'waktu_selesai' => $tpsSetting->selesai_pemilihan_time,
                'hasil' => $hasilLengkap,
                'perangkat_tps' => $perangkatTps,
                // Kita tambahkan URL file scan (jika sudah di-upload)
                'file_c1_url' => $tpsSetting->form_c1_file ? asset('uploads/c1/' . $tpsSetting->form_c1_file) : null,
                'file_c1_time' => $tpsSetting->form_c1_upload_time,
            ]
        ]);
    }

    /**
     * Upload hasil scan form C1
     */
    public function uploadC1(Request $request)
    {
        $user = $request->user();
        if ($user->level != 3) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'file_c1' => 'required|mimes:pdf,jpg,jpeg,png|max:2048' // Max 2MB
        ]);

        $npsn = $user->npsn;
        $tpsId = $user->id_tps;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $tpsSetting = DB::table('tb_tps_setting')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('id_kelas', $tpsId)
            ->first();

        if (!$tpsSetting) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan TPS tidak ditemukan.'
            ], 404);
        }

        if ($request->hasFile('file_c1')) {
            $file = $request->file('file_c1');
            $extension = $file->getClientOriginalExtension();
            $filename = $npsn . '_' . $tpsId . '_' . time() . '_c1.' . $extension;
            
            // Simpan ke direktori public/uploads/c1
            $file->move(public_path('uploads/c1'), $filename);

            // Update database
            DB::table('tb_tps_setting')
                ->where('id', $tpsSetting->id)
                ->update([
                    'form_c1_file' => $filename,
                    'form_c1_upload_time' => date('Y-m-d H:i:s')
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengunggah dokumen C1.',
                'file_url' => asset('uploads/c1/' . $filename)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada file yang diunggah.'
        ], 400);
    }
}
