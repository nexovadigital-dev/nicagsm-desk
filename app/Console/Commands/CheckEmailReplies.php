<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Console\Command;

class CheckEmailReplies extends Command
{
    protected $signature   = 'nexova:check-email-replies';
    protected $description = 'Poll the support mailbox for email replies and attach them to tickets';

    public function handle(): int
    {
        if (! extension_loaded('imap')) {
            $this->error('PHP IMAP extension is not loaded.');
            return self::FAILURE;
        }

        $host     = config('mail.imap.host',     '');
        $port     = config('mail.imap.port',     993);
        $enc      = config('mail.imap.encryption', 'ssl');
        $user     = config('mail.imap.username', '');
        $pass     = config('mail.imap.password', '');
        $folder   = config('mail.imap.folder',   'INBOX');

        if (! $host || ! $user || ! $pass) {
            $this->warn('IMAP credentials not configured (mail.imap.*). Skipping.');
            return self::SUCCESS;
        }

        $encFlag  = match(strtolower($enc)) {
            'ssl'   => '/ssl',
            'tls'   => '/tls',
            'notls' => '/notls',
            default => '/ssl',
        };

        $mailbox = "{{$host}:{$port}/imap{$encFlag}}{$folder}";

        $imap = @imap_open($mailbox, $user, $pass, 0, 1);
        if (! $imap) {
            $this->error('Could not connect to IMAP: ' . imap_last_error());
            return self::FAILURE;
        }

        $unseen = imap_search($imap, 'UNSEEN');

        if (! $unseen) {
            $this->info('No new messages.');
            imap_close($imap);
            return self::SUCCESS;
        }

        $processed = 0;

        foreach ($unseen as $msgNum) {
            $header  = imap_headerinfo($imap, $msgNum);
            $subject = imap_utf8($header->subject ?? '');

            // Find ticket reply token embedded in subject: [TKT-XXXXX] or ticket number
            $ticket = $this->resolveTicket($subject);

            if (! $ticket) {
                // Try X-Ticket-Token header
                $rawHeader = imap_fetchheader($imap, $msgNum);
                preg_match('/X-Ticket-Token:\s*([a-zA-Z0-9]+)/i', $rawHeader, $m);
                if (! empty($m[1])) {
                    $ticket = Ticket::where('ticket_reply_token', $m[1])->first();
                }
            }

            if (! $ticket) {
                // Mark as seen and skip — not a ticket reply
                imap_setflag_full($imap, (string) $msgNum, '\\Seen');
                continue;
            }

            $body = $this->getBody($imap, $msgNum);
            $body = $this->stripQuotedReply($body);
            $body = trim($body);

            if ($body) {
                Message::create([
                    'ticket_id'   => $ticket->id,
                    'sender_type' => 'user',
                    'content'     => $body,
                ]);
                $processed++;
            }

            // Mark as seen
            imap_setflag_full($imap, (string) $msgNum, '\\Seen');
        }

        imap_close($imap);

        $this->info("Processed {$processed} email reply(ies).");
        return self::SUCCESS;
    }

    private function resolveTicket(string $subject): ?Ticket
    {
        // Match "TKT-00001" anywhere in the subject
        if (preg_match('/TKT-\d{5}/i', $subject, $m)) {
            return Ticket::where('ticket_number', strtoupper($m[0]))->first();
        }
        return null;
    }

    private function getBody(\IMAP\Connection $imap, int $msgNum): string
    {
        $structure = imap_fetchstructure($imap, $msgNum);

        if (! isset($structure->parts)) {
            // Single-part message
            $body = imap_fetchbody($imap, $msgNum, '1');
            return $this->decode($body, $structure->encoding ?? 0);
        }

        // Prefer text/plain part
        foreach ($structure->parts as $i => $part) {
            if (strtolower($part->subtype ?? '') === 'plain') {
                $body = imap_fetchbody($imap, $msgNum, (string) ($i + 1));
                return $this->decode($body, $part->encoding ?? 0);
            }
        }

        // Fallback: first part
        $body = imap_fetchbody($imap, $msgNum, '1');
        return $this->decode($body, $structure->parts[0]->encoding ?? 0);
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
     * Strip the quoted/forwarded reply portion (everything after "On ... wrote:" or "--").
     */
    private function stripQuotedReply(string $text): string
    {
        // Remove lines starting with > (quoted lines)
        $lines  = explode("\n", $text);
        $result = [];
        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), '>')) continue;
            $result[] = $line;
        }
        $text = implode("\n", $result);

        // Trim everything after the "On ... wrote:" separator
        $text = preg_replace('/\n?On .+wrote:.*$/si', '', $text);

        // Trim after common reply separators
        foreach (['--- ', '---- ', '________', '________________________________'] as $sep) {
            if (($pos = strpos($text, "\n{$sep}")) !== false) {
                $text = substr($text, 0, $pos);
            }
        }

        return $text;
    }
}
