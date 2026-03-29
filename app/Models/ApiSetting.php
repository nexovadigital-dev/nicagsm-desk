<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'api_key',
        'webhook_verify_token',
        'is_active',
        'priority',
    ];

    // Ocultar campos sensibles al serializar el modelo (ej. JSON)
    protected $hidden = [
        'api_key',
        'webhook_verify_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];
}