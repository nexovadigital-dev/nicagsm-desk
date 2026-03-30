<x-filament-panels::page>
<style>
.sa-wrap     { display:flex; flex-direction:column; gap:24px; }
.sa-grid4    { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; }
.sa-grid2    { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.sa-stat     { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 18px; }
.sa-stat-v   { font-size:28px; font-weight:900; color:#111827; line-height:1; margin-bottom:4px; letter-spacing:-.02em; }
.sa-stat-l   { font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; }
.sa-card     { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
.sa-card-head{ padding:14px 18px; border-bottom:1px solid #e2e8f0; font-size:13px; font-weight:700; color:#111827; display:flex; align-items:center; justify-content:space-between; }
.sa-card-body{ padding:18px; }
.sa-bar-wrap { display:flex; flex-direction:column; gap:10px; }
.sa-bar-row  { display:flex; align-items:center; gap:10px; font-size:12px; }
.sa-bar-track{ flex:1; height:6px; background:#f1f5f9; border-radius:99px; overflow:hidden; }
.sa-bar-fill { height:100%; border-radius:99px; transition:width .4s; }
.sa-action   { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none; transition:opacity .15s; }
.sa-action:hover { opacity:.85; }
@media(max-width:640px){ .sa-grid2{ grid-template-columns:1fr; } }
</style>

@php
$stats = $this->stats;
$planColors = [
    'free'=>'#94a3b8','trial'=>'#6b7280','starter'=>'#3b82f6',
    'pro'=>'#22c55e','enterprise'=>'#8b5cf6',
];
$planNames = [
    'free'=>'Free','trial'=>'Prueba','starter'=>'Starter',
    'pro'=>'Pro','enterprise'=>'Enterprise',
];
$maxCount = max(1, max(array_values($stats['byPlan'] + ['_'=>0])));
$maxRev   = max(1, max(array_column($stats['revenueChart'], 'amount')));
@endphp

<div class="sa-wrap">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">Panel Nexova HQ</h1>
            <p style="font-size:13px;color:#6b7280;margin:4px 0 0">Vista general · {{ now()->format('d/m/Y') }}</p>
        </div>
        @if($stats['pendingPayments'] > 0)
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/transactions-page"
           style="background:#fff7ed;border:1px solid #fed7aa;color:#92400e;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block"></span>
            {{ $stats['pendingPayments'] }} pago(s) por verificar
        </a>
        @endif
    </div>

    {{-- Stats strip --}}
    <div class="sa-grid4">
        <div class="sa-stat" style="border-left:3px solid #22c55e">
            <div class="sa-stat-v">{{ $stats['totalOrgs'] }}</div>
            <div class="sa-stat-l">Organizaciones</div>
        </div>
        <div class="sa-stat" style="border-left:3px solid #22c55e">
            <div class="sa-stat-v" style="color:#22c55e">{{ $stats['paidOrgs'] }}</div>
            <div class="sa-stat-l">Con plan pago</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v" style="color:#6b7280">{{ $stats['trialOrgs'] }}</div>
            <div class="sa-stat-l">Free / Trial</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v">{{ $stats['totalUsers'] }}</div>
            <div class="sa-stat-l">Agentes / usuarios</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v">{{ number_format($stats['totalTickets']) }}</div>
            <div class="sa-stat-l">Conversaciones totales</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v" style="color:#3b82f6">{{ $stats['todayTickets'] }}</div>
            <div class="sa-stat-l">Hoy</div>
        </div>
        <div class="sa-stat" style="border-left:3px solid #22c55e">
            <div class="sa-stat-v" style="color:#22c55e">${{ number_format($stats['monthRevenue'], 2) }}</div>
            <div class="sa-stat-l">Ingresos este mes</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v">${{ number_format($stats['totalRevenue'], 2) }}</div>
            <div class="sa-stat-l">Ingresos totales</div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="sa-grid2">

        {{-- Distribución por plan --}}
        <div class="sa-card">
            <div class="sa-card-head">
                <span>Distribución por plan</span>
                <span style="font-size:12px;font-weight:500;color:#6b7280">{{ $stats['totalOrgs'] }} total</span>
            </div>
            <div class="sa-card-body">
                <div class="sa-bar-wrap">
                    @forelse($stats['byPlan'] as $plan => $count)
                    @php $clr = $planColors[$plan] ?? '#22c55e'; @endphp
                    <div class="sa-bar-row">
                        <span style="width:72px;flex-shrink:0;color:#374151;font-weight:600;font-size:12px">
                            {{ $planNames[$plan] ?? ucfirst($plan) }}
                        </span>
                        <div class="sa-bar-track">
                            <div class="sa-bar-fill" style="width:{{ round(($count/$maxCount)*100) }}%;background:{{ $clr }}"></div>
                        </div>
                        <span style="width:24px;text-align:right;font-weight:800;color:#111827;font-size:12px">{{ $count }}</span>
                    </div>
                    @empty
                    <p style="font-size:12px;color:#6b7280;text-align:center;margin:0">Sin datos</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Revenue 7 días --}}
        <div class="sa-card">
            <div class="sa-card-head">
                <span>Ingresos — últimos 7 días</span>
                @php $weekTotal = array_sum(array_column($stats['revenueChart'], 'amount')); @endphp
                <span style="font-size:12px;font-weight:700;color:#22c55e">${{ number_format($weekTotal, 2) }}</span>
            </div>
            <div class="sa-card-body">
                @if($weekTotal > 0)
                <div style="display:flex;align-items:flex-end;gap:5px;height:72px">
                    @foreach($stats['revenueChart'] as $day)
                    @php $pct = round(($day['amount'] / $maxRev) * 100); @endphp
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%;justify-content:flex-end">
                        @if($day['amount'] > 0)
                        <span style="font-size:8px;color:#6b7280;font-weight:600">${{ number_format($day['amount'],0) }}</span>
                        @endif
                        <div style="width:100%;height:{{ max(4, $pct) }}%;background:#22c55e;border-radius:4px 4px 0 0;min-height:4px"></div>
                        <span style="font-size:9px;color:#9ca3af">{{ $day['label'] }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="height:72px;display:flex;align-items:center;justify-content:center">
                    <p style="font-size:12px;color:#9ca3af;margin:0">Sin ingresos registrados esta semana</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/organizations"
           class="sa-action" style="background:#22c55e;color:#fff">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            Organizaciones
        </a>
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/transactions-page"
           class="sa-action" style="background:#fff;border:1px solid #e2e8f0;color:#374151">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2" stroke-linecap="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            Transacciones
        </a>
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/payment-config-page"
           class="sa-action" style="background:#fff;border:1px solid #e2e8f0;color:#374151">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2" stroke-linecap="round"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>
            Config pagos
        </a>
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/mail-config-page"
           class="sa-action" style="background:#fff;border:1px solid #e2e8f0;color:#374151">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Servidor mail
        </a>
    </div>

</div>
</x-filament-panels::page>
