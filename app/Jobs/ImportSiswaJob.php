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
                        $nik = trim($row[1] ?? '');
                        $nama = trim($row[2] ?? '');
                        $jk_str = trim(strtolower($row[3] ?? ''));
                        $kelas = trim($row[4] ?? '');
                        $difabel_str = trim(strtolower($row[5] ?? ''));
                        
                        // Parse JK (L=1, P=2)
                        $jk = 1;
                        if ($jk_str == 'p' || $jk_str == 'perempuan' || $jk_str == '2') {
                            $jk = 2;
                        }
                        
                        // Parse difabel
                        $difabel = 0;
                        if ($difabel_str == 'ya' || $difabel_str == '1') {
                            $difabel = 1;
                        }

                        if (!empty($nisn) && !empty($nama)) {
                            // Cek apakah NISN sudah ada di database
                            $cek_nisn = DB::table('tb_siswa')->where('nisn', $nisn)->first();

                            if (empty($cek_nisn)) {
                                // Insert baru
                                DB::table('tb_siswa')->insert([
                                    'nisn' => $nisn,
                                    'nik' => $nik,
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
                                        'nik' => $nik,
                                        'nm_siswa' => $nama,
                                        'jk' => $jk,
                                        'kelas' => $kelas,
                                        'difabel' => $difabel,
                                        'status' => 1 // Pastikan aktif
                                    ]);
                                    $this->logToDb("Berhasil update {$nama} (NISN: {$nisn})", 1, $nisn);
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
                                            'nik_baru' => $nik,
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
