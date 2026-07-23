<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WaktuPemilihanService
{
    /**
     * Mengecek apakah waktu sekarang berada dalam rentang jadwal pemilihan
     * berdasarkan jenis, tahun, dan npsn.
     *
     * @param int $jenis Jenis pemilihan
     * @param int|string $tahun Tahun pemilihan
     * @param string $npsn NPSN Sekolah
     * @return array Array berisi boolean 'is_open' dan pesan status 'message'
     */
    public function cekJadwalBuka($jenisReadable, $tahun, $npsn)
    {
        $setting = DB::table('tb_setting_waktu_pemilihan')
            ->where('jenis', $jenisReadable)
            ->where('tahun', $tahun)
            ->where('npsn', $npsn)
            ->first();

        if (!$setting || empty($setting->waktu_mulai) || empty($setting->waktu_selesai)) {
            return [
                'is_open' => false,
                'message' => 'Jadwal pemilihan belum diatur.',
                'setting' => $setting ?? null
            ];
        }

        $now = Carbon::now();
        $mulai = Carbon::parse($setting->waktu_mulai);
        $selesai = Carbon::parse($setting->waktu_selesai);

        $jenisReadable = str_replace('_', ' ', $jenisReadable);
        // Jika waktu_mulai dan waktu_selesai bukan NULL, Carbon::parse sudah akan membaca jam/menitnya
        if ($now->lt($mulai)) {
            return [
                'is_open' => false,
                'message' => 'Waktu ' . $jenisReadable . ' belum dimulai.',
                'setting' => $setting
            ];
        }

        if ($now->gt($selesai)) {
            return [
                'is_open' => false,
                'message' => 'Waktu ' . $jenisReadable .  ' sudah berakhir.',
                'setting' => $setting
            ];
        }

        return [
            'is_open' => true,
            'message' => 'Waktu ' . $jenisReadable . ' sedang berlangsung.',
            'setting' => $setting
        ];
    }
}
