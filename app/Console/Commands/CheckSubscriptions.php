<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\Subscription;
use App\Models\PaymentTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSubscriptions extends Command
{
    protected $signature   = 'nexova:check-subscriptions';
    protected $description = 'Expire ended subscriptions and downgrade orgs to Free plan';

    public function handle(): void
    {
        // 1. Expire ended subscriptions
        $expired = Subscription::where('status', 'active')
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expired as $sub) {
            $sub->update(['status' => 'expired']);

            Organization::where('id', $sub->organization_id)->update([
                'plan' => 'free',
            ]);

            Log::info("[CheckSubscriptions] Subscription #{$sub->id} expired → org {$sub->organization_id} → plan: free");
            $this->line("Org {$sub->organization_id}: subscription #{$sub->id} expired → plan: free");
        }

        // 2. Expire pending crypto transactions older than 60 min with no hash
        $expiredTx = PaymentTransaction::where('status', 'pending')
            ->whereNull('tx_hash')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Done. Expired subscriptions: {$expired->count()} | Expired pending crypto TXs: {$expiredTx}");
    }
}
