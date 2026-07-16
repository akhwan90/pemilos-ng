<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriAduan extends Model
{
    protected $table = 'kategori_aduan';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function aduanAspirasis()
    {
        return $this->hasMany(AduanAspirasi::class);
    }
}
