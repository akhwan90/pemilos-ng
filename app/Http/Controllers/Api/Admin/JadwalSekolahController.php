<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalSekolahController extends Controller
{
    // Daftar jenis setting yang wajib ada
    private $jenisSettings = [
        'input_data_dps',
        'pengumuman_data_dps',
        'input_data_dpt',
        'pengumuman_data_dpt',
        'input_data_calon',
        'kampanye',
        'generate_token',
        'pemilihan'
    ];

    public function index($npsn)
    {
        $tahun = env('TAHUN_AKTIF', 2026);

        // Ambil data yang sudah ada di database
        $existingData = DB::table('tb_setting_waktu_pemilihan')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->get()
            ->map(function($item) {
                // Konversi kembali dari DB format 'YYYY-MM-DD HH:mm:ss' 
                // menjadi 'YYYY-MM-DDTHH:mm' agar dibaca benar oleh input type="datetime-local" di Vue
                if ($item->waktu_mulai) {
                    $item->waktu_mulai = date('Y-m-d\TH:i', strtotime($item->waktu_mulai));
                }
                if ($item->waktu_selesai) {
                    $item->waktu_selesai = date('Y-m-d\TH:i', strtotime($item->waktu_selesai));
                }
                return $item;
            })
            ->keyBy('jenis');

        $result = [];
        foreach ($this->jenisSettings as $jenis) {
            if ($existingData->has($jenis)) {
                $result[] = $existingData[$jenis];
            } else {
                // Return dummy empty structure kalau belum disetting
                $result[] = [
                    'id' => null,
                    'jenis' => $jenis,
                    'jenjang' => null, // akan diisi saat save
                    'waktu_mulai' => null,
                    'waktu_selesai' => null,
                    'tahun' => $tahun,
                    'npsn' => $npsn
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function store(Request $request, $npsn)
    {
        $tahun = env('TAHUN_AKTIF', 2026);
        $settings = $request->input('settings'); // Expect array of settings

        if (!is_array($settings)) {
            return response()->json(['success' => false, 'message' => 'Format data tidak valid'], 400);
        }

        // Kita perlu tau jenjang sekolah ini apa
        $sekolah = DB::table('tb_sekolah')->where('npsn', $npsn)->first();
        $jenjang = $sekolah ? $sekolah->jenjang : '';

        DB::beginTransaction();
        try {
            foreach ($settings as $item) {
                if (!in_array($item['jenis'], $this->jenisSettings)) continue;

                // Cek apakah data sudah ada
                $existing = DB::table('tb_setting_waktu_pemilihan')
                    ->where('npsn', $npsn)
                    ->where('tahun', $tahun)
                    ->where('jenis', $item['jenis'])
                    ->first();

                // Validasi input tanggal. Untuk input type="datetime-local", Vue mengembalikan string 'YYYY-MM-DDTHH:mm'.
                // MariaDB butuh format 'YYYY-MM-DD HH:mm:00'.
                $waktuMulai = !empty($item['waktu_mulai']) ? str_replace('T', ' ', $item['waktu_mulai']) . ':00' : null;
                $waktuSelesai = !empty($item['waktu_selesai']) ? str_replace('T', ' ', $item['waktu_selesai']) . ':00' : null;


                // Hanya simpan kalau user mengisi tanggal, atau update jika sudah ada
                if ($existing) {
                    if ($waktuMulai || $waktuSelesai) {
                        DB::table('tb_setting_waktu_pemilihan')->where('id', $existing->id)->update([
                            'waktu_mulai' => $waktuMulai,
                            'waktu_selesai' => $waktuSelesai,
                        ]);
                    }
                } else {
                    if ($waktuMulai && $waktuSelesai) {
                        DB::table('tb_setting_waktu_pemilihan')->insert([
                            'jenjang' => $jenjang,
                            'jenis' => $item['jenis'],
                            'waktu_mulai' => $waktuMulai,
                            'waktu_selesai' => $waktuSelesai,
                            'tahun' => $tahun,
                            'npsn' => $npsn
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Jadwal berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan jadwal: ' . $e->getMessage()], 500);
        }
    }
}
