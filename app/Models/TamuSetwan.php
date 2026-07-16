<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TamuSetwan extends Model
{
    const STATUS_BARU = 'baru';
    const STATUS_DIPROSES = 'diproses';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_SELESAI = 'selesai';

    protected $fillable = [
        'nama',
        'instansi',
        'hari_berkunjung',
        'jam_berkunjung',
        'tanggal_berkunjung',
        'jumlah_peserta',
        'nama_jabatan_ketua_rombongan',
        'nomor_hp_narahubung',
        'email',
        'alamat_instansi',
        'tujuan_kunjungan',
        'file_surat_kunjungan',
        'file_spt',
        'file_bukti_menginap',
        'file_daftar_hadir',
        'file_dokumen_ppid',
        'file_daftar_hadir_ttd',
        'file_foto_kunjungan',
        'nomor_surat_ppid',
        'materi',
        'status',
        'ip_address',
        'user_agent',
    ];

    public function notes()
    {
        return $this->morphMany(AdminNote::class, 'notable');
    }
}
