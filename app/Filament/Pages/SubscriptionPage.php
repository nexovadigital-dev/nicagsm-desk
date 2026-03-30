<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Mail\CryptoPaymentReceivedMail;
use App\Models\Organization;
use App\Models\PaymentConfig;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class SubscriptionPage extends Page
{
    protected string $view = 'filament.pages.subscription';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Mi Suscripción';
    protected static ?int $navigationSort     = 90;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-credit-card';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Cuenta';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // ── State ─────────────────────────────────────────────────────────────────
    public string $selectedMethod = '';  // crypto method or 'mercadopago'
    public string $txHash         = '';
    public ?int   $activeTxId     = null;

    // Crypto payment state (populated after initiation)
    public string $cryptoWallet   = '';
    public string $cryptoAmount   = '';
    public string $cryptoCurrency = '';
    public string $cryptoNetwork  = '';
    public string $cryptoExpiry   = '';
    public string $cryptoQrSvg    = '';

    // ── Computed properties ────────────────────────────────────────────────────
    public function getOrgProperty(): ?Organization
    {
        return auth()->user()?->organization;
    }

    public function getPlanProperty(): ?Plan
    {
        return Plan::where('slug', $this->org?->plan ?? 'free')->first();
    }

    public function getProPlanProperty(): ?Plan
    {
        return Plan::where('slug', 'pro')->first();
    }

    public function getAvailablePlansProperty()
    {
        return Plan::where('is_active', true)
            ->where('price_usd', '>', 0)
            ->orderBy('sort')
            ->get();
    }

    public function getActiveSubscriptionProperty(): ?Subscription
    {
        // Include cancelled subs so user can see expiry date
        return Subscription::where('organization_id', $this->org?->id)
            ->whereIn('status', ['active', 'cancelled'])
            ->where('ends_at', '>', now())
            ->latest('starts_at')
            ->first();
    }

    public function getRecentTransactionsProperty()
    {
        return PaymentTransaction::where('organization_id', $this->org?->id)
            ->with('plan')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    public function getActiveCryptoMethodsProperty(): array
    {
        return PaymentConfig::whereIn('method', [
            'usdt_trc20', 'usdt_bep20', 'usdt_polygon',
            'usdc_trc20', 'usdc_bep20', 'usdc_polygon',
        ])->where('is_active', true)->where('wallet_address', '!=', '')->get()
            ->keyBy('method')->toArray();
    }

    public function getIsMpActiveProperty(): bool
    {
        return PaymentConfig::where('method', 'mercadopago')
            ->where('is_active', true)
            ->whereNotNull('mp_access_token')
            ->exists();
    }

    public function getActivePendingTxProperty(): ?PaymentTransaction
    {
        if (! $this->org) return null;
        return PaymentTransaction::where('organization_id', $this->org->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    // ── Actions ───────────────────────────────────────────────────────────────
    public function initCryptoPay(string $method, string $planSlug = 'pro'): void
    {
        $org = $this->org;
        if (! $org) return;

        $config = PaymentConfig::where('method', $method)
            ->where('is_active', true)
            ->whereNotNull('wallet_address')
            ->first();

        if (! $config) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Método de pago no disponible');
            return;
        }

        $plan = Plan::where('slug', $planSlug)->where('is_active', true)->first() ?? $this->proPlan;
        if (! $plan) return;

        // Cancel previous pending crypto txs
        PaymentTransaction::where('organization_id', $org->id)
            ->where('status', 'pending')
            ->whereIn('method', ['usdt_trc20', 'usdt_bep20', 'usdt_polygon', 'usdc_trc20', 'usdc_bep20', 'usdc_polygon'])
            ->update(['status' => 'expired']);

        $tx = PaymentTransaction::create([
            'organization_id' => $org->id,
            'plan_id'         => $plan->id,
            'method'          => $method,
            'network'         => $config->network,
            'currency'        => $config->currency,
            'amount_usd'      => $plan->price_usd,
            'amount_crypto'   => $plan->price_usd, // USDT/USDC = 1:1 with USD
            'wallet_to'       => $config->wallet_address,
            'status'          => 'pending',
            'pending_at'      => now(),
            'expires_at'      => now()->addMinutes(60),
        ]);

        $this->activeTxId     = $tx->id;
        $this->cryptoWallet   = $config->wallet_address;
        $this->cryptoAmount   = number_format((float)$plan->price_usd, 2);
        $this->cryptoCurrency = $config->currency;
        $this->cryptoNetwork  = strtoupper($config->network ?? $method);
        $this->cryptoExpiry   = $tx->expires_at->toIso8601String();
        $this->cryptoQrSvg    = $this->generateQrBase64($config->wallet_address);
        $this->selectedMethod = $method;

        $this->dispatch('open-crypto-modal');
    }

    public function initMpPay(string $planSlug = 'pro'): void
    {
        $org  = $this->org;
        $plan = Plan::where('slug', $planSlug)->where('is_active', true)->first() ?? $this->proPlan;
        if (! $org || ! $plan) return;

        $mp = PaymentConfig::where('method', 'mercadopago')->where('is_active', true)->first();
        if (! $mp?->mp_access_token) {
            $this->dispatch('nexova-toast', type: 'error', message: 'MercadoPago no configurado');
            return;
        }

        $accessToken = decrypt($mp->mp_access_token);
        $externalRef = "org_{$org->id}_plan_{$plan->slug}_" . time();

        $response = Http::withToken($accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [[
                    'title'       => "Plan {$plan->name} — Nexova Desk",
                    'quantity'    => 1,
                    'currency_id' => 'USD',
                    'unit_price'  => (float) $plan->price_usd,
                ]],
                'back_urls' => [
                    'success' => url('/payment/mp/success'),
                    'failure' => url('/payment/mp/failure'),
                    'pending' => url('/payment/mp/pending'),
                ],
                'auto_return'        => 'approved',
                'notification_url'   => url('/api/webhooks/mercadopago'),
                'external_reference' => $externalRef,
                'expires'            => true,
                'expiration_date_to' => now()->addHours(2)->toIso8601String(),
            ]);

        if (! $response->successful()) {
            Log::error('[MP] Preference error', ['body' => $response->body()]);
            $this->dispatch('nexova-toast', type: 'error', message: 'Error iniciando pago con MercadoPago');
            return;
        }

        $data = $response->json();

        PaymentTransaction::create([
            'organization_id'  => $org->id,
            'plan_id'          => $plan->id,
            'method'           => 'mercadopago',
            'currency'         => 'USD',
            'amount_usd'       => $plan->price_usd,
            'mp_preference_id' => $data['id'],
            'status'           => 'pending',
            'pending_at'       => now(),
            'expires_at'       => now()->addHours(2),
        ]);

        $this->redirect($data['init_point']);
    }

    public function cancelSubscription(): void
    {
        $org = $this->org;
        if (! $org || $org->plan !== 'pro') {
            $this->dispatch('nexova-toast', type: 'error', message: 'No tienes una suscripción Pro activa');
            return;
        }

        // Mark active subscription as cancelled (keeps access until ends_at)
        Subscription::where('organization_id', $org->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        $this->dispatch('nexova-toast', type: 'success', message: 'Suscripción cancelada. Tendrás acceso Pro hasta el vencimiento.');
        $this->dispatch('close-cancel-modal');
    }

    public function submitTxHash(): void
    {
        $this->validate([
            'txHash'     => 'required|string|min:20|max:128',
            'activeTxId' => 'required|integer',
        ], [], ['txHash' => 'TX Hash', 'activeTxId' => 'Transacción']);

        $tx = PaymentTransaction::where('id', $this->activeTxId)
            ->where('organization_id', $this->org?->id)
            ->where('status', 'pending')
            ->first();

        if (! $tx) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Transacción no encontrada o ya procesada');
            return;
        }

        if ($tx->expires_at?->isPast()) {
            $tx->update(['status' => 'expired']);
            $this->dispatch('nexova-toast', type: 'error', message: 'El tiempo de pago expiró. Inicia un nuevo pago.');
            return;
        }

        $duplicate = PaymentTransaction::where('tx_hash', $this->txHash)
            ->where('id', '!=', $tx->id)
            ->exists();

        if ($duplicate) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Este TX hash ya fue registrado');
            return;
        }

        $tx->update(['tx_hash' => $this->txHash]);
        $this->txHash = '';
        $this->dispatch('close-crypto-modal');
        $this->dispatch('nexova-toast', type: 'success', message: 'Hash registrado. Verificaremos tu pago en la blockchain en breve.');

        // Notify superadmins by email
        try {
            $tx->load(['organization', 'plan']);
            $superAdmins = User::where('is_super_admin', true)->whereNotNull('email')->get();
            foreach ($superAdmins as $admin) {
                Mail::to($admin->email)->send(new CryptoPaymentReceivedMail($tx));
            }
        } catch (\Throwable $e) {
            Log::error('[Payment] Failed to notify superadmin: ' . $e->getMessage());
        }
    }

    private function generateQrBase64(string $content): string
    {
        try {
            $renderer = new ImageRenderer(new RendererStyle(180), new SvgImageBackEnd());
            $writer   = new Writer($renderer);
            return base64_encode($writer->writeString($content));
        } catch (\Throwable $e) {
            return '';
        }
    }
}
