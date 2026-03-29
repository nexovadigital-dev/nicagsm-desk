<x-filament-panels::page>
<style>
.sa-wrap    { display:flex; flex-direction:column; gap:24px; }
.sa-plans   { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:16px; }
.sa-plan-card { background:var(--c-surface,#fff); border:1px solid var(--c-border,#e3e6ea); border-radius:12px; overflow:hidden; }
.sa-plan-top  { padding:18px 20px; border-bottom:1px solid var(--c-border,#e3e6ea); }
.sa-plan-body { padding:18px 20px; display:flex; flex-direction:column; gap:8px; }
.sa-plan-row  { display:flex; justify-content:space-between; font-size:13px; }
.sa-plan-key  { color:var(--c-sub,#6b7280); }
.sa-plan-val  { font-weight:700; color:var(--c-text,#111827); }
.sa-btn     { display:inline-flex; align-items:center; gap:4px; padding:6px 14px; border-radius:7px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:opacity .15s; }
.sa-btn:hover { opacity:.85; }
.sa-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; display:flex; align-items:center; justify-content:center; }
.sa-modal   { background:var(--c-surface,#fff); border-radius:14px; width:520px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,.18); max-height:90vh; overflow-y:auto; }
.sa-modal-head { padding:18px 22px; border-bottom:1px solid var(--c-border,#e3e6ea); font-size:15px; font-weight:800; color:var(--c-text,#111827); position:sticky;top:0;background:var(--c-surface,#fff); }
.sa-modal-body { padding:22px; display:flex; flex-direction:column; gap:14px; }
.sa-modal-foot { padding:14px 22px; border-top:1px solid var(--c-border,#e3e6ea); display:flex; justify-content:flex-end; gap:10px; position:sticky;bottom:0;background:var(--c-surface,#fff); }
.sa-input   { width:100%; padding:8px 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; font-size:13px; background:var(--c-surface,#fff); color:var(--c-text,#111827); outline:none; box-sizing:border-box; }
.sa-input:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.15); }
.sa-label   { font-size:12px; font-weight:700; color:var(--c-sub,#6b7280); margin-bottom:4px; text-transform:uppercase; letter-spacing:.04em; }
.sa-grid2   { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.sa-badge   { display:inline-block; padding:2px 10px; border-radius:99px; font-size:11px; font-weight:700; }
</style>

@php
$planColors = [
    'free'       => ['bg'=>'rgba(107,114,128,.12)', 'color'=>'#6b7280', 'accent'=>'#6b7280'],
    'trial'      => ['bg'=>'rgba(245,158,11,.12)',  'color'=>'#d97706', 'accent'=>'#d97706'],
    'pro'        => ['bg'=>'rgba(34,197,94,.12)',   'color'=>'#16a34a', 'accent'=>'#22c55e'],
    'enterprise' => ['bg'=>'rgba(99,102,241,.12)',  'color'=>'#4f46e5', 'accent'=>'#6366f1'],
];
@endphp

<div class="sa-wrap"
     x-data="{ planModal: false }"
     @open-plan-modal.window="planModal = true"
     @close-plan-modal.window="planModal = false">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-size:20px;font-weight:800;color:var(--c-text,#111827);margin:0">Planes</h1>
            <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:4px 0 0">Gestiona los planes de suscripción disponibles para organizaciones</p>
        </div>
        <button wire:click="createPlan" class="sa-btn" style="background:#22c55e;color:#fff">
            + Nuevo plan
        </button>
    </div>

    {{-- Plan cards --}}
    <div class="sa-plans">
        @foreach($this->plans as $plan)
        @php $pc = $planColors[$plan->slug] ?? $planColors['trial']; @endphp
        <div class="sa-plan-card">
            <div class="sa-plan-top">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                    <span class="sa-badge" style="background:{{ $pc['bg'] }};color:{{ $pc['color'] }}">{{ $plan->slug }}</span>
                    @if($plan->is_active)
                        <span class="sa-badge" style="background:#dcfce7;color:#15803d">Activo</span>
                    @else
                        <span class="sa-badge" style="background:#fee2e2;color:#b91c1c">Inactivo</span>
                    @endif
                </div>
                <div style="font-size:20px;font-weight:900;color:var(--c-text,#111827);line-height:1">
                    ${{ number_format($plan->price_usd, 2) }}
                    <span style="font-size:13px;font-weight:500;color:var(--c-sub,#6b7280)">/mes</span>
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--c-text,#111827);margin-top:4px">{{ $plan->name }}</div>
                @if($plan->description)
                <div style="font-size:12px;color:var(--c-sub,#6b7280);margin-top:4px">{{ $plan->description }}</div>
                @endif
            </div>
            <div class="sa-plan-body">
                <div class="sa-plan-row">
                    <span class="sa-plan-key">Agentes</span>
                    <span class="sa-plan-val">{{ $plan->max_agents >= 999 ? 'Ilimitado' : $plan->max_agents }}</span>
                </div>
                <div class="sa-plan-row">
                    <span class="sa-plan-key">Widgets</span>
                    <span class="sa-plan-val">{{ $plan->max_widgets >= 999 ? 'Ilimitado' : $plan->max_widgets }}</span>
                </div>
                <div class="sa-plan-row">
                    <span class="sa-plan-key">Sesiones / día</span>
                    <span class="sa-plan-val">{{ $plan->max_sessions_per_day >= 999999 ? 'Ilimitado' : number_format($plan->max_sessions_per_day) }}</span>
                </div>
                <div class="sa-plan-row">
                    <span class="sa-plan-key">Msgs / sesión</span>
                    <span class="sa-plan-val">{{ $plan->max_messages_per_session >= 999 ? 'Ilimitado' : $plan->max_messages_per_session }}</span>
                </div>
                <div class="sa-plan-row">
                    <span class="sa-plan-key">Msgs bot/mes</span>
                    <span class="sa-plan-val">{{ ($plan->max_bot_messages_monthly ?? 0) === 0 ? '∞ Ilimitado' : number_format($plan->max_bot_messages_monthly) }}</span>
                </div>
                <div class="sa-plan-row">
                    <span class="sa-plan-key">IA bloqueada</span>
                    <span class="sa-plan-val">
                        @if($plan->ai_blocked)
                            <span style="color:#ef4444">Sí (solo KB)</span>
                        @else
                            <span style="color:#22c55e">No (IA activa)</span>
                        @endif
                    </span>
                </div>
                <div style="margin-top:4px">
                    <button wire:click="edit({{ $plan->id }})"
                            class="sa-btn" style="background:#f3f4f6;color:#374151;width:100%;justify-content:center">
                        Editar plan
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Create / Edit modal --}}
    <div class="sa-overlay" x-show="planModal" x-cloak @click.self="planModal=false" style="display:none">
        <div class="sa-modal">
            <div class="sa-modal-head" x-text="$wire.creating ? 'Nuevo plan' : 'Editar plan'">Plan</div>
            <div class="sa-modal-body">
                <div class="sa-grid2">
                    <div>
                        <div class="sa-label">Nombre</div>
                        <input wire:model="editName" class="sa-input" placeholder="Ej. Starter">
                    </div>
                    <div>
                        <div class="sa-label">Precio USD / mes</div>
                        <input wire:model="editPriceUsd" type="number" step="0.01" min="0" class="sa-input" placeholder="0.00">
                    </div>
                </div>
                <div class="sa-grid2">
                    <div>
                        <div class="sa-label">Slug <span style="font-size:11px;color:#9ca3af">(auto si vacío)</span></div>
                        <input wire:model="editSlug" class="sa-input" placeholder="starter">
                    </div>
                    <div>
                        <div class="sa-label">Orden (sort)</div>
                        <input wire:model="editSort" type="number" min="0" class="sa-input">
                    </div>
                </div>
                <div>
                    <div class="sa-label">Descripción</div>
                    <input wire:model="editDescription" class="sa-input" placeholder="Descripción breve del plan">
                </div>
                <div class="sa-grid2">
                    <div>
                        <div class="sa-label">Máx. agentes</div>
                        <input wire:model="editMaxAgents" type="number" min="1" class="sa-input">
                    </div>
                    <div>
                        <div class="sa-label">Máx. widgets</div>
                        <input wire:model="editMaxWidgets" type="number" min="1" class="sa-input">
                    </div>
                </div>
                <div class="sa-grid2">
                    <div>
                        <div class="sa-label">Sesiones / día</div>
                        <input wire:model="editMaxSessionsPerDay" type="number" min="1" class="sa-input">
                    </div>
                    <div>
                        <div class="sa-label">Msgs / sesión</div>
                        <input wire:model="editMaxMsgPerSession" type="number" min="1" class="sa-input">
                    </div>
                </div>
                <div>
                    <div class="sa-label">Msgs bot / mes <span style="font-size:11px;color:#9ca3af">(0 = ilimitado)</span></div>
                    <input wire:model="editMaxBotMessages" type="number" min="0" class="sa-input">
                </div>

                {{-- Feature flags --}}
                <div>
                    <div class="sa-label" style="margin-bottom:8px">Funcionalidades incluidas</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                        @foreach(\App\Filament\SuperAdmin\Pages\PlansManager::AVAILABLE_FEATURES as $key => $label)
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;padding:6px 8px;border:1px solid var(--c-border,#e5e7eb);border-radius:7px;transition:background .1s"
                               onmouseover="this.style.background='rgba(34,197,94,.06)'" onmouseout="this.style.background='transparent'">
                            <input type="checkbox" wire:model="editFeatures" value="{{ $key }}" style="width:14px;height:14px;accent-color:#22c55e">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:10px">
                    <input type="checkbox" wire:model="editIsActive" id="editIsActive" style="width:16px;height:16px;accent-color:#22c55e">
                    <label for="editIsActive" style="font-size:13px;font-weight:600">Plan activo (visible para comprar)</label>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <input type="checkbox" wire:model="editAiBlocked" id="editAiBlocked" style="width:16px;height:16px;accent-color:#ef4444">
                    <label for="editAiBlocked" style="font-size:13px;font-weight:600;color:#ef4444">Bloquear IA (solo responde desde KB)</label>
                </div>
            </div>
            <div class="sa-modal-foot">
                <button @click="planModal=false" class="sa-btn" style="background:#f3f4f6;color:#374151">Cancelar</button>
                <button wire:click="savePlan" class="sa-btn" style="background:#22c55e;color:#fff">Guardar</button>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
