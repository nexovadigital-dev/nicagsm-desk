<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Message::observe(\App\Observers\MessageObserver::class);

        // Apply SMTP settings from DB at runtime
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $s = \App\Models\SystemSetting::first();
                if ($s && $s->smtp_host) {
                    config([
                        'mail.default'                     => 'smtp',
                        'mail.mailers.smtp.host'           => $s->smtp_host,
                        'mail.mailers.smtp.port'           => (int) ($s->smtp_port ?: 587),
                        'mail.mailers.smtp.encryption'     => $s->smtp_encryption ?: 'tls',
                        'mail.mailers.smtp.username'       => $s->smtp_username,
                        'mail.mailers.smtp.password'       => $s->smtp_password ? decrypt($s->smtp_password) : null,
                        'mail.from.address'                => $s->smtp_from_address ?: config('mail.from.address'),
                        'mail.from.name'                   => $s->smtp_from_name ?: 'Nexova Desk',
                    ]);
                }
            }
        } catch (\Throwable) {
            // DB not ready or decrypt fail — use env defaults
        }
    }
}
