<x-filament-panels::page>
<style>
.sa-wrap    { display:flex; flex-direction:column; gap:24px; }
.sa-strip   { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; }
.sa-stat    { background:var(--c-surface,#fff); border:1px solid var(--c-border,#e3e6ea); border-radius:10px; padding:16px 18px; }
.sa-stat-v  { font-size:26px; font-weight:800; color:var(--c-text,#111827); line-height:1; margin-bottom:4px; }
.sa-stat-l  { font-size:11px; font-weight:600; color:var(--c-sub,#6b7280); text-transform:uppercase; letter-spacing:.05em; }
.sa-stat-accent { border-left:3px solid #22c55e; }
.sa-card    { background:var(--c-surface,#fff); border:1px solid var(--c-border,#e3e6ea); border-radius:12px; overflow:hidden; }
.sa-card-head { padding:16px 20px; border-bottom:1px solid var(--c-border,#e3e6ea); font-size:13px; font-weight:700; color:var(--c-text,#111827); }
.sa-card-body { padding:20px; }
.sa-plan-row  { display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--c-border,#e3e6ea); font-size:13px; }
.sa-plan-row:last-child { border-bottom:none; }
.sa-plan-badge { display:inline-block; padding:2px 10px; border-radius:99px; font-size:11px; font-weight:700; }
.sa-bar-wrap  { display:flex; flex-direction:column; gap:6px; }
.sa-bar-row   { display:flex; align-items:center; gap:8px; font-size:11px; color:var(--c-sub,#6b7280); }
.sa-bar       { flex:1; height:5px; background:var(--c-surf2,#f0f2f5); border-radius:99px; overflow:hidden; }
.sa-bar-fill  { height:100%; background:#22c55e; border-radius:99px; }
</style>

@php $stats = $this->stats; @endphp

<div class="sa-wrap">

    {{-- Título --}}
    <div>
        <h1 style="font-size:20px;font-weight:800;color:var(--c-text,#111827);margin:0">Panel Nexova</h1>
        <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:4px 0 0">Vista general de todas las organizaciones y pagos</p>
    </div>

    {{-- Stats strip --}}
    <div class="sa-strip">
        <div class="sa-stat sa-stat-accent">
            <div class="sa-stat-v">{{ $stats['totalOrgs'] }}</div>
            <div class="sa-stat-l">Organizaciones</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v" style="color:#22c55e">{{ $stats['paidOrgs'] }}</div>
            <div class="sa-stat-l">Con plan pago</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v" style="color:#f59e0b">{{ $stats['trialOrgs'] }}</div>
            <div class="sa-stat-l">En prueba</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v">{{ $stats['totalUsers'] }}</div>
            <div class="sa-stat-l">Usuarios totales</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v">{{ $stats['totalTickets'] }}</div>
            <div class="sa-stat-l">Conversaciones</div>
        </div>
        <div class="sa-stat sa-stat-accent">
            <div class="sa-stat-v">${{ number_format($stats['monthRevenue'], 2) }}</div>
            <div class="sa-stat-l">Ingresos este mes</div>
        </div>
        <div class="sa-stat">
            <div class="sa-stat-v">${{ number_format($stats['totalRevenue'], 2) }}</div>
            <div class="sa-stat-l">Ingresos totales</div>
        </div>
        @if($stats['pendingPayments'] > 0)
        <div class="sa-stat" style="border-left:3px solid #f59e0b">
            <div class="sa-stat-v" style="color:#f59e0b">{{ $stats['pendingPayments'] }}</div>
            <div class="sa-stat-l">Pagos pendientes</div>
        </div>
        @endif
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        {{-- Distribución por plan --}}
        <div class="sa-card">
            <div class="sa-card-head">Distribución por plan</div>
            <div class="sa-card-body">
                @php
                $planColors = ['trial'=>'#6b7280','starter'=>'#3b82f6','pro'=>'#8b5cf6','enterprise'=>'#22c55e'];
                $planNames  = ['trial'=>'Prueba','starter'=>'Starter','pro'=>'Pro','enterprise'=>'Enterprise'];
                $maxCount   = max(1, max(array_values($stats['byPlan'] + ['x'=>0])));
                @endphp
                <div class="sa-bar-wrap">
                    @foreach($stats['byPlan'] as $plan => $count)
                    <div class="sa-bar-row">
                        <span style="width:70px;flex-shrink:0;color:var(--c-text,#111827);font-weight:600">{{ $planNames[$plan] ?? $plan }}</span>
                        <div class="sa-bar">
                            <div class="sa-bar-fill" style="width:{{ round(($count/$maxCount)*100) }}%;background:{{ $planColors[$plan] ?? '#22c55e' }}"></div>
                        </div>
                        <span style="width:28px;text-align:right;font-weight:700;color:var(--c-text,#111827)">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Revenue últimos 7 días --}}
        <div class="sa-card">
            <div class="sa-card-head">Ingresos — últimos 7 días (USD)</div>
            <div class="sa-card-body">
                @php $maxRev = max(1, max(array_column($stats['revenueChart'], 'amount'))); @endphp
                <div style="display:flex;align-items:flex-end;gap:6px;height:80px">
                    @foreach($stats['revenueChart'] as $day)
                    @php $pct = round(($day['amount'] / $maxRev) * 100); @endphp
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;height:100%">
                        <div style="flex:1;display:flex;align-items:flex-end;width:100%">
                            <div style="width:100%;height:{{ max(3, $pct) }}%;background:#22c55e;border-radius:3px 3px 0 0;min-height:3px"></div>
                        </div>
                        <span style="font-size:9px;color:var(--c-sub,#6b7280)">{{ $day['label'] }}</span>
                    </div>
                    @endforeach
                </div>
                @if(array_sum(array_column($stats['revenueChart'], 'amount')) == 0)
                <p style="text-align:center;font-size:12px;color:var(--c-sub,#6b7280);margin:8px 0 0">Sin ingresos registrados aún</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/organizations"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#22c55e;color:#fff;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">
            Ver organizaciones
        </a>
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/payment-config-page"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border:1px solid var(--c-border,#e3e6ea);background:transparent;color:var(--c-text,#111827);border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
            Configurar pagos
        </a>
        @if($stats['pendingPayments'] > 0)
        <a href="{{ filament()->getPanel('superadmin')->getUrl() }}/transactions-page"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border:1px solid #fed7aa;background:#fff7ed;color:#92400e;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">
            {{ $stats['pendingPayments'] }} pago(s) pendiente(s) de confirmar
        </a>
        @endif
    </div>

</div>
</x-filament-panels::page>
