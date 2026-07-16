<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    
    // Karena tabel database aslinya bernama tb_siswa
    protected $table = 'tb_siswa';

    // Karena tidak menggunakan created_at dan updated_at default dari laravel
    public $timestamps = false;

    // Field-field yang diizinkan untuk diisi secara massal (Mass Assignment)
    protected $fillable = [
        'nik',
        'nisn',
        'nm_siswa',
        'jk',
        'kelas',
        'difabel',
        'no_wa',
        'email',
        'npsn',
        'status',
        'tahun',
        'create_at',
        'hapus_time',
        'hapus_user_id',
    ];

    /**
     * Relasi ke Tabel Sekolah
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'npsn', 'npsn');
    }
}
