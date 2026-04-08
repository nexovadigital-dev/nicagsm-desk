<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckPartnerLicense extends Command
{
    protected $signature   = 'partner:check-license';
    protected $description = 'Verify the partner license against nexovadesk.com and refresh the cache';

    public function handle(): int
    {
        $token   = config('partner.token');
        $baseUrl = config('partner.license_url', 'https://nexovadesk.com');

        if (! $token) {
            $this->error('PARTNER_TOKEN is not set in .env');
            return self::FAILURE;
        }

        $this->info("Checking license for token: " . substr($token, 0, 8) . "…");

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->get("{$baseUrl}/api/partner/verify/{$token}");

            if ($response->successful() && $response->json('valid') === true) {
                Cache::put('partner_license_valid', true, now()->addHours(24));
                $this->info("License valid — partner: " . $response->json('partner'));
                return self::SUCCESS;
            }

            Cache::put('partner_license_valid', false, now()->addHours(1));
            $this->error("License invalid: " . ($response->json('message') ?? 'unknown'));
            Log::error('[Partner] License check failed via artisan', ['response' => $response->json()]);
            return self::FAILURE;

        } catch (\Throwable $e) {
            // Network error — don't invalidate cache, keep current value
            $this->warn("Network error during license check: " . $e->getMessage());
            Log::warning('[Partner] License check network error', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }
    }
}
