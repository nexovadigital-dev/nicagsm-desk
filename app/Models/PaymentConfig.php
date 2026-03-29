<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
    protected $fillable = [
        'method', 'label', 'network', 'currency',
        'wallet_address',
        'mp_access_token', 'mp_public_key', 'mp_country',
        'metadata', 'is_active',
    ];

    protected $hidden = ['mp_access_token', 'mp_public_key'];

    protected $casts = [
        'metadata'  => 'array',
        'is_active' => 'boolean',
    ];

    public function isCrypto(): bool
    {
        return in_array($this->method, [
            'usdt_trc20', 'usdt_bep20', 'usdt_polygon',
            'usdc_trc20', 'usdc_bep20', 'usdc_polygon',
        ]);
    }

    public function isMercadoPago(): bool
    {
        return $this->method === 'mercadopago';
    }

    public function getMpAccessToken(): ?string
    {
        return $this->mp_access_token ? decrypt($this->mp_access_token) : null;
    }

    public function getMpPublicKey(): ?string
    {
        return $this->mp_public_key ? decrypt($this->mp_public_key) : null;
    }

    /**
     * Human-readable network name for display.
     */
    public function networkLabel(): string
    {
        return match ($this->network) {
            'trc20'   => 'Red Tron (TRC20)',
            'bep20'   => 'BNB Smart Chain (BEP20)',
            'polygon' => 'Polygon (MATIC)',
            default   => $this->network ?? '',
        };
    }

    /**
     * Block explorer URL for a given tx hash.
     */
    public function explorerUrl(string $txHash): ?string
    {
        return match ($this->network) {
            'trc20'   => "https://tronscan.org/#/transaction/{$txHash}",
            'bep20'   => "https://bscscan.com/tx/{$txHash}",
            'polygon' => "https://polygonscan.com/tx/{$txHash}",
            default   => null,
        };
    }
}
