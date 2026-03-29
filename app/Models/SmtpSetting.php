<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'host', 'port', 'encryption', 'username', 'password',
        'from_address', 'from_name', 'notifications_enabled',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'port' => 'integer',
    ];

    protected $hidden = ['password'];

    public static function instance(): self
    {
        return self::firstOrCreate([], [
            'host'                   => 'smtp.mailtrap.io',
            'port'                   => 587,
            'encryption'             => 'tls',
            'from_name'              => 'Nexova Chat',
            'notifications_enabled'  => false,
        ]);
    }

    /**
     * Apply this config dynamically to Laravel's mail system.
     */
    public function applyToConfig(): void
    {
        config([
            'mail.default'                      => 'smtp',
            'mail.mailers.smtp.host'            => $this->host,
            'mail.mailers.smtp.port'            => $this->port,
            'mail.mailers.smtp.encryption'      => $this->encryption === 'none' ? null : $this->encryption,
            'mail.mailers.smtp.username'        => $this->username,
            'mail.mailers.smtp.password'        => $this->password,
            'mail.from.address'                 => $this->from_address,
            'mail.from.name'                    => $this->from_name,
        ]);
    }
}
