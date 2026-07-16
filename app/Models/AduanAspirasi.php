<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AduanAspirasi extends Model
{
    const STATUS_BARU = 'baru';
    const STATUS_DIPROSES = 'diproses';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_SELESAI = 'selesai';

    protected $fillable = [
        'nama',
        'nik',
        'alamat',
        'pekerjaan',
        'nomor_hp',
        'email',
        'kategori_aduan_id',
        'isi_aduan',
        'file_berkas_aduan',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'kategori_aduan_id' => 'integer',
        ];
    }

    public function kategoriAduan()
    {
        return $this->belongsTo(KategoriAduan::class);
    }

    public function notes()
    {
        return $this->morphMany(AdminNote::class, 'notable');
    }
}
