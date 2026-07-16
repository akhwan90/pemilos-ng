<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanAudiensi extends Model
{
    const STATUS_BARU = 'baru';
    const STATUS_DIPROSES = 'diproses';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_SELESAI = 'selesai';

    protected $table = 'permohonan_audiencis';

    protected $fillable = [
        'nama',
        'nama_instansi_kelompok_paguyuban_komunitas',
        'maksud_tujuan_audiensi',
        'nama_jabatan_ketua_rombongan',
        'nomor_hp_narahubung',
        'jumlah_peserta',
        'file_permohonan_audiensi',
        'file_daftar_hadir',
        'file_dokumen_ppid',
        'nomor_surat_ppid',
        'tanggal_pelaksanaan',
        'jam_pelaksanaan',
        'alkap_penerima',
        'status',
        'ip_address',
        'user_agent',
    ];

    public function notes()
    {
        return $this->morphMany(AdminNote::class, 'notable');
    }
}
