<x-filament-panels::page>
<style>
.sa-wrap    { display:flex; flex-direction:column; gap:24px; }
.sa-card    { background:var(--c-surface,#fff); border:1px solid var(--c-border,#e3e6ea); border-radius:12px; overflow:hidden; }
.sa-tbl     { width:100%; border-collapse:collapse; font-size:13px; }
.sa-tbl th  { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--c-sub,#6b7280); border-bottom:1px solid var(--c-border,#e3e6ea); white-space:nowrap; }
.sa-tbl td  { padding:12px 16px; border-bottom:1px solid var(--c-border,#e3e6ea); color:var(--c-text,#111827); vertical-align:middle; }
.sa-tbl tr:last-child td { border-bottom:none; }
.sa-tbl tr:hover td { background:var(--c-surf2,#f9fafb); }
.sa-badge   { display:inline-block; padding:2px 9px; border-radius:99px; font-size:11px; font-weight:700; white-space:nowrap; }
.sa-btn     { display:inline-flex; align-items:center; gap:4px; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; border:none; transition:opacity .15s; white-space:nowrap; }
.sa-btn:hover { opacity:.85; }
.sa-input   { padding:8px 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; font-size:13px; background:var(--c-surface,#fff); color:var(--c-text,#111827); outline:none; }
.sa-input:focus { border-color:#22c55e; }
.sa-select  { padding:8px 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; font-size:13px; background:var(--c-surface,#fff); color:var(--c-text,#111827); outline:none; cursor:pointer; }
.mono       { font-family:monospace; font-size:11px; }
</style>

@php
$statusColors = [
    'pending'   => ['bg'=>'#fef9c3','color'=>'#854d0e','label'=>'Pendiente'],
    'confirmed' => ['bg'=>'#dcfce7','color'=>'#15803d','label'=>'Confirmado'],
    'failed'    => ['bg'=>'#fee2e2','color'=>'#b91c1c','label'=>'Fallido'],
    'expired'   => ['bg'=>'#f3f4f6','color'=>'#6b7280','label'=>'Expirado'],
];
$methodIcons = [
    'usdt_trc20'=>'USDT·TRC20','usdt_bep20'=>'USDT·BEP20','usdt_polygon'=>'USDT·POL',
    'usdc_trc20'=>'USDC·TRC20','usdc_bep20'=>'USDC·BEP20','usdc_polygon'=>'USDC·POL',
    'mercadopago'=>'MercadoPago',
];
@endphp

<div class="sa-wrap">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-size:20px;font-weight:800;color:var(--c-text,#111827);margin:0">Transacciones</h1>
            <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:4px 0 0">Historial de pagos y confirmaciones pendientes</p>
        </div>
        @php
            $awaitingReview = $this->transactions->where('status','pending')->whereNotNull('tx_hash')->count();
            $waitingHash    = $this->transactions->where('status','pending')->whereNull('tx_hash')->count();
        @endphp
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            @if($awaitingReview > 0)
            <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:700;color:#92400e">
                ⚠ {{ $awaitingReview }} por verificar en blockchain
            </div>
            @endif
            @if($waitingHash > 0)
            <div style="background:#f3f4f6;border:1px solid var(--c-border,#e3e6ea);border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;color:var(--c-sub,#6b7280)">
                {{ $waitingHash }} esperando TX hash
            </div>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    @php
        $stats      = $this->stats;
        $methodLabels = [
            'usdt_trc20'=>'USDT·TRC20','usdt_bep20'=>'USDT·BEP20','usdt_polygon'=>'USDT·POL',
            'usdc_trc20'=>'USDC·TRC20','usdc_bep20'=>'USDC·BEP20','usdc_polygon'=>'USDC·POL',
            'mercadopago'=>'MercadoPago',
        ];
        $maxMethod  = max(1, max(array_column($stats['byMethod'], 'total') ?: [0]));
        $maxMonth   = max(1, max(array_column($stats['byMonth'],  'total') ?: [0]));
        $monthNames = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun',
                       '07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'];
    @endphp
    @if(!empty($stats['byMethod']))
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        {{-- Por método --}}
        <div class="sa-card">
            <div style="padding:12px 18px;border-bottom:1px solid var(--c-border,#e3e6ea);font-size:13px;font-weight:700;color:var(--c-text,#111827)">
                Ingresos por método (total)
            </div>
            <div style="padding:16px 18px;display:flex;flex-direction:column;gap:9px">
                @foreach($stats['byMethod'] as $row)
                @php $pct = round(($row['total'] / $maxMethod) * 100); @endphp
                <div style="display:flex;align-items:center;gap:10px;font-size:12px">
                    <span style="width:90px;flex-shrink:0;font-weight:600;color:#374151">{{ $methodLabels[$row['method']] ?? $row['method'] }}</span>
                    <div style="flex:1;height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden">
                        <div style="width:{{ $pct }}%;height:100%;background:#22c55e;border-radius:99px"></div>
                    </div>
                    <span style="width:56px;text-align:right;font-weight:800;color:#111827">${{ number_format($row['total'],0) }}</span>
                    <span style="font-size:10px;color:#9ca3af">×{{ $row['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        {{-- Por mes --}}
        <div class="sa-card">
            <div style="padding:12px 18px;border-bottom:1px solid var(--c-border,#e3e6ea);font-size:13px;font-weight:700;color:var(--c-text,#111827)">
                Ingresos por mes (últimos 6 meses)
            </div>
            <div style="padding:16px 18px">
                @if(!empty($stats['byMonth']))
                <div style="display:flex;align-items:flex-end;gap:8px;height:72px">
                    @foreach($stats['byMonth'] as $row)
                    @php
                        $pct = round(($row['total'] / $maxMonth) * 100);
                        [$yr, $mo] = explode('-', $row['month']);
                        $label = ($monthNames[$mo] ?? $mo) . ' ' . substr($yr, 2);
                    @endphp
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;height:100%;justify-content:flex-end">
                        <span style="font-size:8px;color:#6b7280;font-weight:600">${{ number_format($row['total'],0) }}</span>
                        <div style="width:100%;height:{{ max(4,$pct) }}%;background:#22c55e;border-radius:4px 4px 0 0;min-height:4px"></div>
                        <span style="font-size:9px;color:#9ca3af;white-space:nowrap">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p style="font-size:12px;color:#9ca3af;text-align:center;margin:16px 0">Sin datos este período</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <input wire:model.live.debounce.300ms="search"
               class="sa-input" style="width:260px"
               placeholder="Buscar org, TX hash, ID pago…">
        <select wire:model.live="filterStatus" class="sa-select">
            <option value="all">Todos los estados</option>
            <option value="pending">Pendientes</option>
            <option value="confirmed">Confirmados</option>
            <option value="failed">Fallidos</option>
            <option value="expired">Expirados</option>
        </select>
        <span style="font-size:12px;color:var(--c-sub,#6b7280);margin-left:auto">
            {{ $this->transactions->total() }} transacción(es)
        </span>
    </div>

    {{-- Table --}}
    <div class="sa-card" style="overflow-x:auto">
        <table class="sa-tbl">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Organización</th>
                    <th>Plan</th>
                    <th>Método</th>
                    <th>Monto</th>
                    <th>TX / Referencia</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->transactions as $tx)
                @php
                    $sc = $statusColors[$tx->status] ?? $statusColors['expired'];
                    $methodLabel = $methodIcons[$tx->method] ?? $tx->method;
                    $isCrypto = !in_array($tx->method, ['mercadopago']);
                @endphp
                <tr>
                    <td style="white-space:nowrap;font-size:12px;color:var(--c-sub,#6b7280)">
                        {{ $tx->created_at->format('d/m/Y') }}<br>
                        <span style="font-size:11px">{{ $tx->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <div style="font-weight:700">{{ $tx->organization?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">{{ $tx->organization?->slug }}</div>
                    </td>
                    <td>
                        <span style="font-weight:600">{{ $tx->plan?->name ?? '—' }}</span>
                    </td>
                    <td>
                        <span class="sa-badge" style="background:#f3f4f6;color:#374151;font-size:10px">{{ $methodLabel }}</span>
                    </td>
                    <td style="white-space:nowrap">
                        <div style="font-weight:800">${{ number_format($tx->amount_usd, 2) }}</div>
                        @if($isCrypto && $tx->amount_crypto)
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">
                            {{ number_format($tx->amount_crypto, 4) }} {{ $tx->currency }}
                        </div>
                        @endif
                        @if(!$isCrypto && $tx->amount_local)
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">
                            {{ number_format($tx->amount_local, 0) }} COP
                        </div>
                        @endif
                    </td>
                    <td style="max-width:200px">
                        @if($tx->tx_hash)
                            <div class="mono" style="word-break:break-all">{{ Str::limit($tx->tx_hash, 18) }}</div>
                            @php $explorerUrl = $tx->explorerUrl(); @endphp
                            @if($explorerUrl)
                            <a href="{{ $explorerUrl }}" target="_blank"
                               style="font-size:11px;color:#22c55e;text-decoration:none">Ver en explorer ↗</a>
                            @endif
                        @elseif($tx->mp_payment_id)
                            <div class="mono">MP: {{ $tx->mp_payment_id }}</div>
                            @if($tx->mp_payment_status)
                            <div style="font-size:11px;color:var(--c-sub,#6b7280)">{{ $tx->mp_payment_status }}</div>
                            @endif
                        @else
                            <span style="color:var(--c-sub,#6b7280)">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="sa-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
                            {{ $sc['label'] }}
                        </span>
                        @if($tx->confirmed_at)
                        <div style="font-size:10px;color:var(--c-sub,#6b7280);margin-top:3px">
                            {{ $tx->confirmed_at->format('d/m H:i') }}
                        </div>
                        @endif
                    </td>
                    <td>
                        @if($tx->status === 'pending')
                        <div style="display:flex;flex-direction:column;gap:5px">
                            @if($tx->tx_hash)
                            <div style="font-size:10px;font-weight:700;color:#92400e;background:#fef3c7;border-radius:4px;padding:2px 6px;margin-bottom:2px;text-align:center">
                                TX recibido · verificar
                            </div>
                            @endif
                            <button wire:click="confirmTransaction({{ $tx->id }})"
                                    wire:confirm="¿Confirmar esta transacción y activar el plan?"
                                    class="sa-btn" style="background:#22c55e;color:#fff">
                                ✓ Confirmar
                            </button>
                            <button wire:click="rejectTransaction({{ $tx->id }})"
                                    wire:confirm="¿Marcar esta transacción como fallida?"
                                    class="sa-btn" style="background:#fee2e2;color:#b91c1c">
                                ✕ Rechazar
                            </button>
                        </div>
                        @else
                            <span style="font-size:12px;color:var(--c-sub,#6b7280)">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--c-sub,#6b7280);padding:40px">
                        No hay transacciones registradas aún
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>{{ $this->transactions->links() }}</div>

</div>
</x-filament-panels::page>
