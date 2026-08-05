<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activity';
    public $timestamps = false; // Karena kita menggunakan kolom 'waktu'

    protected $fillable = [
        'username',
        'id_aktifitas',
        'keterangan',
        'ip',
        'browser',
        'browser_versi',
        'is_mobile',
        'mobile_ver',
        'os',
        'waktu',
    ];
}
