<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNote extends Model
{
    protected $fillable = [
        'admin_id',
        'notable_id',
        'notable_type',
        'note',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function notable()
    {
        return $this->morphTo();
    }
}
