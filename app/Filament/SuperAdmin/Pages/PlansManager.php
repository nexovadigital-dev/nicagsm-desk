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

    // Edit state
    public ?int    $editingId               = null;
    public string  $editName                = '';
    public string  $editSlug                = '';
    public string  $editDescription         = '';
    public string  $editPriceUsd            = '0';
    public int     $editMaxAgents           = 1;
    public int     $editMaxWidgets          = 1;
    public int     $editMaxSessionsPerDay   = 50;
    public int     $editMaxMsgPerSession    = 20;
    public bool    $editIsActive            = true;
    public bool    $editAiBlocked          = false;
    public int     $editMaxBotMessages     = 1000;

    public function getPlansProperty()
    {
        return Plan::orderBy('sort')->get();
    }

    public function edit(int $id): void
    {
        $p = Plan::find($id);
        if (! $p) return;
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
        $this->dispatch('open-plan-modal');
    }

    public function savePlan(): void
    {
        Plan::where('id', $this->editingId)->update([
            'name'                     => trim($this->editName),
            'description'              => trim($this->editDescription),
            'price_usd'                => (float) $this->editPriceUsd,
            'max_agents'               => $this->editMaxAgents,
            'max_widgets'              => $this->editMaxWidgets,
            'max_sessions_per_day'     => $this->editMaxSessionsPerDay,
            'max_messages_per_session' => $this->editMaxMsgPerSession,
            'is_active'                    => $this->editIsActive,
            'ai_blocked'                   => $this->editAiBlocked,
            'max_bot_messages_monthly'     => $this->editMaxBotMessages,
        ]);

        $this->editingId = null;
        $this->dispatch('close-plan-modal');
        $this->dispatch('nexova-toast', type: 'success', message: 'Plan actualizado');
    }
}
