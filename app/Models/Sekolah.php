<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'tb_sekolah';
    
    // Karena primary key nya bukan 'id' melainkan 'npsn'
    protected $primaryKey = 'npsn';
    
    // Memberitahu eloquent bahwa npsn ini bukan auto-increment
    public $incrementing = false;
    
    // Tipe data primary key
    protected $keyType = 'integer';

    public $timestamps = false;

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'alamat_sekolah',
        'kepala_sekolah',
        'mulai_memilih',
        'akhir_memilih',
        'jenjang2',
        'jenjang',
        'logo',
        'is_delete',
        'deleted_time',
        'desa',
        'kecamatan',
        'is_kemenag',
        'is_jadwal_sendiri'
    ];

    /**
     * Relasi 1 Sekolah memiliki Banyak Siswa
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'npsn', 'npsn');
    }

    /**
     * Relasi 1 Sekolah memiliki Banyak Pilihan (Calon Ketua OSIS)
     */
    public function pilihans()
    {
        return $this->hasMany(Pilihan::class, 'npsn', 'npsn');
    }
}
