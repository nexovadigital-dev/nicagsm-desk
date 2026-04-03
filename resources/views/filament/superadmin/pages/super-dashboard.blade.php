<x-filament-panels::page>
<style>
.hq { display:flex; flex-direction:column; gap:28px; }

/* Metric cards */
.hq-metrics { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
@media(max-width:900px) { .hq-metrics { grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px) { .hq-metrics { grid-template-columns:1fr 1fr; } }
.hq-metric {
    background:#fff; border:1px solid #e8ecf0; border-radius:10px;
    padding:18px 20px; position:relative; overflow:hidden;
}
.hq-metric-label { font-size:11px; font-weight:600; color:#6b7280; letter-spacing:.04em; text-transform:uppercase; margin-bottom:8px; }
.hq-metric-val { font-size:26px; font-weight:800; color:#0f172a; letter-spacing:-.03em; line-height:1; margin-bottom:4px; }
.hq-metric-sub { font-size:11.5px; color:#94a3b8; }
.hq-metric-accent { position:absolute; top:0; left:0; width:3px; height:100%; border-radius:10px 0 0 10px; }

/* Two-column grid */
.hq-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media(max-width:760px) { .hq-row { grid-template-columns:1fr; } }

/* Cards */
.hq-card { background:#fff; border:1px solid #e8ecf0; border-radius:10px; overflow:hidden; }
.hq-card-head { padding:14px 18px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
.hq-card-title { font-size:12.5px; font-weight:700; color:#0f172a; }
.hq-card-badge { font-size:11px; font-weight:700; color:#22c55e; }
.hq-card-body { padding:16px 18px; }

/* Bar chart */
.hq-bar-list { display:flex; flex-direction:column; gap:10px; }
.hq-bar-row { display:flex; align-items:center; gap:10px; }
.hq-bar-label { font-size:12px; font-weight:600; color:#374151; width:76px; flex-shrink:0; }
.hq-bar-track { flex:1; height:5px; background:#f1f5f9; border-radius:99px; overflow:hidden; }
.hq-bar-fill { height:100%; border-radius:99px; transition:width .5s cubic-bezier(.22,.97,.42,1); }
.hq-bar-count { font-size:12px; font-weight:700; color:#0f172a; width:22px; text-align:right; flex-shrink:0; }

/* Revenue sparkline */
.hq-spark { display:flex; align-items:flex-end; gap:4px; height:64px; }
.hq-spark-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; height:100%; justify-content:flex-end; }
.hq-spark-bar { width:100%; background:#22c55e; border-radius:3px 3px 0 0; min-height:3px; transition:height .4s; }
.hq-spark-lbl { font-size:9px; color:#94a3b8; white-space:nowrap; }

/* Quick actions */
.hq-actions { display:flex; gap:8px; flex-wrap:wrap; }
.hq-action {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 14px; border-radius:8px; font-size:12.5px; font-weight:600;
    text-decoration:none; border:1px solid transparent; transition:opacity .12s, background .12s;
    white-space:nowrap;
}
.hq-action:hover { opacity:.85; }
.hq-action-primary { background:#0f172a; color:#fff; }
.hq-action-outline  { background:#fff; border-color:#e2e8f0; color:#374151; }
.hq-action-warn     { background:#fffbeb; border-color:#fde68a; color:#92400e; }

/* Pending alert */
.hq-alert { display:flex; align-items:center; gap:10px; padding:12px 16px; background:#fffbeb; border:1px solid #fde68a; border-radius:9px; font-size:13px; color:#92400e; font-weight:600; }
.hq-alert-dot { width:8px; height:8px; border-radius:50%; background:#f59e0b; flex-shrink:0; animation:hqPulse 2s infinite; }
@keyframes hqPulse { 0%,100%{opacity:1} 50%{opacity:.45} }

/* Orgs recent list */
.hq-org-list { display:flex; flex-direction:column; }
.hq-org-row { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #f8fafc; }
.hq-org-row:last-child { border-bottom:none; }
.hq-org-avatar { width:28px; height:28px; border-radius:7px; background:#0f172a; color:#fff; font-size:10px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.hq-org-name { font-size:12.5px; font-weight:600; color:#0f172a; flex:1; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.hq-org-plan { font-size:10px; font-weight:700; padding:2px 8px; border-radius:99px; flex-shrink:0; }
.hq-org-date { font-size:10.5px; color:#94a3b8; flex-shrink:0; }
</style>

@php
$stats = $this->stats;
$planColors = [
    'free'       => ['bg'=>'#f1f5f9',          'color'=>'#64748b'],
    'trial'      => ['bg'=>'rgba(245,158,11,.1)','color'=>'#b45309'],
    'starter'    => ['bg'=>'rgba(59,130,246,.1)','color'=>'#1d4ed8'],
    'pro'        => ['bg'=>'rgba(34,197,94,.1)', 'color'=>'#15803d'],
    'partner'    => ['bg'=>'rgba(6,78,59,.25)',  'color'=>'#6ee7b7'],
    'enterprise' => ['bg'=>'rgba(99,102,241,.1)','color'=>'#4338ca'],
];
$planNames = ['free'=>'Free','trial'=>'Prueba','starter'=>'Starter','pro'=>'Pro','partner'=>'Partner','enterprise'=>'Ent.'];
$maxCount = max(1, max(array_values($stats['byPlan'] + ['_'=>0])));
$maxRev   = max(1, max(array_column($stats['revenueChart'], 'amount')));
$weekTotal = array_sum(array_column($stats['revenueChart'], 'amount'));
@endphp

<div class="hq">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <h1 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;letter-spacing:-.025em">Nexova HQ</h1>
            <p style="font-size:12.5px;color:#94a3b8;margin:3px 0 0">{{ now()->isoFormat('dddd, D [de] MMMM YYYY') }}</p>
        </div>
        <div class="hq-actions">
            @if($stats['pendingPayments'] > 0)
            <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/transactions-page" class="hq-action hq-action-warn">
                <span class="hq-alert-dot" style="animation:none;background:#f59e0b"></span>
                {{ $stats['pendingPayments'] }} pago{{ $stats['pendingPayments'] > 1 ? 's' : '' }} pendiente{{ $stats['pendingPayments'] > 1 ? 's' : '' }}
            </a>
            @endif
            <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/organizations" class="hq-action hq-action-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                Organizaciones
            </a>
        </div>
    </div>

    {{-- Metric strip --}}
    <div class="hq-metrics">
        <div class="hq-metric">
            <div class="hq-metric-accent" style="background:#22c55e"></div>
            <div class="hq-metric-label">Organizaciones</div>
            <div class="hq-metric-val">{{ $stats['totalOrgs'] }}</div>
            <div class="hq-metric-sub">{{ $stats['paidOrgs'] }} con plan pago</div>
        </div>
        <div class="hq-metric">
            <div class="hq-metric-accent" style="background:#22c55e"></div>
            <div class="hq-metric-label">Ingresos este mes</div>
            <div class="hq-metric-val" style="color:#22c55e">${{ number_format($stats['monthRevenue'], 2) }}</div>
            <div class="hq-metric-sub">${{ number_format($stats['totalRevenue'], 2) }} total acumulado</div>
        </div>
        <div class="hq-metric">
            <div class="hq-metric-accent" style="background:#3b82f6"></div>
            <div class="hq-metric-label">Conversaciones hoy</div>
            <div class="hq-metric-val" style="color:#3b82f6">{{ $stats['todayTickets'] }}</div>
            <div class="hq-metric-sub">{{ number_format($stats['totalTickets']) }} totales</div>
        </div>
        <div class="hq-metric">
            <div class="hq-metric-accent" style="background:#f59e0b"></div>
            <div class="hq-metric-label">Usuarios / Agentes</div>
            <div class="hq-metric-val">{{ $stats['totalUsers'] }}</div>
            <div class="hq-metric-sub">{{ $stats['trialOrgs'] }} en free/trial</div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="hq-row">

        {{-- Plan distribution --}}
        <div class="hq-card">
            <div class="hq-card-head">
                <span class="hq-card-title">Distribución por plan</span>
                <span style="font-size:11px;color:#94a3b8">{{ $stats['totalOrgs'] }} orgs</span>
            </div>
            <div class="hq-card-body">
                <div class="hq-bar-list">
                    @forelse($stats['byPlan'] as $plan => $count)
                    @php
                        $clr = $planColors[$plan]['color'] ?? '#22c55e';
                        $pct = round(($count / $maxCount) * 100);
                    @endphp
                    <div class="hq-bar-row">
                        <span class="hq-bar-label">{{ $planNames[$plan] ?? ucfirst($plan) }}</span>
                        <div class="hq-bar-track">
                            <div class="hq-bar-fill" style="width:{{ $pct }}%;background:{{ $clr }}"></div>
                        </div>
                        <span class="hq-bar-count">{{ $count }}</span>
                    </div>
                    @empty
                    <p style="font-size:12px;color:#94a3b8;text-align:center;margin:12px 0">Sin datos aún</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Revenue sparkline --}}
        <div class="hq-card">
            <div class="hq-card-head">
                <span class="hq-card-title">Ingresos — últimos 7 días</span>
                <span class="hq-card-badge">${{ number_format($weekTotal, 2) }}</span>
            </div>
            <div class="hq-card-body">
                @if($weekTotal > 0)
                <div class="hq-spark">
                    @foreach($stats['revenueChart'] as $day)
                    @php $pct = max(4, round(($day['amount'] / $maxRev) * 100)); @endphp
                    <div class="hq-spark-col">
                        @if($day['amount'] > 0)
                        <span style="font-size:8px;color:#6b7280;font-weight:600">${{ number_format($day['amount'],0) }}</span>
                        @endif
                        <div class="hq-spark-bar" style="height:{{ $pct }}%"></div>
                        <span class="hq-spark-lbl">{{ $day['label'] }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="height:64px;display:flex;align-items:center;justify-content:center">
                    <p style="font-size:12px;color:#94a3b8;margin:0">Sin ingresos esta semana</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="hq-actions">
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/transactions-page" class="hq-action hq-action-outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><path stroke-linecap="round" d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            Transacciones
        </a>
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/payment-config-page" class="hq-action hq-action-outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" stroke-linecap="round"/><path stroke-linecap="round" d="M1 10h22"/></svg>
            Config pagos
        </a>
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/mail-config-page" class="hq-action hq-action-outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><path stroke-linecap="round" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline stroke-linecap="round" points="22,6 12,13 2,6"/></svg>
            Servidor mail
        </a>
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/ai-config-page" class="hq-action hq-action-outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><path stroke-linecap="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            IA Global
        </a>
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/plans-manager" class="hq-action hq-action-outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><path stroke-linecap="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Planes
        </a>
    </div>

</div>
</x-filament-panels::page>
