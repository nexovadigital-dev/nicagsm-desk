<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ActiveVisitor;
use Illuminate\Console\Command;

class PurgeStaleVisitors extends Command
{
    protected $signature   = 'chat:purge-visitors';
    protected $description = 'Delete active_visitors rows with no heartbeat in the last 30 seconds';

    public function handle(): int
    {
        $deleted = ActiveVisitor::where('last_ping_at', '<', now()->subSeconds(30))->delete();

        if ($deleted > 0) {
            $this->line("Purged {$deleted} stale visitor(s).");
        }

        return self::SUCCESS;
    }
}
