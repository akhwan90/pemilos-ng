<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateTokenService
{
    /**
     * Generate token acak yang mudah dibaca (menghindari karakter ambigu)
     * 
     * @param int $length
     * @return string
     */
    private function generateReadableToken($length = 5)
    {
        // Menghapus 0, O, 1, I, L untuk menghindari kebingungan
        $pool = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $pool[random_int(0, strlen($pool) - 1)];
        }
        
        return $token;
    }

    /**
     * Mengeksekusi proses pembuatan token massal untuk seluruh siswa di satu TPS
     * 
     * @param string $idTps (kd_kelas)
     * @param string $npsn (sebagai verifikasi keamanan ganda)
     * @param string $tahun
     * @return array
     */
    public function generateForTps($idTps, $npsn, $tahun)
    {
        try {
            DB::beginTransaction();

            // 1. Ambil semua data siswa (DPT) yang berada di TPS tersebut pada tahun aktif
            $siswaList = DB::table('tb_siswa_tps')
                ->where('id_tps', $idTps)
                ->where('npsn', $npsn)
                ->where('tahun', $tahun)
                ->get();

            if ($siswaList->isEmpty()) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Tidak ada DPT (Data Pemilih Tetap) di TPS ini. Tidak dapat melakukan Generate Token.'
                ];
            }

            // 2. Loop dan update token dengan string acak (mudah dibaca)
            foreach ($siswaList as $siswa) {
                $newToken = $this->generateReadableToken(5);
                
                DB::table('tb_siswa_tps')
                    ->where('id', $siswa->id)
                    ->update([
                        'token' => $newToken
                    ]);
            }

            // 3. Update status TPS (tb_tps_setting)
            // Cek apakah data generate token untuk kelas ini sudah ada
            $cekKelas = DB::table('tb_tps_setting')
                ->where('id_kelas', $idTps)
                ->where('npsn', $npsn)
                ->where('tahun', $tahun)
                ->first();

            $currentTime = date('Y-m-d H:i:s');

            if ($cekKelas) {
                // Update record yang sudah ada
                DB::table('tb_tps_setting')
                    ->where('id_kelas', $idTps)
                    ->where('tahun', $tahun)
                    ->update([
                        'is_generate_token' => 1,
                        'generate_token_time' => $currentTime
                    ]);
            } else {
                // Insert baru
                DB::table('tb_tps_setting')->insert([
                    'id_kelas' => $idTps,
                    'npsn' => $npsn,
                    'tahun' => $tahun,
                    'is_generate_token' => 1,
                    'generate_token_time' => $currentTime
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Generate Token berhasil dilakukan untuk ' . $siswaList->count() . ' siswa di TPS ini.',
                'generated_count' => $siswaList->count()
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat generate token: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Membatalkan (rollback) token untuk seluruh siswa di satu TPS
     * 
     * @param string $idTps (kd_kelas)
     * @param string $npsn
     * @param string $tahun
     * @return array
     */
    public function cancelForTps($idTps, $npsn, $tahun)
    {
        try {
            DB::beginTransaction();

            // 1. Hapus token dari data siswa di TPS tersebut
            $updatedSiswa = DB::table('tb_siswa_tps')
                ->where('id_tps', $idTps)
                ->where('npsn', $npsn)
                ->where('tahun', $tahun)
                ->update([
                    'token' => null
                ]);

            // 2. Update status kelas/TPS menjadi belum generate token
            DB::table('tb_tps_setting')
                ->where('id_kelas', $idTps)
                ->where('npsn', $npsn)
                ->where('tahun', $tahun)
                ->update([
                    'is_generate_token' => 0,
                    'generate_token_time' => null
                ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Batal Generate Token berhasil. Token untuk semua siswa di TPS ini telah dihapus.',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat rollback token: ' . $e->getMessage()
            ];
        }
    }
}