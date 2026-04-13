<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\SmtpSetting;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * ProcessInboundEmails
 *
 * Polls each organization's IMAP inbox every minute (via scheduler).
 * When a client replies to a ticket email, this command:
 *   1. Parses the subject to find the ticket number  (e.g. "Re: Ticket #TKT-00001 …")
 *   2. Creates a Message with sender_type = 'visitor'
 *   3. Reopens the ticket if it was closed
 *   4. Marks the email as \Seen so it is not processed again
 *
 * Requires PHP IMAP extension: php-imap (usually pre-installed on cPanel / Hostinger).
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

        // Process every org that has IMAP enabled
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

        $dsn = $this->buildDsn($smtp, $folder);

        $inbox = @imap_open($dsn, $smtp->imap_username, $smtp->imap_password, 0, 1);

        if (! $inbox) {
            Log::warning("[IMAP] Could not connect for org #{$orgId}: " . imap_last_error());
            return;
        }

        try {
            // Fetch unseen messages only
            $uids = imap_search($inbox, 'UNSEEN', SE_UID);

            if (! $uids) {
                return; // nothing new
            }

            foreach ($uids as $uid) {
                $this->processMessage($inbox, $uid, $orgId);
            }
        } finally {
            imap_close($inbox, CL_EXPUNGE);
        }
    }

    private function processMessage($inbox, int $uid, int $orgId): void
    {
        $header  = imap_rfc822_parse_headers(imap_fetchheader($inbox, $uid, FT_UID));
        $subject = isset($header->subject) ? imap_utf8($header->subject) : '';

        // ── Extract ticket number from subject ────────────────────────────────
        // Matches: TKT-00001 anywhere in the subject (case-insensitive)
        if (! preg_match('/TKT-\d+/i', $subject, $m)) {
            // Not a ticket reply — mark as seen and skip
            imap_setflag_full($inbox, (string) $uid, '\\Seen', ST_UID);
            return;
        }

        $ticketNumber = strtoupper($m[0]);

        $ticket = Ticket::where('organization_id', $orgId)
            ->where('ticket_number', $ticketNumber)
            ->first();

        if (! $ticket) {
            imap_setflag_full($inbox, (string) $uid, '\\Seen', ST_UID);
            return;
        }

        // ── Extract plain-text body ───────────────────────────────────────────
        $body = $this->getPlainText($inbox, $uid);
        $body = $this->stripQuotedReply($body);

        if (empty(trim($body))) {
            imap_setflag_full($inbox, (string) $uid, '\\Seen', ST_UID);
            return;
        }

        // ── Create the message ────────────────────────────────────────────────
        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'visitor',
            'content'     => trim($body),
        ]);

        // Reopen if closed
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'human']);
        }

        // Mark as read so we don't process it again
        imap_setflag_full($inbox, (string) $uid, '\\Seen', ST_UID);

        Log::info("[IMAP] Ticket {$ticketNumber} — new reply from client (org #{$orgId})");
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

    /**
     * Return the plain-text body of a message.
     * Falls back to stripping HTML tags if only HTML part exists.
     */
    private function getPlainText($inbox, int $uid): string
    {
        $structure = imap_fetchstructure($inbox, $uid, FT_UID);

        // Simple (non-multipart) message
        if (! isset($structure->parts)) {
            $body = imap_fetchbody($inbox, $uid, '1', FT_UID);
            return $this->decode($body, $structure->encoding);
        }

        // Multipart — find text/plain first, then text/html
        foreach ($structure->parts as $i => $part) {
            $subtype = strtolower($part->subtype ?? '');
            if ($subtype === 'plain') {
                $raw = imap_fetchbody($inbox, $uid, (string) ($i + 1), FT_UID);
                return $this->decode($raw, $part->encoding);
            }
        }

        // Fallback to HTML part
        foreach ($structure->parts as $i => $part) {
            $subtype = strtolower($part->subtype ?? '');
            if ($subtype === 'html') {
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
     * Remove the quoted original message that most email clients append
     * when replying (e.g. "On ... wrote:", ">..." lines).
     */
    private function stripQuotedReply(string $body): string
    {
        // Remove lines starting with ">"
        $lines = explode("\n", $body);
        $clean = [];
        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), '>')) continue;
            $clean[] = $line;
        }
        $body = implode("\n", $clean);

        // Remove common "On [date] ... wrote:" patterns
        $body = preg_replace('/\r?\nOn .+wrote:\r?\n/s', '', $body);

        return trim($body);
    }
}
