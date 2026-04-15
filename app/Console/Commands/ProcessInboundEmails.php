<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\TicketReopenBlockedMail;
use App\Models\Message;
use App\Models\SmtpSetting;
use App\Models\Ticket;
use App\Services\OrgMailer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * ProcessInboundEmails
 *
 * Revisa el buzón IMAP de cada organización y procesa los emails que son
 * respuestas a tickets (identificados por TKT-XXXXX en el asunto).
 *
 * Busca los últimos 2 días de mensajes (leídos o no leídos) y usa una caché
 * de UIDs procesados para no crear mensajes duplicados. Esto permite que
 * el buzón sea revisado por un cliente de correo externo sin perder replies.
 */
class ProcessInboundEmails extends Command
{
    protected $signature   = 'tickets:process-inbound';
    protected $description = 'Poll IMAP inboxes and inject client email replies into tickets';

    public function handle(): int
    {
        if (! function_exists('imap_open')) {
            $this->error('PHP IMAP extension is not installed. Enable php-imap on your server.');
            return self::FAILURE;
        }

        SmtpSetting::where('imap_enabled', true)
            ->whereNotNull('imap_host')
            ->whereNotNull('imap_username')
            ->whereNotNull('imap_password')
            ->each(fn ($smtp) => $this->processOrg($smtp));

        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────────────────────────────────

    private function processOrg(SmtpSetting $smtp): void
    {
        $orgId  = $smtp->organization_id;
        $folder = $smtp->imap_folder ?: 'INBOX';
        $dsn    = $this->buildDsn($smtp, $folder);

        $inbox = @imap_open($dsn, $smtp->imap_username, $smtp->imap_password, 0, 1);

        if (! $inbox) {
            Log::warning("[IMAP] No se pudo conectar para org #{$orgId}: " . imap_last_error());
            return;
        }

        try {
            // Buscar emails de las últimas 48 horas (leídos Y no leídos)
            // Solución para buzones monitoreados: no depende del flag UNSEEN.
            // Se usa caché de UIDs para evitar procesar el mismo mensaje dos veces.
            $since = date('d-M-Y', strtotime('-2 days'));
            $uids  = imap_search($inbox, "SINCE \"{$since}\"", SE_UID);

            if (! $uids) {
                Log::info("[IMAP] org #{$orgId}: sin mensajes en los últimos 2 días");
                return;
            }

            $processed = 0;
            foreach ($uids as $uid) {
                $cacheKey = "imap_uid_{$orgId}_{$uid}";

                // Si ya fue procesado como ticket o skip, saltar
                $cached = Cache::get($cacheKey);
                if ($cached === 'ticket' || $cached === 'skip') {
                    continue;
                }

                $wasTicket = $this->processMessage($inbox, $uid, $orgId);

                // Cachear resultado. TTL 72h.
                // 'ticket' = procesado como reply de ticket
                // 'skip'   = no era reply de ticket (no tiene TKT-XXXXX)
                Cache::put($cacheKey, $wasTicket ? 'ticket' : 'skip', now()->addHours(72));

                if ($wasTicket) {
                    $processed++;
                }
            }

            Log::info("[IMAP] org #{$orgId}: revisados " . count($uids) . " msgs — {$processed} replies de ticket añadidos");
        } finally {
            imap_close($inbox);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Procesa un email. Retorna true si era una respuesta de ticket que se procesó.
     */
    private function processMessage($inbox, int $uid, int $orgId): bool
    {
        $rawSubject = imap_fetchheader($inbox, $uid, FT_UID);
        $header     = imap_rfc822_parse_headers($rawSubject);

        // Decodificar subject: primero imap_utf8(), luego mb_decode_mimeheader() como refuerzo.
        // Los clientes de correo (Gmail, Outlook) encodifican caracteres especiales ([, ], —, ñ)
        // como MIME encoded-words: =?UTF-8?Q?Re=3A_=5BTKT=2D00003=5D...?=
        $rawSub  = isset($header->subject) ? $header->subject : '';
        $subject = imap_utf8($rawSub);
        // Si aún quedan secuencias MIME sin decodificar, aplicar mb_decode_mimeheader
        if (str_contains($subject, '=?') || str_contains($subject, '?=')) {
            $subject = mb_decode_mimeheader($rawSub);
        }
        $subject = mb_convert_encoding($subject, 'UTF-8', 'UTF-8');

        Log::info("[IMAP] uid={$uid} org=#{$orgId}: subject='{$subject}'");

        // Detectar TKT-XXXXX en el asunto en cualquier formato:
        // Re: [TKT-00001], Re: Ticket TKT-00001, Fwd: TKT-00001, etc.
        if (! preg_match('/\bTKT-?(\d+)\b/i', $subject, $m)) {
            Log::info("[IMAP] uid={$uid} org=#{$orgId}: sin TKT en asunto — omitido");
            return false; // No es respuesta de ticket
        }

        $ticketNumber = 'TKT-' . str_pad($m[1], 5, '0', STR_PAD_LEFT);

        $ticket = Ticket::where('organization_id', $orgId)
            ->where('ticket_number', $ticketNumber)
            ->first();

        if (! $ticket) {
            Log::warning("[IMAP] org #{$orgId}: ticket {$ticketNumber} no encontrado");
            return false;
        }

        // Extraer y limpiar el cuerpo del email
        $rawBody     = $this->getPlainText($inbox, $uid);
        $strippedBody = $this->stripQuotedReply($rawBody);

        // Si tras strip el cuerpo quedó vacío, usar los primeros 800 chars del cuerpo
        // original como fallback (previene perder emails cuando el patrón de strip
        // es demasiado agresivo con el formato del cliente de correo del usuario).
        if (empty(trim($strippedBody))) {
            $fallback = trim(mb_substr($rawBody, 0, 800));
            if (empty($fallback)) {
                Log::info("[IMAP] uid={$uid} org=#{$orgId}: body vacío antes y después del strip — descartado");
                return false;
            }
            Log::info("[IMAP] uid={$uid} org=#{$orgId}: strip dejó vacío, usando cuerpo original (primeros 800 chars)");
            $body = $fallback;
        } else {
            $body = $strippedBody;
        }

        Log::info("[IMAP] uid={$uid} org=#{$orgId}: body extraído (" . strlen($body) . " chars)");

        // Ticket cerrado: NO reabrir — enviar aviso automático al cliente
        if ($ticket->status === 'closed') {
            $this->sendClosedNotice($ticket, $header);
            Log::info("[IMAP] Ticket {$ticketNumber} cerrado — enviado aviso al cliente (org #{$orgId})");
            return true; // Sí era ticket reply (aunque cerrado)
        }

        // Crear el mensaje en el ticket
        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'user',   // 'user' = cliente
            'content'     => trim($body),
        ]);

        // Subir el ticket al tope del Live Inbox
        $ticket->touch();

        Log::info("[IMAP] Ticket {$ticketNumber} — nuevo reply del cliente (org #{$orgId})");

        return true;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Aviso de ticket cerrado
    // ──────────────────────────────────────────────────────────────────────────

    private function sendClosedNotice(Ticket $ticket, object $header): void
    {
        $senderAddr = null;

        if (! empty($header->reply_toaddress)) {
            $parsed = imap_rfc822_parse_adrlist($header->reply_toaddress, 'localhost');
            $senderAddr = ($parsed[0]->mailbox ?? '') . '@' . ($parsed[0]->host ?? '');
        }
        if (! $senderAddr || str_ends_with($senderAddr, '@localhost')) {
            $parsed = imap_rfc822_parse_adrlist($header->fromaddress ?? '', 'localhost');
            $senderAddr = ($parsed[0]->mailbox ?? '') . '@' . ($parsed[0]->host ?? '');
        }

        if (! $senderAddr || str_ends_with($senderAddr, '@localhost')) {
            Log::warning("[IMAP] sendClosedNotice: no se pudo obtener el email del remitente para ticket #{$ticket->ticket_number}");
            return;
        }

        $fresh = $ticket->fresh(['organization']);
        $org   = $fresh?->organization;

        if (! $org) {
            return;
        }

        try {
            $mailerName = OrgMailer::mailerNameFor($org);
            $mailable   = new TicketReopenBlockedMail($fresh);
            $mailerName
                ? Mail::mailer($mailerName)->to($senderAddr)->send($mailable)
                : Mail::to($senderAddr)->send($mailable);
        } catch (\Throwable $e) {
            Log::error("[IMAP] sendClosedNotice error: {$e->getMessage()}");
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function buildDsn(SmtpSetting $smtp, string $folder): string
    {
        $enc = match ($smtp->imap_encryption) {
            'ssl'  => '/ssl',
            'tls'  => '/tls',
            'none' => '/novalidate-cert',
            default => '/ssl',
        };

        $port = $smtp->imap_port ?: 993;

        return "{{$smtp->imap_host}:{$port}/imap{$enc}}{$folder}";
    }

    private function getPlainText($inbox, int $uid): string
    {
        $structure = imap_fetchstructure($inbox, $uid, FT_UID);

        if (! isset($structure->parts)) {
            $body = imap_fetchbody($inbox, $uid, '1', FT_UID);
            return $this->decode($body, $structure->encoding);
        }

        foreach ($structure->parts as $i => $part) {
            if (strtolower($part->subtype ?? '') === 'plain') {
                $raw = imap_fetchbody($inbox, $uid, (string) ($i + 1), FT_UID);
                return $this->decode($raw, $part->encoding);
            }
        }

        foreach ($structure->parts as $i => $part) {
            if (strtolower($part->subtype ?? '') === 'html') {
                $raw  = imap_fetchbody($inbox, $uid, (string) ($i + 1), FT_UID);
                $html = $this->decode($raw, $part->encoding);
                return strip_tags($html);
            }
        }

        return '';
    }

    private function decode(string $body, int $encoding): string
    {
        return match ($encoding) {
            3 => base64_decode($body),
            4 => quoted_printable_decode($body),
            default => $body,
        };
    }

    /**
     * Elimina el hilo citado que los clientes de correo añaden al responder.
     * Soporta patrones de Gmail (español e inglés), Outlook y Apple Mail.
     */
    private function stripQuotedReply(string $body): string
    {
        // Normalizar saltos de línea
        $body = str_replace("\r\n", "\n", $body);

        // ── 1. Gmail ES: "El lun, 13 abr 2026, 3:50 p.m., X (<email>) escribió:" ──
        $body = preg_replace(
            '/\nEl\s+(?:lun|mar|mi[eé]|jue|vie|s[aá]b|dom).*?escribi[oó]:?.*/isu',
            '',
            $body
        );

        // ── 2. Gmail EN: "On Mon, Apr 13, 2026 at 2:55 PM, X <email> wrote:" ──
        $body = preg_replace(
            '/\nOn\s+(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun).*?wrote:?.*/isu',
            '',
            $body
        );

        // ── 3. Líneas que empiezan con ">" (bloques citados) ──────────────────
        $lines   = explode("\n", $body);
        $clean   = [];
        $inQuote = false;
        foreach ($lines as $line) {
            $trimmed = ltrim($line);
            if (str_starts_with($trimmed, '>')) {
                $inQuote = true;
                continue;
            }
            if ($inQuote && trim($line) === '') {
                continue; // línea vacía tras bloque citado
            }
            $inQuote = false;
            $clean[] = $line;
        }
        $body = implode("\n", $clean);

        // ── 4. Separadores de Outlook ("From:", "De:", "________") ─────────────
        $body = preg_replace('/\n-{5,}.*?(From|De):.+/si', '', $body);
        $body = preg_replace('/\n(From|De):\s*.+\n(Sent|Enviado|Date|Fecha):.*/si', '', $body);
        $body = preg_replace('/\n_{5,}.*$/s', '', $body);

        // ── 5. Limpiar espacios extra ──────────────────────────────────────────
        $body = preg_replace('/\n{3,}/', "\n\n", $body);

        return trim($body);
    }
}

