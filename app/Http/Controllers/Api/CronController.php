<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * CronController
 *
 * Expone endpoints HTTP para disparar tareas programadas desde:
 *   - Hostinger hPanel (Cron Jobs)
 *   - Servicios de cron externos (cron-job.org, UptimeRobot, EasyCron, etc.)
 *
 * Protección: token secreto en CRON_SECRET (.env). Sin token → 403.
 *
 * Uso:
 *   GET /cron/run?token=SECRET            → ejecuta schedule:run (todos los jobs activos)
 *   GET /cron/imap?token=SECRET           → solo tickets:process-inbound
 *   GET /cron/subscriptions?token=SECRET  → solo nexova:check-subscriptions
 *   GET /cron/sync?token=SECRET           → solo nexova:sync-external
 *   GET /cron/crypto?token=SECRET         → solo nexova:verify-crypto
 *   GET /cron/license?token=SECRET        → solo partner:check-license
 */
class CronController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────

    private function authorize(Request $request): bool
    {
        $secret = config('app.cron_secret', env('CRON_SECRET', ''));
        if (empty($secret)) {
            // Si no hay CRON_SECRET configurado, bloquear siempre
            return false;
        }
        return $request->query('token') === $secret;
    }

    private function forbidden(): JsonResponse
    {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    private function runCommand(string $command, string $label): JsonResponse
    {
        $start = microtime(true);
        try {
            Artisan::call($command);
            $output = Artisan::output();
            $elapsed = round((microtime(true) - $start) * 1000);
            Log::info("[Cron HTTP] {$label} ejecutado en {$elapsed}ms");
            return response()->json([
                'ok'      => true,
                'command' => $command,
                'elapsed' => "{$elapsed}ms",
                'output'  => trim($output) ?: '(sin output)',
            ]);
        } catch (\Throwable $e) {
            Log::error("[Cron HTTP] Error en {$command}: {$e->getMessage()}");
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Endpoints
    // ─────────────────────────────────────────────────────────────────────────

    /** Ejecuta schedule:run — equivalente al cron de sistema `php artisan schedule:run` */
    public function run(Request $request): JsonResponse
    {
        if (! $this->authorize($request)) return $this->forbidden();
        return $this->runCommand('schedule:run', 'schedule:run');
    }

    /** Solo procesar emails IMAP entrantes (respuestas de clientes a tickets) */
    public function imap(Request $request): JsonResponse
    {
        if (! $this->authorize($request)) return $this->forbidden();
        return $this->runCommand('tickets:process-inbound', 'IMAP');
    }

    /** Solo verificar suscripciones vencidas */
    public function subscriptions(Request $request): JsonResponse
    {
        if (! $this->authorize($request)) return $this->forbidden();
        return $this->runCommand('nexova:check-subscriptions', 'subscriptions');
    }

    /** Solo sincronizar sistema externo */
    public function sync(Request $request): JsonResponse
    {
        if (! $this->authorize($request)) return $this->forbidden();
        return $this->runCommand('nexova:sync-external', 'sync');
    }

    /** Solo verificar pagos crypto */
    public function crypto(Request $request): JsonResponse
    {
        if (! $this->authorize($request)) return $this->forbidden();
        return $this->runCommand('nexova:verify-crypto', 'crypto');
    }

    /** Solo verificar licencia partner */
    public function license(Request $request): JsonResponse
    {
        if (! $this->authorize($request)) return $this->forbidden();
        return $this->runCommand('partner:check-license', 'license');
    }
}
