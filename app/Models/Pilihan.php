<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pilihan extends Model
{
    use HasFactory;

    protected $table = 'tb_pilihan';
    public $timestamps = false;

    protected $fillable = [
        'tahun',
        'kampanye',
        'created',
        'nisn',
        'nama',
        'photo',
        'photo_wakil',
        'no',
        'npsn',
        'visi',
        'misi',
        'proker',
        'pengalaman',
        'prestasi',
    ];

    /**
     * Relasi Calon (Pilihan) ini milik Sekolah mana
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'npsn', 'npsn');
    }
}
