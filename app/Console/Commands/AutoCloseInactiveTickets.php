<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Console\Command;

class AutoCloseInactiveTickets extends Command
{
    protected $signature   = 'tickets:auto-close {--hours=24 : Hours of inactivity before closing}';
    protected $description = 'Close bot-handled tickets with no activity in the last N hours';

    public function handle(): int
    {
        $hours    = (int) $this->option('hours');
        $cutoff   = now()->subHours($hours);

        // Only close tickets still in 'bot' status — never touch tickets with a human agent
        $tickets = Ticket::where('status', 'bot')
            ->where('updated_at', '<', $cutoff)
            ->get();

        $closed = 0;

        foreach ($tickets as $ticket) {
            $ticket->update(['status' => 'closed']);

            Message::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'bot',
                'content'     => 'Esta conversación fue cerrada automáticamente por inactividad. Si necesitas ayuda, puedes iniciar una nueva conversación.',
            ]);

            $closed++;
        }

        if ($closed > 0) {
            $this->info("Auto-closed {$closed} inactive ticket(s) (>{$hours}h without activity).");
        }

        return self::SUCCESS;
    }
}
