<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Console\Command;

class ExpireAgentCalls extends Command
{
    protected $signature   = 'chat:expire-agent-calls';
    protected $description = 'Revert tickets stuck waiting for an agent past their configured timeout';

    public function handle(): int
    {
        // Only tickets waiting for agent with no agent messages yet
        $tickets = Ticket::where('status', 'human')
            ->whereNotNull('agent_called_at')
            ->whereDoesntHave('messages', fn ($q) => $q->where('sender_type', 'agent'))
            ->with('widget')
            ->get();

        $reverted = 0;

        foreach ($tickets as $ticket) {
            $timeoutMin = $ticket->widget?->agent_call_timeout ?? 10;
            $deadline   = $ticket->agent_called_at->addMinutes($timeoutMin);

            if (now()->lessThan($deadline)) {
                continue;
            }

            $ticket->update([
                'status'          => 'bot',
                'agent_called_at' => null,
            ]);
            Message::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'bot',
                'content'     => 'No hay agentes disponibles en este momento. Sin embargo, tu llamado fue enviado y es probable que un agente se conecte en cualquier momento. 💬 Te recomiendo dejar tus datos de contacto (email, número de WhatsApp o Telegram) en este chat — así los agentes podrán comunicarse contigo directamente.',
            ]);

            $reverted++;
        }

        if ($reverted > 0) {
            $this->info("Reverted {$reverted} expired agent call(s).");
        }

        return self::SUCCESS;
    }
}
