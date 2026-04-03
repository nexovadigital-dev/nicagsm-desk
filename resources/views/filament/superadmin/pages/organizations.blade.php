<x-filament-panels::page>
<style>
.sa-wrap    { display:flex; flex-direction:column; gap:24px; }
.sa-card    { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
.sa-modal-wide { background:#fff; border-radius:14px; width:700px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,.14); max-height:85vh; display:flex; flex-direction:column; }
.sa-tbl     { width:100%; border-collapse:collapse; font-size:13px; }
.sa-tbl th  { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#64748b; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
.sa-tbl td  { padding:12px 16px; border-bottom:1px solid #f1f5f9; color:#0f172a; vertical-align:middle; }
.sa-tbl tr:last-child td { border-bottom:none; }
.sa-tbl tr:hover td { background:#f8fafc; }
.sa-badge   { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:700; }
.sa-btn     { display:inline-flex; align-items:center; gap:4px; padding:5px 11px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; border:none; font-family:inherit; transition:opacity .15s; white-space:nowrap; }
.sa-btn:hover { opacity:.82; }
.sa-input   { width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; font-family:inherit; background:#fff; color:#0f172a; outline:none; box-sizing:border-box; transition:border .15s; }
.sa-input:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.12); }
.sa-select  { padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; font-family:inherit; background:#fff; color:#0f172a; outline:none; cursor:pointer; }
.sa-select:focus { border-color:#22c55e; }
.sa-overlay { position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:50; display:flex; align-items:center; justify-content:center; }
.sa-modal   { background:#fff; border-radius:14px; width:500px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,.14); }
.sa-modal-head { padding:18px 22px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:800; color:#0f172a; }
.sa-modal-body { padding:22px; display:flex; flex-direction:column; gap:14px; }
.sa-modal-foot { padding:14px 22px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px; background:#f8fafc; border-radius:0 0 14px 14px; }
.sa-label   { font-size:11.5px; font-weight:700; color:#64748b; margin-bottom:5px; text-transform:uppercase; letter-spacing:.05em; display:block; }
</style>

@php
$planColors = [
    'free'       => ['bg'=>'rgba(107,114,128,.1)', 'color'=>'#4b5563'],
    'trial'      => ['bg'=>'rgba(245,158,11,.1)',  'color'=>'#b45309'],
    'pro'        => ['bg'=>'rgba(34,197,94,.12)',  'color'=>'#15803d'],
    'enterprise' => ['bg'=>'rgba(99,102,241,.1)',  'color'=>'#4338ca'],
];
// Build label map from DB plans
$planLabelMap = $this->plans->pluck('name','slug')->toArray();
$planLabelMap = array_merge(['free'=>'Free','trial'=>'Prueba'], $planLabelMap);
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
     @open-org-modal.window="orgModal = true"
     @close-org-modal.window="orgModal = false">

    {{-- Header --}}
    <div>
        <h1 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;letter-spacing:-.02em">Organizaciones</h1>
        <p style="font-size:13px;color:#64748b;margin:4px 0 0">Todas las cuentas registradas en Nexova Desk</p>
    </div>

    {{-- Filters --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <input wire:model.live.debounce.300ms="search"
               class="sa-input" style="max-width:220px"
               placeholder="Buscar por nombre o slug…">
        <select wire:model.live="filterPlan" class="sa-select">
            <option value="all">Todos los planes</option>
            @foreach($this->plans as $p)
            <option value="{{ $p->slug }}">{{ $p->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" class="sa-select">
            <option value="all">Activo / Inactivo</option>
            <option value="active">Solo activas</option>
            <option value="inactive">Solo inactivas</option>
        </select>
        <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;white-space:nowrap">
            <input type="checkbox" wire:model.live="filterExpiry" style="accent-color:#22c55e;width:15px;height:15px">
            Vence en ≤ 7 días
        </label>
        <span style="font-size:12px;color:#64748b;margin-left:auto">
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
                    <th>IA</th>
                    <th>Suscripción</th>
                    <th>Estado</th>
                    <th>Usuarios</th>
                    <th>Creada</th>
                    <th style="min-width:280px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->organizations as $org)
                @php
                    $pc    = $planColors[$org->plan] ?? ['bg'=>'rgba(245,158,11,.1)','color'=>'#b45309'];
                    $pl    = $planLabelMap[$org->plan] ?? $org->plan;
                    $owner = $org->users->first();
                    $sub   = $org->activeSubscription;
                    $hasSub = $sub && $sub->isActive();
                    $isPartner = (bool) $org->is_partner;
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:7px;font-weight:700;color:#0f172a">
                            {{ $org->name }}
                            @if($isPartner)
                            <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:800;background:linear-gradient(135deg,#064e3b,#065f46);color:#6ee7b7;letter-spacing:.03em;border:1px solid rgba(110,231,183,.25)">
                                <svg fill="currentColor" viewBox="0 0 20 20" width="9" height="9"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                PARTNER
                            </span>
                            @endif
                        </div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:2px">
                            {{ $org->slug }}
                            @if($owner) · {{ $owner->email }} @endif
                        </div>
                    </td>
                    <td>
                        @if($isPartner)
                        <span class="sa-badge" style="background:rgba(6,78,59,.3);color:#6ee7b7;border:1px solid rgba(110,231,183,.2)">Partner</span>
                        @else
                        <span class="sa-badge" style="background:{{ $pc['bg'] }};color:{{ $pc['color'] }}">{{ $pl }}</span>
                        @endif
                    </td>
                    <td>
                        @php $hasOwnAi = $org->ai_use_own_keys && ($org->getRawOriginal('ai_groq_key') || $org->getRawOriginal('ai_gemini_key')); @endphp
                        @if($hasOwnAi)
                            <span class="sa-badge" style="background:rgba(99,102,241,.1);color:#6366f1;font-size:9px">
                                <svg fill="currentColor" viewBox="0 0 8 8" width="5" height="5"><circle cx="4" cy="4" r="4"/></svg>
                                Propia
                            </span>
                        @else
                            <span style="font-size:11px;color:#94a3b8">Global</span>
                        @endif
                    </td>
                    <td>
                        @if($hasSub)
                            <div style="font-size:12px;color:#0f172a;font-weight:600">
                                Activa
                            </div>
                            <div style="font-size:11px;color:#64748b;margin-top:1px">
                                Vence {{ $sub->ends_at->format('d/m/Y') }}
                                @php $days = $sub->daysRemaining(); @endphp
                                @if($days <= 7)
                                    <span style="color:#dc2626;font-weight:700">({{ $days }}d)</span>
                                @else
                                    <span style="color:#64748b">({{ $days }}d)</span>
                                @endif
                            </div>
                        @elseif($org->plan === 'trial' && $org->trial_ends_at)
                            <div style="font-size:12px;color:#b45309;font-weight:600">Prueba</div>
                            <div style="font-size:11px;color:#64748b;margin-top:1px">
                                Hasta {{ $org->trial_ends_at->format('d/m/Y') }}
                            </div>
                        @else
                            <span style="font-size:12px;color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td>
                        @if($org->is_active)
                            <span class="sa-badge" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d">
                                <span style="width:5px;height:5px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>
                                Activa
                            </span>
                        @else
                            <span class="sa-badge" style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c">
                                <span style="width:5px;height:5px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
                                Inactiva
                            </span>
                        @endif
                    </td>
                    <td style="font-weight:600;color:#0f172a">{{ $org->users_count }}</td>
                    <td style="color:#94a3b8;font-size:12px">{{ $org->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap">
                            <button wire:click="openOrg({{ $org->id }})"
                                    class="sa-btn" style="background:#f1f5f9;color:#374151">
                                Editar
                            </button>
                            <button wire:click="openWidgets({{ $org->id }})"
                                    class="sa-btn" style="background:#eff6ff;color:#1d4ed8">
                                Widgets ({{ $org->chat_widgets_count ?? 0 }})
                            </button>
                            @if($hasSub)
                            <button @click="activateOrgId={{ $org->id }}; activateOrgName={{ json_encode($org->name) }}; activatePlanSlug='{{ $org->plan }}'; activateModal=true"
                                    class="sa-btn" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa">
                                Cambiar plan
                            </button>
                            @else
                            <button @click="activateOrgId={{ $org->id }}; activateOrgName={{ json_encode($org->name) }}; activatePlanSlug='{{ $org->plan === 'trial' ? 'pro' : $org->plan }}'; activateModal=true"
                                    class="sa-btn" style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0">
                                Activar plan
                            </button>
                            @endif
                            <button wire:click="toggleActive({{ $org->id }})"
                                    class="sa-btn"
                                    style="background:{{ $org->is_active ? '#fef2f2' : '#f0fdf4' }};color:{{ $org->is_active ? '#b91c1c' : '#15803d' }}">
                                {{ $org->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                            @if($isPartner)
                            <button wire:click="revokePartner({{ $org->id }})"
                                    wire:confirm="¿Revocar acceso Partner a {{ $org->name }}? Se moverá al plan Free."
                                    class="sa-btn" style="background:rgba(6,78,59,.15);color:#6ee7b7;border:1px solid rgba(110,231,183,.2)">
                                <svg fill="currentColor" viewBox="0 0 20 20" width="10" height="10"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Partner ✓
                            </button>
                            @else
                            <button wire:click="assignPartner({{ $org->id }})"
                                    wire:confirm="¿Asignar Plan Partner a {{ $org->name }}? Tendrá acceso ilimitado."
                                    class="sa-btn" style="background:#f0fdf4;color:#065f46;border:1px solid #bbf7d0">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                Partner
                            </button>
                            @endif
                            <button wire:click="impersonate({{ $org->id }})"
                                    wire:confirm="¿Iniciar sesión como propietario de {{ $org->name }}?"
                                    class="sa-btn" style="background:#f5f3ff;color:#6d28d9">
                                Entrar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#94a3b8;padding:40px 16px;font-size:13px">
                        No se encontraron organizaciones
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>{{ $this->organizations->links() }}</div>

    {{-- ── Edit org modal ── --}}
    <div class="sa-overlay" x-show="orgModal" x-cloak @click.self="orgModal=false" style="display:none">
        <div class="sa-modal" style="width:560px;max-width:96vw">
            <div class="sa-modal-head" style="display:flex;align-items:center;justify-content:space-between">
                <span>Editar organización</span>
                <button @click="orgModal=false" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;display:flex" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#94a3b8'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="sa-modal-body" style="max-height:75vh;overflow-y:auto">

                {{-- General --}}
                <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:12px;padding-bottom:6px;border-bottom:1px solid #f1f5f9">General</div>
                <div>
                    <label class="sa-label">Nombre</label>
                    <input wire:model="editOrgName" class="sa-input" placeholder="Nombre de la organización">
                </div>
                <div>
                    <label class="sa-label">Plan</label>
                    <select wire:model="editOrgPlan" class="sa-select" style="width:100%">
                        @foreach($this->plans as $plan)
                        <option value="{{ $plan->slug }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                    <p style="font-size:11.5px;color:#f59e0b;margin:6px 0 0;display:flex;align-items:center;gap:5px">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        Cambiar plan expira la suscripción activa. Para nueva suscripción usa "Activar plan".
                    </p>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <input type="checkbox" wire:model="editOrgActive" id="editOrgActive" style="width:16px;height:16px;accent-color:#22c55e">
                    <label for="editOrgActive" style="font-size:13px;font-weight:600;color:#0f172a;cursor:pointer">Organización activa</label>
                </div>

                {{-- AI Keys --}}
                <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin:20px 0 12px;padding-bottom:6px;border-bottom:1px solid #f1f5f9">Credenciales IA</div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                    <input type="checkbox" wire:model="editAiUseOwnKeys" id="editAiUseOwnKeys" style="width:16px;height:16px;accent-color:#22c55e">
                    <label for="editAiUseOwnKeys" style="font-size:13px;font-weight:600;color:#0f172a;cursor:pointer">Usar claves propias de IA</label>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label class="sa-label" style="display:flex;align-items:center;gap:6px">
                            Groq API Key
                            @if($editAiHasGroq)
                                <span style="display:inline-flex;align-items:center;gap:3px;font-size:9px;font-weight:700;color:#059669;background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.18);border-radius:99px;padding:1px 6px">
                                    <svg fill="currentColor" viewBox="0 0 8 8" width="5" height="5"><circle cx="4" cy="4" r="4"/></svg>Configurada
                                </span>
                            @endif
                        </label>
                        <input type="password" wire:model="editAiGroqKey" class="sa-input" style="font-family:monospace;font-size:12px"
                               placeholder="{{ $editAiHasGroq ? '••••••••• (dejar vacío para mantener)' : 'gsk_...' }}"
                               autocomplete="off">
                        @if($editAiHasGroq)
                        <button type="button" wire:click="clearOrgAiKey('groq')"
                                style="margin-top:5px;font-size:11px;color:#dc2626;background:none;border:none;cursor:pointer;padding:0;font-family:inherit"
                                onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                            Eliminar clave Groq
                        </button>
                        @endif
                    </div>
                    <div>
                        <label class="sa-label" style="display:flex;align-items:center;gap:6px">
                            Gemini API Key
                            @if($editAiHasGemini)
                                <span style="display:inline-flex;align-items:center;gap:3px;font-size:9px;font-weight:700;color:#059669;background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.18);border-radius:99px;padding:1px 6px">
                                    <svg fill="currentColor" viewBox="0 0 8 8" width="5" height="5"><circle cx="4" cy="4" r="4"/></svg>Configurada
                                </span>
                            @endif
                        </label>
                        <input type="password" wire:model="editAiGeminiKey" class="sa-input" style="font-family:monospace;font-size:12px"
                               placeholder="{{ $editAiHasGemini ? '••••••••• (dejar vacío para mantener)' : 'AIza...' }}"
                               autocomplete="off">
                        @if($editAiHasGemini)
                        <button type="button" wire:click="clearOrgAiKey('gemini')"
                                style="margin-top:5px;font-size:11px;color:#dc2626;background:none;border:none;cursor:pointer;padding:0;font-family:inherit"
                                onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                            Eliminar clave Gemini
                        </button>
                        @endif
                    </div>
                </div>
                <p style="font-size:11.5px;color:#64748b;margin-top:8px;line-height:1.5">
                    Las claves se almacenan cifradas. Deja el campo vacío para no cambiar la clave existente. Activa "Usar claves propias" para que esta org use sus propias claves en lugar de las globales.
                </p>
            </div>
            <div class="sa-modal-foot">
                <button @click="orgModal=false" class="sa-btn" style="background:#f1f5f9;color:#374151">Cancelar</button>
                <button wire:click="saveOrg"
                        wire:confirm="¿Guardar cambios? Si cambias el plan, la suscripción activa quedará inactiva."
                        class="sa-btn" style="background:#22c55e;color:#fff">Guardar cambios</button>
            </div>
        </div>
    </div>

    {{-- ── Widgets modal ── --}}
    <div class="sa-overlay" x-show="widgetsModal" x-cloak @click.self="widgetsModal=false" style="display:none">
        <div class="sa-modal-wide">
            <div style="padding:18px 22px;border-bottom:1px solid #e2e8f0;font-size:15px;font-weight:800;color:#0f172a;display:flex;align-items:center;justify-content:space-between">
                <span>Widgets de <strong>{{ $widgetsOrgName }}</strong></span>
                <button @click="widgetsModal=false" style="background:none;border:none;cursor:pointer;opacity:.4;padding:4px;transition:opacity .1s" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="overflow-y:auto;flex:1">
                @if($this->orgWidgets->isEmpty())
                <div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px">
                    Esta organización no tiene widgets configurados
                </div>
                @else
                <table class="sa-tbl">
                    <thead>
                        <tr>
                            <th>Widget</th>
                            <th>Dominios</th>
                            <th>Token</th>
                            <th>Estado</th>
                            <th>Creado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->orgWidgets as $w)
                        @php $domains = collect(explode(',', $w->allowed_domains ?? ''))->map(fn($d)=>trim($d))->filter()->values(); @endphp
                        <tr>
                            <td>
                                <div style="font-weight:700">{{ $w->name }}</div>
                                @if($w->bot_name)
                                <div style="font-size:11px;color:#94a3b8">Bot: {{ $w->bot_name }}</div>
                                @endif
                            </td>
                            <td style="max-width:200px">
                                @if($domains->count())
                                    @foreach($domains as $d)
                                    <div style="font-family:monospace;font-size:11px;color:#64748b">{{ $d }}</div>
                                    @endforeach
                                @else
                                    <span style="font-size:12px;color:#94a3b8">Todos los dominios</span>
                                @endif
                            </td>
                            <td style="font-family:monospace;font-size:11px;color:#94a3b8">{{ substr($w->token ?? '', 0, 18) }}…</td>
                            <td>
                                @if($w->is_active)
                                    <span class="sa-badge" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d">Activo</span>
                                @else
                                    <span class="sa-badge" style="background:#f1f5f9;color:#64748b">Inactivo</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#94a3b8">{{ $w->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Activate / change plan modal ── --}}
    <div class="sa-overlay" x-show="activateModal" x-cloak @click.self="activateModal=false" style="display:none">
        <div class="sa-modal">
            <div class="sa-modal-head" x-text="'Plan para: ' + activateOrgName"></div>
            <div class="sa-modal-body">
                <div>
                    <label class="sa-label">Plan a activar</label>
                    <select x-model="activatePlanSlug" class="sa-select" style="width:100%">
                        @foreach($this->plans as $plan)
                        @if(!$plan->isFree())
                        <option value="{{ $plan->slug }}">{{ $plan->name }} — ${{ number_format($plan->price_usd,2) }}/mes</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="sa-label">Duración (meses)</label>
                    <input type="number" x-model="activateMonths" min="1" max="36" class="sa-input" style="max-width:100px">
                </div>
                <div style="padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;font-size:12.5px;color:#166534">
                    <strong>Nota:</strong> Si hay una suscripción activa, será marcada como expirada y se creará una nueva.
                </div>
            </div>
            <div class="sa-modal-foot">
                <button @click="activateModal=false" class="sa-btn" style="background:#f1f5f9;color:#374151">Cancelar</button>
                <button @click="$wire.activatePlan(activateOrgId, activatePlanSlug, Number(activateMonths)); activateModal=false"
                        class="sa-btn" style="background:#22c55e;color:#fff">Confirmar activación</button>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
