<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\ChatWidget;
use App\Models\Message;
use App\Models\Ticket;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class DashboardPage extends Page
{
    use ScopedToOrganization;

    protected string $view = 'filament.pages.dashboard-page';

    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Dashboard';
    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-chart-bar';
    }

    public function getTitle(): string|Htmlable { return ''; }

    /**
     * Widgets de la org que tienen el bot desactivado — para mostrar alerta en el dashboard.
     */
    public function getBotsDisabledProperty(): \Illuminate\Support\Collection
    {
        $orgId = auth()->user()?->organization_id;
        if (! $orgId) return collect();

        return ChatWidget::where('organization_id', $orgId)
            ->where('is_active', true)
            ->where('bot_enabled', false)
            ->pluck('name');
    }

    private function orgTickets(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->scopeToOrg(Ticket::query());
    }

    public function getRecentTicketsProperty()
    {
        return $this->orgTickets()
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->where('is_support_ticket', false)
            ->whereIn('status', ['bot', 'human'])
            ->latest('updated_at')
            ->limit(6)
            ->get();
    }

    public function getStatsProperty(): array
    {
        $today    = now()->startOfDay();
        $thisWeek = now()->startOfWeek();

        $base = fn () => $this->orgTickets();

        $totalTickets  = $base()->count();
        $todayTickets  = $base()->where('created_at', '>=', $today)->count();
        $weekTickets   = $base()->where('created_at', '>=', $thisWeek)->count();
        $openTickets   = $base()->whereIn('status', ['bot','human'])->count();
        $humanTickets  = $base()->where('status', 'human')->count();
        $botTickets    = $base()->where('status', 'bot')->count();
        $closedTickets = $base()->where('status', 'closed')->count();
        $closedToday   = $base()->where('status', 'closed')->where('updated_at', '>=', $today)->count();

        // Resolution rate
        $totalClosed = $closedTickets;
        $botResolved = $base()->where('status', 'closed')
            ->whereDoesntHave('messages', fn ($q) => $q->where('sender_type', 'agent'))->count();
        $botRate = $totalClosed > 0 ? round(($botResolved / $totalClosed) * 100) : 0;

        // Average rating (legacy widget rating)
        $avgRating = $base()->whereNotNull('rating')->avg('rating');

        // CSAT — survey ratings from support tickets
        $csatBase      = fn () => $base()->where('is_support_ticket', true);
        $csatTotal     = $csatBase()->where('status', 'closed')->count();
        $csatResponded = $csatBase()->whereNotNull('survey_rating')->count();
        $csatAvg       = $csatBase()->whereNotNull('survey_rating')->avg('survey_rating');
        $csatRate      = $csatTotal > 0 ? round(($csatResponded / $csatTotal) * 100) : 0;

        // Messages today (scoped to org tickets)
        $orgTicketIds  = $base()->pluck('id');
        $messagesToday = Message::whereIn('ticket_id', $orgTicketIds)
            ->where('created_at', '>=', $today)->count();

        // Platform breakdown
        $byPlatform = $base()->selectRaw('platform, count(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform')
            ->toArray();

        // Last 7 days chart data
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData[] = [
                'label' => $date->format('D'),
                'count' => $base()->whereDate('created_at', $date->toDateString())->count(),
            ];
        }

        // Last 7 days satisfaction chart
        $ratingChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $rated = $base()->whereNotNull('rating')->whereDate('updated_at', $date->toDateString());
            $ratingChart[] = [
                'label' => $date->format('D'),
                'avg'   => $rated->count() ? round((float) $rated->avg('rating'), 1) : null,
                'count' => $rated->count(),
            ];
        }

        // Rating distribution (1-5 stars)
        $ratingDist = [];
        for ($star = 5; $star >= 1; $star--) {
            $ratingDist[$star] = $base()->where('rating', $star)->count();
        }
        $totalRated = array_sum($ratingDist);

        return compact(
            'totalTickets','todayTickets','weekTickets',
            'openTickets','humanTickets','botTickets','closedTickets','closedToday',
            'botRate','avgRating','messagesToday','byPlatform','chartData',
            'ratingChart','ratingDist','totalRated',
            'csatAvg','csatRate','csatResponded','csatTotal'
        );
    }
}
