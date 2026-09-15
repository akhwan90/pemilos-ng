<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NisnHistory extends Model
{
    protected $table = 'nisn_histories';
    protected $fillable = [
        'nisn',
        'npsn',
        'nama',
        'kelas',
        'jk',
        'difabel',
        'status',
        'keterangan'
    ];
}