<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'organization_id', 'subscription_id', 'plan_id',
        'method', 'network', 'currency',
        'amount_usd', 'amount_crypto', 'amount_local', 'exchange_rate',
        'wallet_to', 'wallet_from', 'tx_hash',
        'mp_preference_id', 'mp_payment_id', 'mp_payment_status',
        'status', 'confirmed_at', 'expires_at', 'metadata',
    ];

    protected $casts = [
        'amount_usd'    => 'decimal:2',
        'amount_crypto' => 'decimal:8',
        'amount_local'  => 'decimal:2',
        'confirmed_at'  => 'datetime',
        'expires_at'    => 'datetime',
        'metadata'      => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isCrypto(): bool
    {
        return in_array($this->method, [
            'usdt_trc20', 'usdt_bep20', 'usdt_polygon',
            'usdc_trc20', 'usdc_bep20', 'usdc_polygon',
        ]);
    }

    public function isExpired(): bool
    {
        return $this->status === 'pending' && $this->expires_at?->isPast();
    }

    public function explorerUrl(): ?string
    {
        if (! $this->tx_hash) return null;
        $config = PaymentConfig::where('method', $this->method)->first();
        return $config?->explorerUrl($this->tx_hash);
    }
}
