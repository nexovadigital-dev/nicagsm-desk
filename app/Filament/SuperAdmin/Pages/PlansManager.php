<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Plan;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class PlansManager extends Page
{
    protected string $view = 'filament.superadmin.pages.plans-manager';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Planes';
    protected static string|\UnitEnum|null $navigationGroup = 'Planes & Pagos';
    protected static ?int $navigationSort = 20;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-rectangle-stack';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // Edit / create state
    public ?int    $editingId               = null;
    public bool    $creating                = false;
    public string  $editName                = '';
    public string  $editSlug                = '';
    public string  $editDescription         = '';
    public string  $editPriceUsd            = '0';
    public int     $editMaxAgents           = 1;
    public int     $editMaxWidgets          = 1;
    public int     $editMaxSessionsPerDay   = 50;
    public int     $editMaxMsgPerSession    = 20;
    public bool    $editIsActive            = true;
    public bool    $editAiBlocked           = false;
    public int     $editMaxBotMessages      = 1000;
    public int     $editSort                = 10;
    public array   $editFeatures            = [];

    public const AVAILABLE_FEATURES = [
        'kb_manual'        => 'Artículos manuales en KB',
        'kb_scrape'        => 'Escaneo automático del sitio',
        'kb_wordpress'     => 'Integración WooCommerce',
        'ai_enabled'       => 'IA generativa activada',
        'own_api_keys'     => 'Usa tus propias claves de IA',
        'unlimited_agents' => 'Agentes ilimitados',
        'telegram'         => 'Canal Telegram',
        'woocommerce'      => 'Plugin WooCommerce',
        'priority_support' => 'Soporte prioritario',
    ];

    public function getPlansProperty()
    {
        return Plan::orderBy('sort')->get();
    }

    public function createPlan(): void
    {
        $this->creating              = true;
        $this->editingId             = null;
        $this->editName              = '';
        $this->editSlug              = '';
        $this->editDescription       = '';
        $this->editPriceUsd          = '0';
        $this->editMaxAgents         = 5;
        $this->editMaxWidgets        = 3;
        $this->editMaxSessionsPerDay = 200;
        $this->editMaxMsgPerSession  = 20;
        $this->editIsActive          = true;
        $this->editAiBlocked         = false;
        $this->editMaxBotMessages    = 0;
        $this->editSort              = 10;
        $this->editFeatures          = [];
        $this->dispatch('open-plan-modal');
    }

    public function edit(int $id): void
    {
        $p = Plan::find($id);
        if (! $p) return;
        $this->creating              = false;
        $this->editingId             = $id;
        $this->editName              = $p->name;
        $this->editSlug              = $p->slug;
        $this->editDescription       = $p->description ?? '';
        $this->editPriceUsd          = (string) $p->price_usd;
        $this->editMaxAgents         = $p->max_agents;
        $this->editMaxWidgets        = $p->max_widgets;
        $this->editMaxSessionsPerDay = $p->max_sessions_per_day;
        $this->editMaxMsgPerSession  = $p->max_messages_per_session;
        $this->editIsActive          = $p->is_active;
        $this->editAiBlocked         = (bool) $p->ai_blocked;
        $this->editMaxBotMessages    = $p->max_bot_messages_monthly ?? 0;
        $this->editSort              = $p->sort ?? 10;
        $this->editFeatures          = is_array($p->features) ? $p->features : [];
        $this->dispatch('open-plan-modal');
    }

    public function savePlan(): void
    {
        if (strlen(trim($this->editName)) < 2) {
            $this->dispatch('nexova-toast', type: 'error', message: 'El nombre del plan es requerido.');
            return;
        }

        $slug = trim($this->editSlug) ?: \Illuminate\Support\Str::slug($this->editName);

        $data = [
            'name'                     => trim($this->editName),
            'slug'                     => $slug,
            'description'              => trim($this->editDescription),
            'price_usd'                => (float) $this->editPriceUsd,
            'max_agents'               => $this->editMaxAgents,
            'max_widgets'              => $this->editMaxWidgets,
            'max_sessions_per_day'     => $this->editMaxSessionsPerDay,
            'max_messages_per_session' => $this->editMaxMsgPerSession,
            'is_active'                => $this->editIsActive,
            'ai_blocked'               => $this->editAiBlocked,
            'max_bot_messages_monthly' => $this->editMaxBotMessages,
            'sort'                     => $this->editSort,
            'features'                 => array_values($this->editFeatures),
        ];

        if ($this->creating) {
            Plan::create($data);
            $msg = 'Plan creado correctamente';
        } else {
            Plan::where('id', $this->editingId)->update($data);
            $msg = 'Plan actualizado';
        }

        $this->editingId = null;
        $this->creating  = false;
        $this->dispatch('close-plan-modal');
        $this->dispatch('nexova-toast', type: 'success', message: $msg);
    }
}
