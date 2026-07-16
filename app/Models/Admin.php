<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'tb_admin';
    public $timestamps = false;

    protected $fillable = [
        'npsn',
        'username',
        'password',
        'level',
        'id_tps',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relasi ke Tabel Sekolah (Jika level = 2 / admin sekolah)
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'npsn', 'npsn');
    }
}
