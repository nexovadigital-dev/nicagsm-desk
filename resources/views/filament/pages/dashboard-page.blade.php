<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

/* ══ Shell ══════════════════════════════════════════ */
.nx-db { padding: 28px 32px 64px; display: flex; flex-direction: column; gap: 22px; }
@media (max-width:768px) { .nx-db { padding: 20px 16px 48px; gap: 16px; } }

/* ══ Header ═════════════════════════════════════════ */
.nx-db-hd { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
.nx-db-greeting { font-size: 21px; font-weight: 700; color: #0f172a; letter-spacing: -.025em; line-height: 1.2; }
.nx-db-greeting-sub { font-size: 13px; color: #64748b; margin-top: 4px; }
.nx-db-date-block { text-align: right; flex-shrink: 0; }
.nx-db-date-main { font-size: 13px; font-weight: 600; color: #0f172a; }
.nx-db-date-sub  { font-size: 12px; color: #94a3b8; margin-top: 2px; }

/* ══ KPI grid ════════════════════════════════════════ */
.nx-kpis { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; }
@media (max-width:1200px) { .nx-kpis { grid-template-columns: repeat(2,1fr); } }
@media (max-width:600px)  { .nx-kpis { grid-template-columns: 1fr; } }

.nx-kpi {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 20px 64px 20px 20px;
    position: relative;
    transition: border-color .15s;
    overflow: hidden;
}
.nx-kpi:hover { border-color: #cbd5e1; }

/* Left accent stripe — the only color accent per card */
.nx-kpi::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 3px;
}
.nx-kpi.kpi-active::before  { background: #3b82f6; }
.nx-kpi.kpi-new::before     { background: #6366f1; }
.nx-kpi.kpi-msg::before     { background: #0ea5e9; }
.nx-kpi.kpi-csat::before    { background: #f59e0b; }

.nx-kpi__icon {
    position: absolute; top: 16px; right: 16px;
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: rgba(59,130,246,.08);
}
.nx-kpi__label {
    font-size: 11px; font-weight: 600; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .07em;
    margin-bottom: 10px; line-height: 1;
}
.nx-kpi__value {
    font-size: 32px; font-weight: 700; color: #0f172a;
    line-height: 1; letter-spacing: -.03em; margin-bottom: 10px;
}
.nx-kpi__value em { font-size: 17px; font-weight: 500; color: #94a3b8; font-style: normal; letter-spacing: 0; }
.nx-kpi__pills { display: flex; flex-wrap: wrap; gap: 5px; }
.nx-kpi__pill {
    font-size: 11px; font-weight: 600; padding: 2px 8px;
    border-radius: 99px; white-space: nowrap;
}
.nx-kpi__meta { font-size: 12px; color: #94a3b8; }
.nx-stars { display: flex; gap: 2px; margin-top: 2px; }
.nx-star { font-size: 13px; color: #f59e0b; }
.nx-star.off { color: #e2e8f0; }

/* ══ Main 2-col grid ════════════════════════════════ */
.nx-db-main { display: grid; grid-template-columns: 1fr 280px; gap: 14px; }
@media (max-width:1100px) { .nx-db-main { grid-template-columns: 1fr; } }

/* ══ Card base ══════════════════════════════════════ */
.nx-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px; overflow: hidden;
}
.nx-card__hd {
    padding: 15px 20px 13px;
    border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: space-between;
}
.nx-card__title { font-size: 13px; font-weight: 600; color: #0f172a; letter-spacing: -.01em; }
.nx-card__sub   { font-size: 11.5px; color: #94a3b8; }
.nx-card__body  { padding: 20px; }

/* ══ Bar chart ═══════════════════════════════════════ */
.nx-chart {
    display: flex; align-items: flex-end; gap: 7px;
    height: 130px;
    padding-bottom: 22px;
    position: relative;
}
.nx-chart-col {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; height: 100%; justify-content: flex-end;
    position: relative; gap: 0; cursor: default;
}
.nx-chart-val {
    font-size: 10px; font-weight: 700; color: #0f172a;
    margin-bottom: 5px; line-height: 1; opacity: 0;
    transition: opacity .15s;
}
.nx-chart-col:hover .nx-chart-val { opacity: 1; }
.nx-chart-bar-wrap { width: 100%; flex: 1; display: flex; align-items: flex-end; }
.nx-chart-bar {
    width: 100%; border-radius: 5px 5px 0 0;
    background: #3b82f6; opacity: .65;
    min-height: 4px; transition: opacity .15s;
}
.nx-chart-col:hover .nx-chart-bar { opacity: 1; }
.nx-chart-day {
    position: absolute; bottom: -18px;
    font-size: 9.5px; color: #94a3b8;
    letter-spacing: .02em; text-transform: uppercase;
    font-weight: 500; white-space: nowrap;
}

/* ══ Category bars ═══════════════════════════════════ */
.nx-catlist { display: flex; flex-direction: column; }
.nx-cat {
    display: flex; align-items: center; gap: 11px;
    padding: 10px 0; border-bottom: 1px solid #f1f5f9;
}
.nx-cat:last-child { border-bottom: none; padding-bottom: 0; }
.nx-cat__dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.nx-cat__label { font-size: 13px; font-weight: 500; color: #0f172a; flex: 1; }
.nx-cat__track { flex: 2; height: 5px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.nx-cat__fill  { height: 5px; border-radius: 99px; transition: width .4s ease; }
.nx-cat__count { font-size: 13px; font-weight: 600; color: #0f172a; min-width: 28px; text-align: right; }

/* ══ Platform icon ═══════════════════════════════════ */
.nx-plat-icon {
    width: 28px; height: 28px; border-radius: 7px;
    background: #f8fafc; border: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; flex-shrink: 0;
}

/* ══ Rating dist ═════════════════════════════════════ */
.nx-rrow { display: flex; align-items: center; gap: 10px; padding: 5px 0; }
.nx-rrow__stars { font-size: 11px; color: #f59e0b; font-weight: 600; flex-shrink: 0; width: 62px; }
.nx-rrow__track { flex: 1; height: 5px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.nx-rrow__fill  { height: 5px; background: #f59e0b; border-radius: 99px; }
.nx-rrow__n { font-size: 12px; font-weight: 600; color: #0f172a; min-width: 20px; text-align: right; }

/* ══ Satisfaction 2-col ══════════════════════════════ */
.nx-db-sat { display: grid; grid-template-columns: 1fr 280px; gap: 14px; }
@media (max-width:1100px) { .nx-db-sat { grid-template-columns: 1fr; } }

/* ══ Empty ══════════════════════════════════════════ */
.nx-empty { font-size: 12.5px; color: #94a3b8; text-align: center; padding: 18px 0; }
</style>

@php
    $s         = $this->stats;
    $user      = auth()->user();
    $org       = $user->organization;
    $isPartner = $org && $org->is_partner;
    $h         = now()->hour;
    $greet     = $h < 12 ? 'Buenos días' : ($h < 18 ? 'Buenas tardes' : 'Buenas noches');
@endphp

<div class="nx-db">

{{-- ══ Header ══════════════════════════════════════════════════ --}}
<div class="nx-db-hd">
    <div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <div class="nx-db-greeting">{{ $greet }}, {{ $user->name ?? 'Agente' }} 👋</div>
            @if($isPartner)
            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:800;background:linear-gradient(135deg,#064e3b,#065f46);color:#6ee7b7;letter-spacing:.04em;border:1px solid rgba(110,231,183,.2);white-space:nowrap">
                <svg fill="currentColor" viewBox="0 0 20 20" width="10" height="10"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                PARTNER ILIMITADO
            </span>
            @endif
        </div>
        <div class="nx-db-greeting-sub">Aquí está el resumen de actividad de hoy.</div>
    </div>
    <div class="nx-db-date-block">
        <div class="nx-db-date-main">{{ now()->isoFormat('dddd D [de] MMMM') }}</div>
        <div class="nx-db-date-sub">{{ now()->format('Y') }} · {{ now()->format('H:i') }}</div>
    </div>
</div>

{{-- ══ KPI cards ═══════════════════════════════════════════════ --}}
<div class="nx-kpis">

    {{-- Activas --}}
    <div class="nx-kpi kpi-active">
        <div class="nx-kpi__icon">
            <svg fill="none" stroke="#64748b" viewBox="0 0 24 24" width="16" height="16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        </div>
        <div class="nx-kpi__label">Conversaciones activas</div>
        <div class="nx-kpi__value">{{ $s['openTickets'] }}</div>
        <div class="nx-kpi__pills">
            @if($s['humanTickets'] > 0)
            <span class="nx-kpi__pill" style="background:#eff6ff;color:#1d4ed8">{{ $s['humanTickets'] }} agente</span>
            @endif
            @if($s['botTickets'] > 0)
            <span class="nx-kpi__pill" style="background:#eef2ff;color:#4338ca">{{ $s['botTickets'] }} bot</span>
            @endif
            @if($s['openTickets'] === 0)
            <span class="nx-kpi__meta">Sin conversaciones abiertas</span>
            @endif
        </div>
    </div>

    {{-- Nuevas hoy --}}
    <div class="nx-kpi kpi-new">
        <div class="nx-kpi__icon">
            <svg fill="none" stroke="#64748b" viewBox="0 0 24 24" width="16" height="16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v16m8-8H4"/></svg>
        </div>
        <div class="nx-kpi__label">Nuevas hoy</div>
        <div class="nx-kpi__value">{{ $s['todayTickets'] }}</div>
        <div class="nx-kpi__meta">{{ $s['weekTickets'] }} esta semana · {{ $s['totalTickets'] }} total</div>
    </div>

    {{-- Mensajes --}}
    <div class="nx-kpi kpi-msg">
        <div class="nx-kpi__icon">
            <svg fill="none" stroke="#64748b" viewBox="0 0 24 24" width="16" height="16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
        </div>
        <div class="nx-kpi__label">Mensajes hoy</div>
        <div class="nx-kpi__value">{{ $s['messagesToday'] }}</div>
        <div class="nx-kpi__meta">{{ $s['closedToday'] }} conversaciones cerradas hoy</div>
    </div>

    {{-- CSAT Survey card --}}
    <div class="nx-kpi kpi-csat">
        <div class="nx-kpi__icon">
            <svg fill="none" stroke="#64748b" viewBox="0 0 24 24" width="16" height="16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <div class="nx-kpi__label">CSAT — Satisfacción tickets</div>
        @if($s['csatAvg'])
            <div class="nx-kpi__value">{{ number_format($s['csatAvg'],1) }}<em>/5</em></div>
            <div class="nx-stars">
                @for($i=1;$i<=5;$i++)
                <span class="nx-star {{ $i <= round($s['csatAvg']) ? '' : 'off' }}">★</span>
                @endfor
            </div>
            <div class="nx-kpi__meta">{{ $s['csatResponded'] }}/{{ $s['csatTotal'] }} respondidos · {{ $s['csatRate'] }}% tasa</div>
        @else
            <div class="nx-kpi__value" style="font-size:22px">—</div>
            <div class="nx-kpi__meta">Sin encuestas respondidas aún</div>
        @endif
    </div>

</div>

{{-- ══ Main grid ════════════════════════════════════════════════ --}}
<div class="nx-db-main">

    {{-- Chart card --}}
    <div class="nx-card">
        <div class="nx-card__hd">
            <span class="nx-card__title">Conversaciones · últimos 7 días</span>
            <span class="nx-card__sub">{{ now()->subDays(6)->format('d M') }} – {{ now()->format('d M Y') }}</span>
        </div>
        <div class="nx-card__body">
            @php
                $maxC   = max(1, max(array_column($s['chartData'],'count')));
                $total7 = array_sum(array_column($s['chartData'],'count'));
            @endphp
            <div style="display:flex;align-items:baseline;gap:10px;margin-bottom:18px">
                <span style="font-size:30px;font-weight:700;color:#0f172a;letter-spacing:-.03em;line-height:1">{{ $total7 }}</span>
                <span style="font-size:12.5px;color:#94a3b8">conversaciones en 7 días</span>
            </div>
            <div class="nx-chart">
                @foreach($s['chartData'] as $day)
                @php $barH = max(3, round(($day['count']/$maxC)*100)); @endphp
                <div class="nx-chart-col" title="{{ $day['count'] }} conversaciones">
                    <div class="nx-chart-val">{{ $day['count'] ?: '' }}</div>
                    <div class="nx-chart-bar-wrap">
                        <div class="nx-chart-bar" style="height:{{ $barH }}%"></div>
                    </div>
                    <div class="nx-chart-day">{{ $day['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Side column --}}
    <div style="display:flex;flex-direction:column;gap:14px">

        {{-- Status --}}
        <div class="nx-card">
            <div class="nx-card__hd">
                <span class="nx-card__title">Estado</span>
                <span class="nx-card__sub">{{ $s['openTickets'] + $s['closedTickets'] }} total</span>
            </div>
            <div class="nx-card__body" style="padding-block:12px">
                @php $tot = max(1, $s['openTickets'] + $s['closedTickets']); @endphp
                <div class="nx-catlist">
                    <div class="nx-cat">
                        <span class="nx-cat__dot" style="background:#6366f1"></span>
                        <span class="nx-cat__label">Bot</span>
                        <div class="nx-cat__track"><div class="nx-cat__fill" style="width:{{ round(($s['botTickets']/$tot)*100) }}%;background:#6366f1"></div></div>
                        <span class="nx-cat__count">{{ $s['botTickets'] }}</span>
                    </div>
                    <div class="nx-cat">
                        <span class="nx-cat__dot" style="background:#3b82f6"></span>
                        <span class="nx-cat__label">Agente</span>
                        <div class="nx-cat__track"><div class="nx-cat__fill" style="width:{{ round(($s['humanTickets']/$tot)*100) }}%;background:#3b82f6"></div></div>
                        <span class="nx-cat__count">{{ $s['humanTickets'] }}</span>
                    </div>
                    <div class="nx-cat">
                        <span class="nx-cat__dot" style="background:#64748b"></span>
                        <span class="nx-cat__label">Cerradas</span>
                        <div class="nx-cat__track"><div class="nx-cat__fill" style="width:{{ round(($s['closedTickets']/$tot)*100) }}%;background:#64748b"></div></div>
                        <span class="nx-cat__count">{{ $s['closedTickets'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Channels --}}
        <div class="nx-card">
            <div class="nx-card__hd">
                <span class="nx-card__title">Canales</span>
            </div>
            <div class="nx-card__body" style="padding-block:10px">
                @php
                    $platMeta = [
                        'web'      => ['🌐','Web'],
                        'telegram' => ['✈️','Telegram'],
                        'whatsapp' => ['💬','WhatsApp'],
                        'internal' => ['🔒','Interno'],
                    ];
                    $anyPlat = false;
                @endphp
                <div class="nx-catlist">
                    @foreach($platMeta as $plat => [$icon, $label])
                    @if(!empty($s['byPlatform'][$plat]) && $s['byPlatform'][$plat] > 0)
                    @php $anyPlat = true; @endphp
                    <div class="nx-cat">
                        <div class="nx-plat-icon">{{ $icon }}</div>
                        <span class="nx-cat__label">{{ $label }}</span>
                        <span class="nx-cat__count">{{ $s['byPlatform'][$plat] }}</span>
                    </div>
                    @endif
                    @endforeach
                    @if(!$anyPlat)
                    <div class="nx-empty">Sin actividad aún</div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ══ Satisfaction ════════════════════════════════════════════ --}}
@if($s['totalRated'] > 0)
<div class="nx-db-sat">

    {{-- Timeline chart --}}
    <div class="nx-card">
        <div class="nx-card__hd">
            <span class="nx-card__title">Satisfacción · últimos 7 días</span>
            <span class="nx-card__sub">Promedio 1–5 ★</span>
        </div>
        <div class="nx-card__body">
            <div class="nx-chart">
                @foreach($s['ratingChart'] as $day)
                @php $rh = $day['avg'] ? max(3, round(($day['avg']/5)*100)) : 3; @endphp
                <div class="nx-chart-col" title="{{ $day['avg'] ? $day['count'].' cal · '.$day['avg'].'★' : 'Sin datos' }}">
                    <div class="nx-chart-val">{{ $day['avg'] ?? '' }}</div>
                    <div class="nx-chart-bar-wrap">
                        <div class="nx-chart-bar"
                             style="height:{{ $rh }}%;background:{{ $day['avg'] ? '#f59e0b' : '#e2e8f0' }};opacity:{{ $day['avg'] ? '.7' : '.3' }}"></div>
                    </div>
                    <div class="nx-chart-day">{{ $day['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Distribution --}}
    <div class="nx-card">
        <div class="nx-card__hd">
            <span class="nx-card__title">Distribución</span>
            <span class="nx-card__sub">{{ $s['totalRated'] }} val.</span>
        </div>
        <div class="nx-card__body">
            @for($star=5;$star>=1;$star--)
            @php $cnt=$s['ratingDist'][$star]??0; $pct=$s['totalRated']>0?round(($cnt/$s['totalRated'])*100):0; @endphp
            <div class="nx-rrow">
                <div class="nx-rrow__stars">{{ str_repeat('★',$star) }}<span style="opacity:.2">{{ str_repeat('★',5-$star) }}</span></div>
                <div class="nx-rrow__track"><div class="nx-rrow__fill" style="width:{{ $pct }}%"></div></div>
                <div class="nx-rrow__n">{{ $cnt }}</div>
            </div>
            @endfor
        </div>
    </div>

</div>
@endif

</div>
</x-filament-panels::page>
