<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\PaymentConfig;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class PaymentConfigPage extends Page
{
    protected string $view = 'filament.superadmin.pages.payment-config';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Métodos de Pago';
    protected static string|\UnitEnum|null $navigationGroup = 'Planes & Pagos';
    protected static ?int $navigationSort = 21;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-credit-card';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // Crypto wallets
    public array $cryptoWallets = [];

    // MercadoPago
    public string $mpAccessToken = '';
    public string $mpPublicKey   = '';
    public string $mpCountry     = 'CO';
    public bool   $mpActive      = false;

    // Blockchain API keys (BSCScan, PolygonScan)
    public string $bscApiKey     = '';
    public string $polygonApiKey = '';

    protected static array $CRYPTO_METHODS = [
        'usdt_trc20'  => ['label' => 'USDT · TRC20 (Tron)',         'currency' => 'USDT', 'network' => 'trc20'],
        'usdt_bep20'  => ['label' => 'USDT · BEP20 (BNB Chain)',    'currency' => 'USDT', 'network' => 'bep20'],
        'usdt_polygon'=> ['label' => 'USDT · Polygon',              'currency' => 'USDT', 'network' => 'polygon'],
        'usdc_trc20'  => ['label' => 'USDC · TRC20 (Tron)',         'currency' => 'USDC', 'network' => 'trc20'],
        'usdc_bep20'  => ['label' => 'USDC · BEP20 (BNB Chain)',    'currency' => 'USDC', 'network' => 'bep20'],
        'usdc_polygon'=> ['label' => 'USDC · Polygon',              'currency' => 'USDC', 'network' => 'polygon'],
    ];

    public function mount(): void
    {
        foreach (self::$CRYPTO_METHODS as $method => $meta) {
            $cfg = PaymentConfig::where('method', $method)->first();
            $this->cryptoWallets[$method] = [
                'address'   => $cfg?->wallet_address ?? '',
                'is_active' => $cfg?->is_active ?? false,
                'label'     => $meta['label'],
            ];
        }

        $mp = PaymentConfig::where('method', 'mercadopago')->first();
        if ($mp) {
            $this->mpCountry = $mp->mp_country ?? 'CO';
            $this->mpActive  = $mp->is_active;
            // Don't pre-fill keys — security
        }

        $apis = PaymentConfig::where('method', 'blockchain_apis')->first();
        if ($apis && is_array($apis->metadata)) {
            $this->bscApiKey     = $apis->metadata['bsc_api_key']     ?? '';
            $this->polygonApiKey = $apis->metadata['polygon_api_key'] ?? '';
        }
    }

    public function getCryptoMethods(): array
    {
        return self::$CRYPTO_METHODS;
    }

    public function saveCrypto(string $method): void
    {
        $meta   = self::$CRYPTO_METHODS[$method] ?? null;
        if (! $meta) return;

        $wallet  = trim($this->cryptoWallets[$method]['address'] ?? '');
        $active  = (bool) ($this->cryptoWallets[$method]['is_active'] ?? false);

        if (! $wallet && $active) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Ingresa la dirección de wallet antes de activar');
            return;
        }

        PaymentConfig::updateOrCreate(
            ['method' => $method],
            [
                'label'          => $meta['label'],
                'network'        => $meta['network'],
                'currency'       => $meta['currency'],
                'wallet_address' => $wallet ?: null,
                'is_active'      => $wallet ? $active : false,
            ]
        );

        $this->dispatch('nexova-toast', type: 'success', message: "Configuración {$meta['label']} guardada");
    }

    public function saveBlockchainApis(): void
    {
        $meta = [];
        if (trim($this->bscApiKey)) {
            $meta['bsc_api_key'] = trim($this->bscApiKey);
        }
        if (trim($this->polygonApiKey)) {
            $meta['polygon_api_key'] = trim($this->polygonApiKey);
        }

        $existing = PaymentConfig::where('method', 'blockchain_apis')->first();
        $existing_meta = $existing && is_array($existing->metadata) ? $existing->metadata : [];
        $merged = array_merge($existing_meta, $meta);

        PaymentConfig::updateOrCreate(
            ['method' => 'blockchain_apis'],
            ['label' => 'Blockchain APIs', 'metadata' => $merged, 'is_active' => true]
        );

        $this->bscApiKey = '';
        $this->polygonApiKey = '';
        $this->dispatch('nexova-toast', type: 'success', message: 'API keys guardadas');
    }

    public function saveMercadoPago(): void
    {
        $data = [
            'method'    => 'mercadopago',
            'label'     => 'MercadoPago',
            'network'   => null,
            'currency'  => 'COP',
            'mp_country'=> $this->mpCountry,
            'is_active' => $this->mpActive,
        ];

        if (trim($this->mpAccessToken)) {
            $data['mp_access_token'] = encrypt(trim($this->mpAccessToken));
        }
        if (trim($this->mpPublicKey)) {
            $data['mp_public_key'] = encrypt(trim($this->mpPublicKey));
        }

        PaymentConfig::updateOrCreate(['method' => 'mercadopago'], $data);

        $this->mpAccessToken = '';
        $this->mpPublicKey   = '';

        $this->dispatch('nexova-toast', type: 'success', message: 'MercadoPago configurado');
    }
}
