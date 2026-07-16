<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    protected $fillable = [
        'event_type',
        'description',
        'ip_address',
        'user_agent',
        'endpoint',
        'payload',
        'user_id'
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
