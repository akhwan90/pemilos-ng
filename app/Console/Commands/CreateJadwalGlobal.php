<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateJadwalGlobal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-jadwal-global';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $listJadwals = config('pemilos.jenis_jadwal');
        $sekolahs = DB::table('tb_sekolah')->get();
        $waktu = [
            'input_data_dps'=>['mulai'=>'2026-08-01 00:01:00', 'selesai'=>'2026-10-30 23:59:00',],
            'pengumuman_data_dps'=>['mulai'=>'2026-08-01 00:01:00', 'selesai'=>'2026-10-30 23:59:00',],
            'input_data_dpt'=>['mulai'=>'2026-08-01 00:01:00', 'selesai'=>'2026-10-30 23:59:00',],
            'pengumuman_data_dpt'=>['mulai'=>'2026-08-01 00:01:00', 'selesai'=>'2026-10-30 23:59:00',],
            'input_data_calon'=>['mulai'=>'2026-08-01 00:01:00', 'selesai'=>'2026-10-30 23:59:00',],
            'kampanye'=>['mulai'=>'2026-08-01 00:01:00', 'selesai'=>'2026-10-30 23:59:00',],
            'generate_token'=>['mulai'=>'2026-08-01 00:01:00', 'selesai'=>'2026-10-30 23:59:00',],
            'pemilihan'=>['mulai'=>'2026-08-01 00:01:00', 'selesai'=>'2026-10-30 23:59:00',],
        ];



        foreach ($sekolahs as $sekolah) {
            foreach ($listJadwals as $listK => $listV) {
                $cekSudahAda = DB::table('tb_setting_waktu_pemilihan')
                ->where('tahun', env('APP_TAHUN_AKTIF'))
                ->where('npsn', $sekolah->npsn)
                ->where('jenis', $listK)
                ->first();

                $update = DB::table('tb_sekolah')
                ->where('npsn', $sekolah->npsn)
                ->update([
                    'nama_sekolah' => trim($sekolah->nama_sekolah)
                ]);

                if ($cekSudahAda) {
                    echo trim($sekolah->nama_sekolah)." : ".$listK . ' => ' . $listV['label'].' (sudah ada)' . PHP_EOL;
                } else {
                    DB::table('tb_setting_waktu_pemilihan')->insert([
                        'jenjang' => $sekolah->jenjang,
                        'jenis' => $listK,
                        'waktu_mulai'=>$waktu[$listK]['mulai'],
                        'waktu_selesai'=>$waktu[$listK]['selesai'],
                        'tahun' => env('APP_TAHUN_AKTIF'),
                        'npsn' => $sekolah->npsn,
                    ]);
                    echo trim($sekolah->nama_sekolah)." : ".$listK . ' => ' . $listV['label'] . PHP_EOL;
                }
            }
            // echo $sekolah->nama_sekolah . PHP_EOL;
        }
        //
    }
}
