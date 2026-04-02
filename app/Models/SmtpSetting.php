<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'host', 'port', 'encryption', 'username', 'password',
        'from_address', 'from_name',
        'notifications_enabled',
        'enabled',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'enabled'               => 'boolean',
        'port'                  => 'integer',
    ];

    protected $hidden = ['password'];

    /**
     * Get or create the SMTP config for a specific organization.
     */
    public static function forOrg(int $orgId): self
    {
        return self::firstOrCreate(
            ['organization_id' => $orgId],
            [
                'port'                  => 587,
                'encryption'            => 'tls',
                'from_name'             => 'Soporte',
                'notifications_enabled' => false,
                'enabled'               => false,
            ]
        );
    }

    /**
     * Legacy singleton — kept for backward compat (global smtp_settings row with org_id = null).
     * New code should use forOrg() instead.
     */
    public static function instance(): self
    {
        return self::firstOrCreate(
            ['organization_id' => null],
            [
                'host'                  => '',
                'port'                  => 587,
                'encryption'            => 'tls',
                'from_name'             => 'Nexova Chat',
                'notifications_enabled' => false,
                'enabled'               => false,
            ]
        );
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
