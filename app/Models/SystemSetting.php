<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'allow_registrations',
        'registration_closed_message',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
    ];

    protected $casts = [
        'allow_registrations' => 'boolean',
    ];

    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'allow_registrations'         => true,
            'registration_closed_message' => 'No estamos admitiendo registros nuevos en este momento por labores de mantenimiento. Por favor, inténtalo más tarde.',
        ]);
    }
}
