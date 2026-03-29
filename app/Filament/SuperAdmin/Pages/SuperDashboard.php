<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Organization;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class SuperDashboard extends Page
{
    protected string $view = 'filament.superadmin.pages.super-dashboard';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Dashboard';
    protected static string|\UnitEnum|null $navigationGroup = 'Visión General';
    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-chart-bar-square';
    }

    public function getTitle(): string|Htmlable { return ''; }

    public function getStatsProperty(): array
    {
        $today = now()->startOfDay();

        $totalOrgs     = Organization::count();
        $activeOrgs    = Organization::where('is_active', true)->count();
        $trialOrgs     = Organization::where('plan', 'trial')->count();
        $paidOrgs      = Organization::whereNotIn('plan', ['trial'])->where('is_active', true)->count();
        $totalUsers    = User::where('is_super_admin', false)->count();
        $totalTickets  = Ticket::count();
        $todayTickets  = Ticket::where('created_at', '>=', $today)->count();

        $totalRevenue  = PaymentTransaction::where('status', 'confirmed')->sum('amount_usd');
        $monthRevenue  = PaymentTransaction::where('status', 'confirmed')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount_usd');

        $pendingPayments = PaymentTransaction::where('status', 'pending')->count();

        // Orgs por plan
        $byPlan = Organization::selectRaw('plan, count(*) as count')
            ->groupBy('plan')
            ->pluck('count', 'plan')
            ->toArray();

        // Revenue últimos 7 días
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueChart[] = [
                'label' => $date->format('D'),
                'amount' => (float) PaymentTransaction::where('status', 'confirmed')
                    ->whereDate('confirmed_at', $date->toDateString())
                    ->sum('amount_usd'),
            ];
        }

        return compact(
            'totalOrgs', 'activeOrgs', 'trialOrgs', 'paidOrgs',
            'totalUsers', 'totalTickets', 'todayTickets',
            'totalRevenue', 'monthRevenue', 'pendingPayments',
            'byPlan', 'revenueChart'
        );
    }
}
