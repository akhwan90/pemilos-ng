<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class KandidatService
{
    public function getAll($npsn)
    {
        $tahun = env('TAHUN_AKTIF', date('Y'));
        $kandidat = DB::table('tb_pilihan')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->orderBy('no', 'asc')
            ->get();

        foreach ($kandidat as $k) {
            $k->photo_url = $k->photo ? url('/uploads/kandidat/' . $k->photo) : null;
        }

        return $kandidat;
    }

    public function find($npsn, $id)
    {
        $kandidat = DB::table('tb_pilihan')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->first();

        if ($kandidat && $kandidat->photo) {
            $kandidat->photo_url = url('/uploads/kandidat/' . $kandidat->photo);
        }

        return $kandidat;
    }

    public function create($npsn, $data, $file, $userId)
    {
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $exists = DB::table('tb_pilihan')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('no', $data['no'])
            ->exists();

        if ($exists) {
            throw new Exception('No urut kandidat sudah dipakai!');
        }

        $dataInsert = [
            'npsn' => $npsn,
            'tahun' => $tahun,
            'no' => $data['no'],
            'nama' => $data['nama'],
            'nisn' => $data['nisn'],
            'kampanye' => $data['kampanye'] ?? null,
            'visi' => $data['visi'] ?? null,
            'misi' => $data['misi'] ?? null,
            'proker' => $data['proker'] ?? null,
            'pengalaman' => $data['pengalaman'] ?? null,
            'prestasi' => $data['prestasi'] ?? null,
            // 'id_user' => $userId
        ];

        if ($file) {
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kandidat'), $filename);
            $dataInsert['photo'] = $filename;
        }

        DB::table('tb_pilihan')->insert($dataInsert);
    }

    public function update($npsn, $id, $data, $file)
    {
        $kandidat = DB::table('tb_pilihan')->where('id', $id)->where('npsn', $npsn)->first();

        if (!$kandidat) {
            throw new Exception('Kandidat tidak ditemukan');
        }

        $dataUpdate = [
            'no' => $data['no'] ?? $kandidat->no,
            'nama' => $data['nama'],
            'nisn' => $data['nisn'],
            'kampanye' => $data['kampanye'] ?? null,
            'visi' => $data['visi'] ?? null,
            'misi' => $data['misi'] ?? null,
            'proker' => $data['proker'] ?? null,
            'pengalaman' => $data['pengalaman'] ?? null,
            'prestasi' => $data['prestasi'] ?? null,
        ];

        if ($file) {
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kandidat'), $filename);

            if ($kandidat->photo && file_exists(public_path('uploads/kandidat/' . $kandidat->photo))) {
                @unlink(public_path('uploads/kandidat/' . $kandidat->photo));
            }

            $dataUpdate['photo'] = $filename;
        }

        DB::table('tb_pilihan')->where('id', $id)->update($dataUpdate);
    }

    public function delete($npsn, $id)
    {
        $kandidat = DB::table('tb_pilihan')->where('id', $id)->where('npsn', $npsn)->first();

        if (!$kandidat) {
            throw new Exception('Kandidat tidak ditemukan');
        }

        if ($kandidat->photo && file_exists(public_path('uploads/kandidat/' . $kandidat->photo))) {
            @unlink(public_path('uploads/kandidat/' . $kandidat->photo));
        }

        DB::table('tb_pilihan')->where('id', $id)->delete();
    }
}
