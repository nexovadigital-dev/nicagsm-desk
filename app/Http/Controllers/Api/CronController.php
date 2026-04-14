<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Organization;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * CronController
 *
 * Expone endpoints HTTP para disparar tareas programadas.
 * Devuelve HTML amigable en navegador, JSON para peticiones programáticas.
 */
class CronController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────

    /** Detecta si la petición viene de un navegador */
    private function isBrowser(Request $request): bool
    {
        $accept = $request->header('Accept', '');
        return str_contains($accept, 'text/html');
    }

    /** Genera una respuesta HTML amigable */
    private function htmlResponse(bool $ok, string $title, string $message, string $detail = '', int $elapsed = 0): Response
    {
        $color   = $ok ? '#16a34a' : '#dc2626';
        $bg      = $ok ? '#f0fdf4' : '#fef2f2';
        $border  = $ok ? '#bbf7d0' : '#fecaca';
        $elapsed_html = $elapsed > 0 ? "<p style='font-size:12px;color:#9ca3af;margin-top:8px'>Ejecutado en {$elapsed}ms</p>" : '';
        $detail_html  = $detail ? "<pre style='background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;font-size:12px;color:#64748b;overflow-x:auto;white-space:pre-wrap;margin-top:16px;line-height:1.6'>".htmlspecialchars($detail)."</pre>" : '';
        $orgName      = Organization::first()?->name ?? config('app.name', 'Nexova Desk');
        $statusIndicator = "<span style='width:8px;height:8px;border-radius:50%;background:{$color};display:inline-block;margin-right:6px'></span>";

        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Nexova Desk — {$title}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
.card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 40px 36px; max-width: 520px; width: 100%; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
.badge { display: inline-flex; align-items: center; gap: 6px; background: {$bg}; border: 1px solid {$border}; color: {$color}; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 99px; margin-bottom: 20px; }
h1 { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
p.msg { font-size: 14px; color: #475569; line-height: 1.6; }
.footer { margin-top: 28px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 11.5px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
<div class="card">
    <div class="badge">{$statusIndicator} Nexova Desk · Automatizacion</div>
    <h1>{$title}</h1>
    <p class="msg">{$message}</p>
    {$elapsed_html}
    {$detail_html}
    <div class="footer">Control de Automatizaciones de {$orgName}</div>
</div>
</body>
</html>
HTML;

        return response($html, $ok ? 200 : 500)->header('Content-Type', 'text/html; charset=utf-8');
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function runCron(Request $request, string $command, string $label): mixed
    {
        $start = microtime(true);
        try {
            Artisan::call($command);
            $output  = trim(Artisan::output()) ?: null;
            $elapsed = (int) round((microtime(true) - $start) * 1000);
            Log::info("[Cron HTTP] {$label} OK — {$elapsed}ms");

            if ($this->isBrowser($request)) {
                return $this->htmlResponse(
                    true,
                    $label . ' completado',
                    'La tarea se ejecutó correctamente.',
                    $output ?? '',
                    $elapsed
                );
            }

            return response()->json([
                'ok'      => true,
                'command' => $command,
                'elapsed' => "{$elapsed}ms",
                'output'  => $output ?? '(sin output)',
            ]);
        } catch (\Throwable $e) {
            Log::error("[Cron HTTP] Error en {$command}: {$e->getMessage()}");

            if ($this->isBrowser($request)) {
                return $this->htmlResponse(false, 'Error al ejecutar la tarea', $e->getMessage());
            }

            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Endpoints
    // ─────────────────────────────────────────────────────────────────────────

    /** Worker genérico — ejecuta todos los jobs del scheduler */
    public function worker(Request $request): mixed
    {
        return $this->runCron($request, 'schedule:run', 'Worker general');
    }

    /** Solo procesar emails IMAP entrantes */
    public function imap(Request $request): mixed
    {
        return $this->runCron($request, 'tickets:process-inbound', 'Revisar correo (IMAP)');
    }

    /** Solo verificar licencia partner */
    public function license(Request $request): mixed
    {
        return $this->runCron($request, 'partner:check-license', 'Verificar licencia');
    }

    /**
     * Diagnóstico IMAP — NO procesa mensajes, solo informa estado.
     */
    public function imapStatus(Request $request): mixed
    {
        if (! function_exists('imap_open')) {
            $msg = 'La extensión PHP IMAP no está instalada en este servidor.';
            if ($this->isBrowser($request)) {
                return $this->htmlResponse(false, 'IMAP no disponible', $msg);
            }
            return response()->json(['ok' => false, 'error' => $msg]);
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

        if ($this->isBrowser($request)) {
            $any    = ! empty($results);
            $allOk  = $any && collect($results)->every(fn ($r) => $r['status'] === 'connected');
            $detail = '';
            foreach ($results as $r) {
                if ($r['status'] === 'connected') {
                    $detail .= "✅ Cuenta #{$r['org_id']} ({$r['host']})\n";
                    $detail .= "   📬 Mensajes en buzón: {$r['total']}\n";
                    $detail .= "   📩 Sin leer: {$r['unseen']}\n\n";
                } else {
                    $detail .= "❌ Cuenta #{$r['org_id']} ({$r['host']}): {$r['error']}\n\n";
                }
            }
            if (! $any) $detail = 'No hay cuentas IMAP configuradas.';

            return $this->htmlResponse(
                $allOk,
                $allOk ? 'Conexión IMAP activa' : ($any ? 'Error de conexión IMAP' : 'Sin cuentas IMAP'),
                $allOk
                    ? 'El buzón de correo está correctamente configurado y accesible.'
                    : 'Revisa la configuración IMAP en Configuración Avanzada → Correo Electrónico.',
                trim($detail)
            );
        }

        return response()->json(['ok' => true, 'accounts' => $results]);
    }
}
