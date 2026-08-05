<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportSiswaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobId;
    protected $username;
    protected $npsn;
    protected $filename;

    public function __construct($jobId, $username, $npsn, $filename)
    {
        $this->jobId = $jobId;
        $this->username = $username;
        $this->npsn = $npsn;
        $this->filename = $filename;
    }

    public function handle()
    {
        $path = public_path('uploads/xlsx_temp/' . $this->filename);
        $activityService = new \App\Services\ActivityService();
        $activityService->logActivity($this->username, 9, json_encode([
            'npsn' => $this->npsn,
            'filename' => $this->filename
        ]));

        if (!file_exists($path)) {
            Log::error("ImportSiswaJob: File excel tidak ditemukan di {$path}");

            DB::table('upload_job')->where('id', $this->jobId)->update([
                'is_selesai' => 1,
                'finish_at' => date('Y-m-d H:i:s')
            ]);

            $this->logToDb("File excel {$this->filename} tidak ditemukan di server", 0);
            return;
        }

        try {
            // Karena tidak ada PhpSpreadsheet, kita pakai cara kotor/sederhana
            // Jika ada box/spout atau PhpSpreadsheet, gunakan itu. Di CI3 pakai box/spout.
            // Di Laravel, kita asumsikan butuh package Maatwebsite/Excel atau shuchkin/simplexlsx
            // Untuk sementara kita biarkan fungsi parsing excel placeholder yang akan mencatat error "Package not installed"
            // jika belum diinstall.

            if (!class_exists('\Shuchkin\SimpleXLSX')) {
                 $this->logToDb("Package Shuchkin\SimpleXLSX belum diinstall di Laravel. Jalankan: composer require shuchkin/simplexlsx", 0);
                 $this->finishJob();
                 return;
            }

            if ( $xlsx = \Shuchkin\SimpleXLSX::parse($path) ) {
                $rows = $xlsx->rows();
                $no = 0;
                foreach ($rows as $row) {
                    if ($no > 0) { // Skip header (baris pertama)
                        // Mapping struktur sesuai template excel lama CI3
                        // Asumsi kolom: A: NISN, B: NIK, C: Nama, D: JK(L/P), E: Kelas, F: Difabel, G: WA, H: Email
                        $nisn = trim($row[0] ?? '');
                        $nisn = str_replace(['.', '-', '\'', '`', '‘'], '', $nisn);
                        $nama = trim($row[1] ?? '');
                        $jk_str = trim(strtolower($row[2] ?? ''));
                        $kelas = trim($row[3] ?? '');
                        $difabel_str = trim(strtolower($row[4] ?? ''));

                        // Validasi JK (hanya menerima 1/Laki-laki atau 2/Perempuan)
                        $jk = null;
                        if (in_array($jk_str, ['1', 'l', 'laki', 'laki-laki', 'laki - laki', 'pria'])) {
                            $jk = 1;
                        } elseif (in_array($jk_str, ['2', 'p', 'perempuan', 'wanita'])) {
                            $jk = 2;
                        }

                        // Validasi difabel (hanya menerima angka)
                        $difabel = null;
                        if (is_numeric($difabel_str)) {
                            $difabel = (int)$difabel_str;
                        }

                        if (!empty($nisn) && !empty($nama)) {
                            // Validasi Nama minimal 3 karakter
                            if (strlen($nama) < 3) {
                                $this->logToDb("Gagal memproses baris {$no}: Nama '{$nama}' kurang dari 3 karakter (NISN: {$nisn})", 0, $nisn);
                                $no++;
                                continue;
                            }

                            // Validasi NISN minimal 10 digit
                            if (strlen($nisn) < 10) {
                                $this->logToDb("Gagal memproses {$nama}: NISN ({$nisn}) kurang dari 10 digit", 0, $nisn);
                                $no++;
                                continue;
                            }

                            // Cek validitas Jenis Kelamin
                            if (is_null($jk)) {
                                $this->logToDb("Gagal memproses {$nama}: Jenis kelamin '{$jk_str}' tidak valid (Gunakan 1 untuk L atau 2 untuk P)", 0, $nisn);
                                $no++;
                                continue;
                            }

                            // Cek validitas Difabel
                            if (is_null($difabel) || $difabel < 0) {
                                $this->logToDb("Gagal memproses {$nama}: Kode difabel '{$difabel_str}' tidak valid", 0, $nisn);
                                $no++;
                                continue;
                            }

                            // Cek apakah NISN sudah ada di database
                            $cek_nisn = DB::table('tb_siswa')->where('nisn', $nisn)->first();

                            if (empty($cek_nisn)) {
                                // Insert baru
                                DB::table('tb_siswa')->insert([
                                    'nisn' => $nisn,
                                    'nm_siswa' => $nama,
                                    'jk' => $jk,
                                    'kelas' => $kelas,
                                    'difabel' => $difabel,
                                    'npsn' => $this->npsn,
                                    'status' => 1,
                                    'tahun' => env('TAHUN_AKTIF', date('Y')),
                                    'create_at' => date('Y-m-d H:i:s')
                                ]);

                                $this->logToDb("Berhasil insert {$nama} (NISN: {$nisn})", 1, $nisn);
                            } else {
                                // Jika ada, cek npsn
                                if ($cek_nisn->npsn == $this->npsn) {
                                    // Update jika NPSN sama
                                    DB::table('tb_siswa')->where('id', $cek_nisn->id)->update([
                                        // 'nik' => $nik,
                                        'nm_siswa' => $nama,
                                        'jk' => $jk,
                                        'kelas' => $kelas,
                                        'difabel' => $difabel,
                                        'status' => 1 // Pastikan aktif
                                    ]);
                                    $this->logToDb("Berhasil update {$nama} (NISN: {$nisn})", 1, $nisn);
                                } else {
                                    if ($cek_nisn->npsn == null) {
                                        // jika npsn null, maka update npsn menjadi npsn sekolah yang import
                                        DB::table('tb_siswa')->where('nisn', $nisn)->update(['npsn' => $this->npsn]);
                                        $this->logToDb("Berhasil update {$nama} (NISN: {$nisn}). NPSN sebelumnya NULL", 1, $nisn);
                                    } else {
                                        // Beda sekolah (Pindah Sekolah)
                                        $sekolah_lama = DB::table('tb_sekolah')->where('npsn', $cek_nisn->npsn)->first();
                                        $nama_sekolah_lama = $sekolah_lama ? $sekolah_lama->nama_sekolah : $cek_nisn->npsn;

                                        $this->logToDb("NISN: {$nisn} terdaftar di {$nama_sekolah_lama}. Menunggu persetujuan pindah sekolah.", 0, $nisn, $cek_nisn->npsn);

                                        // Masukkan ke tabel aproval_pindah_sekolah
                                        DB::table('aproval_pindah_sekolah')->updateOrInsert(
                                            ['nisn' => $nisn],
                                            [
                                                'user_pemohon' => $this->username,
                                                'user_pemohon_npsn' => $this->npsn,
                                                'npsn' => $cek_nisn->npsn,
                                                'status' => 0,
                                                'created_at' => date('Y-m-d H:i:s'),
                                                'nama_baru' => $nama,
                                                'jk_baru' => $jk,
                                                'kelas_baru' => $kelas,
                                                'difabel_baru' => $difabel
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }
                    $no++;
                }
            } else {
                $this->logToDb("Gagal memparsing file Excel: " . \Shuchkin\SimpleXLSX::parseError(), 0);
            }
        } catch (\Exception $e) {
            Log::error("ImportSiswaJob Exception: " . $e->getMessage());
            $this->logToDb("Terjadi kesalahan sistem: " . $e->getMessage(), 0);
        }

        $this->finishJob();
    }

    private function logToDb($pesan, $isSuccess, $nisn = null, $npsn_lama = null)
    {
        DB::table('upload_job_log')->insert([
            'id_upload_job' => $this->jobId,
            'keterangan' => $pesan,
            'is_success' => $isSuccess,
            'nisn' => $nisn,
            'npsn' => $npsn_lama,
            'waktu' => date('Y-m-d H:i:s')
        ]);
    }

    private function finishJob()
    {
        DB::table('upload_job')->where('id', $this->jobId)->update([
            'is_selesai' => 1,
            'finish_at' => date('Y-m-d H:i:s')
        ]);
    }
}
