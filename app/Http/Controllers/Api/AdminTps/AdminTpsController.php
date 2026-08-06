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

        $activityService = new \App\Services\ActivityService();
        $activityService->logActivity($user->username, 30, json_encode([
            'tps_id' => $tpsId,
            'tahun' => $tahun,
            'npsn' => $npsn,
        ]));

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

        // 2. Jika hasil sudah tersimpan di JSON, langsung gunakan itu
        if ($tpsSetting->hasil) {
            $hasilLengkap = json_decode($tpsSetting->hasil, true);
        } else {
            // 3. Jika belum di-generate (pertama kali buka setelah klik Selesai Pemilihan), hitung dan simpan

            // a. Ambil Paslon/Kandidat
            $kandidatList = DB::table('tb_pilihan')
                ->where('npsn', $npsn)
                ->where('tahun', $tahun)
                ->orderBy('no', 'asc')
                ->get();

            // b. Hitung Suara Masuk & DPT
            $totalDpt = DB::table('tb_siswa_tps')
                ->where('npsn', $npsn)
                ->where('id_tps', $tpsId)
                ->where('tahun', $tahun)
                ->count();

            $suaraMasuk = DB::table('tb_siswa_tps')
                ->where('npsn', $npsn)
                ->where('id_tps', $tpsId)
                ->where('tahun', $tahun)
                ->whereNotNull('waktu_pilih')
                ->count();

            // c. Hitung perolehan masing-masing paslon
            $perolehanPaslon = [];
            foreach ($kandidatList as $kand) {
                $jumlahSuara = DB::table('tb_siswa_tps')
                    ->where('npsn', $npsn)
                    ->where('id_tps', $tpsId)
                    ->where('tahun', $tahun)
                    ->where('pilihan', $kand->id)
                    ->count();

                $perolehanPaslon[] = [
                    'id_calon' => $kand->id,
                    'no_urut' => $kand->no,
                    'nama_ketua' => current(explode('<br>', $kand->nama)), // Ambil baris pertama jika ada tag <br>
                    'nama_lengkap' => $kand->nama,
                    'jumlah_suara' => $jumlahSuara
                ];
            }

            // Validasi suara tidak sah (kalau is_memilih=1 tapi pilihan_id null atau tidak cocok)
            // Di sistem e-voting standar ini jarang terjadi, tapi jaga-jaga
            $totalSuaraSah = array_sum(array_column($perolehanPaslon, 'jumlah_suara'));
            $suaraTidakSah = $suaraMasuk - $totalSuaraSah;

            $hasilLengkap = [
                'statistik' => [
                    'total_dpt' => $totalDpt,
                    'suara_masuk' => $suaraMasuk,
                    'suara_sah' => $totalSuaraSah,
                    'suara_tidak_sah' => $suaraTidakSah < 0 ? 0 : $suaraTidakSah,
                    'tidak_memilih' => $totalDpt - $suaraMasuk
                ],
                'perolehan' => $perolehanPaslon,
                'generated_at' => date('Y-m-d H:i:s')
            ];

            // Simpan snapshot hasil JSON secara permanen agar nilainya terkunci saat pemilihan selesai
            DB::table('tb_tps_setting')
                ->where('id', $tpsSetting->id)
                ->update(['hasil' => json_encode($hasilLengkap)]);
        }

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
