<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\ActiveVisitor;
use App\Models\BannedIp;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Computed;

class VisitorsPage extends Page
{
    use ScopedToOrganization;

    protected string $view = 'filament.pages.visitors-page';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Visitantes';
    protected static string|\UnitEnum|null $navigationGroup = 'Conversaciones';
    protected static ?int $navigationSort = 15;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-eye';
    }

    public static function getNavigationBadge(): ?string
    {
        $orgId = auth()->user()?->organization_id;
        $q = ActiveVisitor::where('last_ping_at', '>=', now()->subSeconds(35));
        if ($orgId) $q->where('organization_id', $orgId);
        $count = $q->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // Timestamp updated on every poll so Livewire detects a state change and re-renders
    public int $pollTick = 0;

    public function notifyCount(): void
    {
        $visitors = $this->scopeToOrg(ActiveVisitor::query())
            ->where('last_ping_at', '>=', now()->subSeconds(35))
            ->pluck('id');
        $this->pollTick++;   // triggers Livewire re-render → activeVisitors computed prop re-runs
        $this->dispatch('visitor-count-updated', count: $visitors->count(), ids: $visitors->values()->all());
    }

    // ── Proactive chat modal ─────────────────────────────────────────────────
    public bool   $showProactiveModal = false;
    public string $proactiveVisitorKey = '';
    public string $proactiveVisitorName = '';
    public string $proactiveMessage   = '¡Hola! ¿En qué te puedo ayudar?';

    // ── Ban modal ────────────────────────────────────────────────────────────
    public bool   $showBanModal = false;
    public string $banIp        = '';
    public string $banReason    = '';

    // ── Data ─────────────────────────────────────────────────────────────────
    #[Computed]
    public function activeVisitors()
    {
        return $this->scopeToOrg(ActiveVisitor::query())
            ->where('last_ping_at', '>=', now()->subSeconds(35))
            ->orderByDesc('last_ping_at')
            ->get();
    }

    #[Computed]
    public function bannedIps()
    {
        return $this->scopeToOrg(BannedIp::query())
            ->orderByDesc('created_at')
            ->get();
    }

    // ── Proactive chat ───────────────────────────────────────────────────────
    public function openProactiveModal(string $visitorKey, string $visitorName): void
    {
        $this->proactiveVisitorKey  = $visitorKey;
        $this->proactiveVisitorName = $visitorName;
        $this->proactiveMessage     = '¡Hola! ¿En qué te puedo ayudar?';
        $this->showProactiveModal   = true;
    }

    public function triggerProactiveChat(): void
    {
        $orgId = $this->orgId();
        ActiveVisitor::where('organization_id', $orgId)
            ->where('visitor_key', $this->proactiveVisitorKey)
            ->update([
                'proactive_open'    => true,
                'proactive_message' => trim($this->proactiveMessage) ?: null,
            ]);

        $this->showProactiveModal = false;
        $this->dispatch('nexova-toast', type: 'success', message: 'Chat abierto en el navegador del visitante');
    }

    // ── Ban/Unban ────────────────────────────────────────────────────────────
    public function openBanModal(string $ip): void
    {
        $this->banIp     = $ip;
        $this->banReason = '';
        $this->showBanModal = true;
    }

    public function banIp(): void
    {
        if (! $this->banIp) return;
        $orgId = $this->orgId();

        BannedIp::updateOrCreate(
            ['organization_id' => $orgId, 'ip' => $this->banIp],
            ['reason' => trim($this->banReason) ?: null]
        );

        // Remove the visitor from active list
        ActiveVisitor::where('organization_id', $orgId)
            ->where('ip', $this->banIp)
            ->delete();

        $this->showBanModal = false;
        $this->dispatch('nexova-toast', type: 'success', message: "IP {$this->banIp} bloqueada");
    }

    public function unbanIp(int $id): void
    {
        BannedIp::where('id', $id)
            ->where('organization_id', $this->orgId())
            ->delete();
        $this->dispatch('nexova-toast', type: 'success', message: 'IP desbloqueada');
    }
}
