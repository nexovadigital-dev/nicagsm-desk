<x-filament-panels::page>
<style>
.sa-wrap  { display:flex; flex-direction:column; gap:24px; }
.sa-card  { background:var(--c-surface,#fff); border:1px solid var(--c-border,#e3e6ea); border-radius:12px; overflow:hidden; }
.sa-tbl   { width:100%; border-collapse:collapse; font-size:13px; }
.sa-tbl th { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--c-sub,#6b7280); border-bottom:1px solid var(--c-border,#e3e6ea); }
.sa-tbl td { padding:12px 16px; border-bottom:1px solid var(--c-border,#e3e6ea); color:var(--c-text,#111827); vertical-align:middle; }
.sa-tbl tr:last-child td { border-bottom:none; }
.sa-tbl tr:hover td { background:var(--c-surf2,#f9fafb); }
.sa-badge { display:inline-block; padding:2px 9px; border-radius:99px; font-size:11px; font-weight:700; }
.sa-input { padding:8px 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; font-size:13px; background:var(--c-surface,#fff); color:var(--c-text,#111827); outline:none; }
.sa-input:focus { border-color:#22c55e; }
.mono     { font-family:monospace; font-size:11px; color:var(--c-sub,#6b7280); word-break:break-all; }
</style>

<div class="sa-wrap">

    <div>
        <h1 style="font-size:20px;font-weight:800;color:var(--c-text,#111827);margin:0">Widgets</h1>
        <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:4px 0 0">Vista de solo lectura de todos los widgets activos por organización</p>
    </div>

    <div style="display:flex;gap:10px;align-items:center">
        <input wire:model.live.debounce.300ms="search"
               class="sa-input" style="width:280px"
               placeholder="Buscar por widget, org, dominio…">
        <span style="font-size:12px;color:var(--c-sub,#6b7280);margin-left:auto">
            {{ $this->widgets->total() }} widget(s)
        </span>
    </div>

    <div class="sa-card" style="overflow-x:auto">
        <table class="sa-tbl">
            <thead>
                <tr>
                    <th>Widget</th>
                    <th>Organización</th>
                    <th>Dominios permitidos</th>
                    <th>Token</th>
                    <th>Estado</th>
                    <th>Creado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->widgets as $widget)
                @php
                    $org = $widget->organization;
                    $domains = collect(explode(',', $widget->allowed_domains ?? ''))
                        ->map(fn($d) => trim($d))->filter()->values();
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:700;color:var(--c-text,#111827)">{{ $widget->name }}</div>
                        @if($widget->bot_name)
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">Bot: {{ $widget->bot_name }}</div>
                        @endif
                    </td>
                    <td>
                        @if($org)
                        <div style="font-weight:600">{{ $org->name }}</div>
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">
                            <span class="sa-badge" style="background:{{ $org->plan === 'pro' ? '#dcfce7' : '#f3f4f6' }};color:{{ $org->plan === 'pro' ? '#15803d' : '#6b7280' }}">
                                {{ strtoupper($org->plan) }}
                            </span>
                        </div>
                        @else
                        <span style="color:var(--c-sub,#6b7280)">Sin org</span>
                        @endif
                    </td>
                    <td style="max-width:220px">
                        @if($domains->count())
                            @foreach($domains->take(3) as $domain)
                            <div class="mono">{{ $domain }}</div>
                            @endforeach
                            @if($domains->count() > 3)
                            <div style="font-size:11px;color:var(--c-sub,#6b7280)">+{{ $domains->count()-3 }} más</div>
                            @endif
                        @else
                            <span style="font-size:12px;color:var(--c-sub,#6b7280)">Todos los dominios</span>
                        @endif
                    </td>
                    <td>
                        <div class="mono" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                             title="{{ $widget->token }}">
                            {{ $widget->token ? substr($widget->token, 0, 16).'…' : '—' }}
                        </div>
                    </td>
                    <td>
                        @if($widget->is_active && $org?->is_active)
                            <span class="sa-badge" style="background:#dcfce7;color:#15803d">Activo</span>
                        @elseif(!$org?->is_active)
                            <span class="sa-badge" style="background:#fee2e2;color:#b91c1c">Org desactivada</span>
                        @else
                            <span class="sa-badge" style="background:#f3f4f6;color:#6b7280">Inactivo</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--c-sub,#6b7280);white-space:nowrap">
                        {{ $widget->created_at->format('d/m/Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:var(--c-sub,#6b7280);padding:40px">
                        No hay widgets registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $this->widgets->links() }}</div>

</div>
</x-filament-panels::page>
