<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = ['email', 'otp', 'pending_data', 'expires_at'];

    protected $casts = [
        'pending_data' => 'array',
        'expires_at'   => 'datetime',
    ];
}
