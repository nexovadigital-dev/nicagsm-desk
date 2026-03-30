<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Organization;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class TransactionsPage extends Page
{
    protected string $view = 'filament.superadmin.pages.transactions';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Transacciones';
    protected static string|\UnitEnum|null $navigationGroup = 'Planes & Pagos';
    protected static ?int $navigationSort = 22;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-banknotes';
    }

    public function getTitle(): string|Htmlable { return ''; }

    public static function getNavigationBadge(): ?string
    {
        $count = PaymentTransaction::where('status', 'pending')
            ->whereNotNull('tx_hash')
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public string $filterStatus = 'all';
    public string $search       = '';

    public function getTransactionsProperty()
    {
        return PaymentTransaction::with(['organization', 'plan'])
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->when(trim($this->search), fn ($q) =>
                $q->whereHas('organization', fn ($o) => $o->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhere('tx_hash', 'like', '%' . $this->search . '%')
                  ->orWhere('mp_payment_id', 'like', '%' . $this->search . '%')
            )
            ->orderByDesc('created_at')
            ->paginate(25);
    }

    /**
     * Manually confirm a crypto transaction (super-admin verified on blockchain).
     */
    public function confirmTransaction(int $id): void
    {
        $tx = PaymentTransaction::with('plan')->find($id);
        if (! $tx || $tx->status !== 'pending') return;

        $tx->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Activate subscription
        Subscription::where('organization_id', $tx->organization_id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $sub = Subscription::create([
            'organization_id' => $tx->organization_id,
            'plan_id'         => $tx->plan_id,
            'status'          => 'active',
            'amount_usd'      => $tx->amount_usd,
            'starts_at'       => now(),
            'ends_at'         => now()->addMonth(),
            'activated_by'    => auth()->id(),
            'notes'           => "Confirmado manualmente. TX: {$tx->tx_hash}",
        ]);

        $tx->update(['subscription_id' => $sub->id]);

        Organization::where('id', $tx->organization_id)->update([
            'plan'                     => $tx->plan->slug,
            'is_active'                => true,
            'max_bot_sessions_per_day' => $tx->plan->max_sessions_per_day,
            'max_messages_per_session' => $tx->plan->max_messages_per_session,
        ]);

        $this->dispatch('nexova-toast', type: 'success', message: 'Transacción confirmada y suscripción activada');
    }

    public function rejectTransaction(int $id): void
    {
        PaymentTransaction::where('id', $id)->where('status', 'pending')
            ->update(['status' => 'failed']);
        $this->dispatch('nexova-toast', type: 'info', message: 'Transacción marcada como fallida');
    }
}
