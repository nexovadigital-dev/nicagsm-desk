<x-filament-panels::page>
<style>
.sa-wrap    { display:flex; flex-direction:column; gap:24px; }
.sa-card    { background:var(--c-surface,#fff); border:1px solid var(--c-border,#e3e6ea); border-radius:12px; overflow:hidden; }
.sa-card-head { padding:14px 20px; border-bottom:1px solid var(--c-border,#e3e6ea); font-size:13px; font-weight:700; color:var(--c-text,#111827); display:flex; align-items:center; justify-content:space-between; }
.sa-modal-wide { background:var(--c-surface,#fff); border-radius:14px; width:700px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,.18); max-height:85vh; display:flex; flex-direction:column; }
.sa-tbl     { width:100%; border-collapse:collapse; font-size:13px; }
.sa-tbl th  { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--c-sub,#6b7280); border-bottom:1px solid var(--c-border,#e3e6ea); }
.sa-tbl td  { padding:12px 16px; border-bottom:1px solid var(--c-border,#e3e6ea); color:var(--c-text,#111827); vertical-align:middle; }
.sa-tbl tr:last-child td { border-bottom:none; }
.sa-tbl tr:hover td { background:var(--c-surf2,#f9fafb); }
.sa-badge   { display:inline-block; padding:2px 10px; border-radius:99px; font-size:11px; font-weight:700; }
.sa-btn     { display:inline-flex; align-items:center; gap:4px; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; border:none; transition:opacity .15s; }
.sa-btn:hover { opacity:.85; }
.sa-input   { width:100%; padding:8px 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; font-size:13px; background:var(--c-surface,#fff); color:var(--c-text,#111827); outline:none; }
.sa-input:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.15); }
.sa-select  { padding:8px 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; font-size:13px; background:var(--c-surface,#fff); color:var(--c-text,#111827); outline:none; cursor:pointer; }
.sa-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; display:flex; align-items:center; justify-content:center; }
.sa-modal   { background:var(--c-surface,#fff); border-radius:14px; width:500px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,.18); }
.sa-modal-head { padding:18px 22px; border-bottom:1px solid var(--c-border,#e3e6ea); font-size:15px; font-weight:800; color:var(--c-text,#111827); }
.sa-modal-body { padding:22px; display:flex; flex-direction:column; gap:14px; }
.sa-modal-foot { padding:14px 22px; border-top:1px solid var(--c-border,#e3e6ea); display:flex; justify-content:flex-end; gap:10px; }
.sa-label   { font-size:12px; font-weight:700; color:var(--c-sub,#6b7280); margin-bottom:4px; text-transform:uppercase; letter-spacing:.04em; }
</style>

@php
$planColors = [
    'free'       => ['bg'=>'rgba(107,114,128,.12)', 'color'=>'#6b7280'],
    'trial'      => ['bg'=>'rgba(245,158,11,.12)',  'color'=>'#d97706'],
    'pro'        => ['bg'=>'rgba(34,197,94,.12)',   'color'=>'#16a34a'],
    'enterprise' => ['bg'=>'rgba(99,102,241,.12)',  'color'=>'#4f46e5'],
];
$planLabel = ['free'=>'Free','trial'=>'Prueba','pro'=>'Pro','enterprise'=>'Enterprise'];
@endphp

<div class="sa-wrap"
     x-data="{
         orgModal: false,
         activateModal: false,
         widgetsModal: false,
         activateOrgId: null,
         activateOrgName: '',
         activatePlanSlug: 'pro',
         activateMonths: 1
     }"
     @open-widgets-modal.window="widgetsModal = true"
     @open-org-modal.window="orgModal = true">

    {{-- Header --}}
    <div>
        <h1 style="font-size:20px;font-weight:800;color:var(--c-text,#111827);margin:0">Organizaciones</h1>
        <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:4px 0 0">Todas las cuentas registradas en Nexova Desk</p>
    </div>

    {{-- Filters --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <input wire:model.live.debounce.300ms="search"
               class="sa-input" style="max-width:260px"
               placeholder="Buscar por nombre o slug…">
        <select wire:model.live="filterPlan" class="sa-select">
            <option value="all">Todos los planes</option>
            <option value="free">Free</option>
            <option value="trial">Prueba</option>
            <option value="pro">Pro</option>
            <option value="enterprise">Enterprise</option>
        </select>
        <span style="font-size:12px;color:var(--c-sub,#6b7280);margin-left:auto">
            {{ $this->organizations->total() }} organización(es)
        </span>
    </div>

    {{-- Table --}}
    <div class="sa-card">
        <table class="sa-tbl">
            <thead>
                <tr>
                    <th>Organización</th>
                    <th>Plan</th>
                    <th>Estado</th>
                    <th>Usuarios</th>
                    <th>Creada</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->organizations as $org)
                @php
                    $pc = $planColors[$org->plan] ?? $planColors['trial'];
                    $pl = $planLabel[$org->plan] ?? $org->plan;
                    $owner = $org->users->first();
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:700;color:var(--c-text,#111827)">{{ $org->name }}</div>
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">{{ $org->slug }}
                            @if($owner) · {{ $owner->email }} @endif
                        </div>
                    </td>
                    <td>
                        <span class="sa-badge" style="background:{{ $pc['bg'] }};color:{{ $pc['color'] }}">{{ $pl }}</span>
                    </td>
                    <td>
                        @if($org->is_active)
                            <span class="sa-badge" style="background:#dcfce7;color:#15803d">Activa</span>
                        @else
                            <span class="sa-badge" style="background:#fee2e2;color:#b91c1c">Inactiva</span>
                        @endif
                    </td>
                    <td style="font-weight:600">{{ $org->users_count }}</td>
                    <td style="color:var(--c-sub,#6b7280);font-size:12px">{{ $org->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <button wire:click="openOrg({{ $org->id }})"
                                    class="sa-btn" style="background:#f3f4f6;color:#374151">
                                Editar
                            </button>
                            <button wire:click="openWidgets({{ $org->id }})"
                                    class="sa-btn" style="background:#dbeafe;color:#1d4ed8">
                                Widgets ({{ $org->chatWidgets_count ?? '?' }})
                            </button>
                            <button @click="activateOrgId={{ $org->id }}; activateOrgName='{{ addslashes($org->name) }}'; activateModal=true"
                                    class="sa-btn" style="background:#dcfce7;color:#15803d">
                                Activar plan
                            </button>
                            <button wire:click="toggleActive({{ $org->id }})"
                                    class="sa-btn" style="background:{{ $org->is_active ? '#fee2e2' : '#dcfce7' }};color:{{ $org->is_active ? '#b91c1c' : '#15803d' }}">
                                {{ $org->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                            <button wire:click="impersonate({{ $org->id }})"
                                    wire:confirm="¿Iniciar sesión como propietario de {{ $org->name }}?"
                                    class="sa-btn" style="background:#ede9fe;color:#6d28d9">
                                Entrar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:var(--c-sub,#6b7280);padding:32px">
                        No se encontraron organizaciones
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>{{ $this->organizations->links() }}</div>

    {{-- Edit org modal --}}
    <div class="sa-overlay" x-show="orgModal" x-cloak @click.self="orgModal=false" style="display:none">
        <div class="sa-modal">
            <div class="sa-modal-head">Editar organización</div>
            <div class="sa-modal-body">
                <div>
                    <div class="sa-label">Nombre</div>
                    <input wire:model="editOrgName" class="sa-input" placeholder="Nombre de la organización">
                </div>
                <div>
                    <div class="sa-label">Plan</div>
                    <select wire:model="editOrgPlan" class="sa-select" style="width:100%">
                        @foreach($this->plans as $plan)
                        <option value="{{ $plan->slug }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <input type="checkbox" wire:model="editOrgActive" id="editOrgActive" style="width:16px;height:16px">
                    <label for="editOrgActive" style="font-size:13px;font-weight:600">Organización activa</label>
                </div>
            </div>
            <div class="sa-modal-foot">
                <button @click="orgModal=false" class="sa-btn" style="background:#f3f4f6;color:#374151">Cancelar</button>
                <button wire:click="saveOrg" @click="orgModal=false"
                        class="sa-btn" style="background:#22c55e;color:#fff">Guardar</button>
            </div>
        </div>
    </div>

    {{-- Widgets modal --}}
    <div class="sa-overlay" x-show="widgetsModal" x-cloak @click.self="widgetsModal=false" style="display:none">
        <div class="sa-modal-wide">
            <div class="sa-modal-head" style="display:flex;align-items:center;justify-content:space-between">
                <span>Widgets de <strong>{{ $widgetsOrgName }}</strong></span>
                <button @click="widgetsModal=false" style="background:none;border:none;cursor:pointer;opacity:.5;padding:4px">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="overflow-y:auto;flex:1">
                @if($this->orgWidgets->isEmpty())
                <div style="padding:40px;text-align:center;color:var(--c-sub,#6b7280);font-size:13px">
                    Esta organización no tiene widgets configurados
                </div>
                @else
                <table class="sa-tbl">
                    <thead>
                        <tr>
                            <th>Widget</th>
                            <th>Dominios donde corre</th>
                            <th>Token</th>
                            <th>Estado</th>
                            <th>Creado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->orgWidgets as $w)
                        @php
                            $domains = collect(explode(',', $w->allowed_domains ?? ''))->map(fn($d)=>trim($d))->filter()->values();
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:700">{{ $w->name }}</div>
                                @if($w->bot_name)
                                <div style="font-size:11px;color:var(--c-sub,#6b7280)">Bot: {{ $w->bot_name }}</div>
                                @endif
                            </td>
                            <td style="max-width:240px">
                                @if($domains->count())
                                    @foreach($domains as $d)
                                    <div style="font-family:monospace;font-size:11px;color:var(--c-sub,#6b7280)">{{ $d }}</div>
                                    @endforeach
                                @else
                                    <span style="font-size:12px;color:var(--c-sub,#6b7280)">Todos los dominios</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-family:monospace;font-size:11px;color:var(--c-sub,#6b7280)">
                                    {{ substr($w->token ?? '', 0, 20) }}…
                                </div>
                            </td>
                            <td>
                                @if($w->is_active)
                                    <span class="sa-plan-badge" style="background:#dcfce7;color:#15803d">Activo</span>
                                @else
                                    <span class="sa-plan-badge" style="background:#f3f4f6;color:#6b7280">Inactivo</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--c-sub,#6b7280)">{{ $w->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Activate plan modal --}}
    <div class="sa-overlay" x-show="activateModal" x-cloak @click.self="activateModal=false" style="display:none">
        <div class="sa-modal">
            <div class="sa-modal-head">Activar plan manualmente</div>
            <div class="sa-modal-body">
                <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:0">
                    Organización: <strong x-text="activateOrgName"></strong>
                </p>
                <div>
                    <div class="sa-label">Plan a activar</div>
                    <select x-model="activatePlanSlug" class="sa-select" style="width:100%">
                        @foreach($this->plans as $plan)
                        <option value="{{ $plan->slug }}">{{ $plan->name }} — ${{ number_format($plan->price_usd,2) }}/mes</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="sa-label">Meses</div>
                    <input type="number" x-model="activateMonths" min="1" max="24" class="sa-input" style="width:80px">
                </div>
            </div>
            <div class="sa-modal-foot">
                <button @click="activateModal=false" class="sa-btn" style="background:#f3f4f6;color:#374151">Cancelar</button>
                <button @click="$wire.activatePlan(activateOrgId, activatePlanSlug, activateMonths); activateModal=false"
                        class="sa-btn" style="background:#22c55e;color:#fff">Activar</button>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
