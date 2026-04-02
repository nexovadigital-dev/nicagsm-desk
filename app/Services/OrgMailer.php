<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Organization;
use App\Models\SmtpSetting;
use Illuminate\Support\Str;

/**
 * Decides which FROM address and SMTP transport to use when sending
 * ticket-related emails on behalf of an organization.
 *
 * Rules:
 *  1. Org has enabled SMTP + verified domain → send via org SMTP, FROM = their address
 *  2. Org has enabled SMTP, no verified domain → send via org SMTP, FROM = their from_address
 *  3. No org SMTP → send via platform SMTP (system config), FROM = {slug}-tickets@nexovadesk.com
 */
class OrgMailer
{
    /**
     * Return [fromAddress, fromName] to embed in the mailable.
     */
    public static function fromFor(Organization $org): array
    {
        $smtp = SmtpSetting::forOrg($org->id);

        if ($smtp && $smtp->enabled && $smtp->host) {
            $addr = $smtp->from_address ?: ($org->support_email ?: null);
            $name = $smtp->from_name    ?: ($org->support_name  ?: $org->name);

            if ($addr) {
                return [$addr, $name];
            }
        }

        // Generic platform-branded FROM — no customization without domain verification
        return [self::genericEmail($org), $org->support_name ?: $org->name];
    }

    /**
     * Generate the platform generic email for this org.
     * Format: {orgslug}-tickets@nexovadesk.com
     */
    public static function genericEmail(Organization $org): string
    {
        $slug = Str::slug($org->name);
        if (empty($slug)) {
            $slug = 'org-' . $org->id;
        }

        return "{$slug}-tickets@nexovadesk.com";
    }

    /**
     * Apply the org's SMTP config before a mail send, using a named dynamic mailer.
     * Returns the mailer name to use with Mail::mailer($name).
     * Returns null if org has no custom SMTP → use default system mailer.
     */
    public static function mailerNameFor(Organization $org): ?string
    {
        $smtp = SmtpSetting::forOrg($org->id);

        if (! $smtp || ! $smtp->enabled || ! $smtp->host) {
            return null; // use system default
        }

        $key = 'org_smtp_' . $org->id;

        config(["mail.mailers.{$key}" => [
            'transport'  => 'smtp',
            'host'       => $smtp->host,
            'port'       => (int) ($smtp->port ?: 587),
            'encryption' => $smtp->encryption === 'none' ? null : ($smtp->encryption ?: 'tls'),
            'username'   => $smtp->username,
            'password'   => $smtp->password,
            'timeout'    => 30,
        ]]);

        return $key;
    }

    /**
     * Whether notifications are enabled for this org.
     * Requires the org to have SMTP configured OR the platform SMTP to be active.
     */
    public static function notificationsEnabled(Organization $org): bool
    {
        $smtp = SmtpSetting::forOrg($org->id);

        // Org has explicitly enabled its own SMTP notifications
        if ($smtp && $smtp->notifications_enabled) {
            return true;
        }

        // Fallback: platform SMTP is configured — use it for generic FROM
        $sys = \App\Models\SystemSetting::first();
        return $sys && $sys->smtp_host;
    }
}
