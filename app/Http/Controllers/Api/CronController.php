<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\SmtpSetting;
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
 *   - Servicios de cron externos (cron-job.org, EasyCron, etc.)
 *
 * Los endpoints son públicos (sin token). Protege a nivel de servidor
 * usando User-Agent filtering o IP whitelist si lo deseas.
 *
 * Uso:
 *   GET /api/cron/worker       → ejecuta todo el scheduler (schedule:run)
 *   GET /api/cron/imap         → solo tickets:process-inbound (respuestas email)
 *   GET /api/cron/license      → solo partner:check-license
 *   GET /api/cron/imap-status  → diagnóstico IMAP (no procesa, solo informa)
 */
class CronController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────

    private function runCommand(string $command, string $label): JsonResponse
    {
        $start = microtime(true);
        try {
            Artisan::call($command);
            $output  = Artisan::output();
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

    /** Worker genérico — ejecuta todos los jobs del scheduler */
    public function worker(): JsonResponse
    {
        return $this->runCommand('schedule:run', 'worker (schedule:run)');
    }

    /** Solo procesar emails IMAP entrantes (respuestas de clientes a tickets) */
    public function imap(): JsonResponse
    {
        return $this->runCommand('tickets:process-inbound', 'IMAP');
    }

    /** Solo verificar licencia partner */
    public function license(): JsonResponse
    {
        return $this->runCommand('partner:check-license', 'license');
    }

    /**
     * Diagnóstico IMAP — NO procesa mensajes.
     * Retorna: estado de conexión, total mensajes, mensajes no leídos.
     */
    public function imapStatus(): JsonResponse
    {
        if (! function_exists('imap_open')) {
            return response()->json(['ok' => false, 'error' => 'PHP IMAP extension not installed']);
        }

        $results = [];

        SmtpSetting::where('imap_enabled', true)
            ->whereNotNull('imap_host')
            ->whereNotNull('imap_username')
            ->whereNotNull('imap_password')
            ->each(function ($s) use (&$results) {
                $enc    = match ($s->imap_encryption) { 'ssl' => '/ssl', 'tls' => '/tls', default => '/novalidate-cert' };
                $folder = $s->imap_folder ?: 'INBOX';
                $dsn    = "{{$s->imap_host}:{$s->imap_port}/imap{$enc}}{$folder}";

                $conn = @imap_open($dsn, $s->imap_username, $s->imap_password, 0, 1);

                if ($conn) {
                    $total  = imap_num_msg($conn);
                    $unseen = imap_search($conn, 'UNSEEN', SE_UID);
                    imap_close($conn);
                    $results[] = [
                        'org_id'  => $s->organization_id,
                        'host'    => $s->imap_host,
                        'folder'  => $folder,
                        'total'   => $total,
                        'unseen'  => $unseen ? count($unseen) : 0,
                        'status'  => 'connected',
                    ];
                } else {
                    $results[] = [
                        'org_id' => $s->organization_id,
                        'host'   => $s->imap_host,
                        'status' => 'error',
                        'error'  => imap_last_error() ?: 'unknown',
                    ];
                }
            });

        return response()->json(['ok' => true, 'accounts' => $results]);
    }
}
