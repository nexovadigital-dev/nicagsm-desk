<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

.ak-wrap { padding: 32px 36px 64px; }

/* Grid de cards de proveedores */
.ak-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 16px;
    max-width: 1100px;
}
@media (max-width: 600px) { .ak-grid { grid-template-columns: 1fr; } }

.ak-card {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 10px;
    padding: 18px 20px;
    display: flex; flex-direction: column; gap: 14px;
}

/* Header del proveedor */
.ak-head { display: flex; align-items: center; gap: 12px; }
.ak-icon {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; color: #fff;
    letter-spacing: -.02em; flex-shrink: 0;
}
.ak-title { font-size: 13px; font-weight: 700; color: var(--c-text,#111); }
.ak-desc  { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 1px; }
.ak-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 99px;
    margin-left: auto; flex-shrink: 0;
}
.ak-badge-on  { background: rgba(5,150,105,.08); color:#059669; border:1px solid rgba(5,150,105,.18); }
.ak-badge-off { background: rgba(107,114,128,.08); color:#9ca3af; border:1px solid rgba(107,114,128,.18); }

/* Campos */
.ak-field { display: flex; flex-direction: column; gap: 4px; }
.ak-label { font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .05em; }
.ak-input-wrap { position: relative; }
.ak-input {
    width: 100%; background: var(--c-bg,#f5f6f8);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 7px; color: var(--c-text,#111);
    font-size: 13px; font-family: monospace;
    padding: 8px 38px 8px 11px; outline: none; box-sizing: border-box;
    transition: border-color .12s;
}
.ak-input:focus { border-color: #16a34a; }
.ak-input.normal-font { font-family: inherit; padding-right: 11px; }
.ak-eye {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--c-sub,#6b7280); padding: 0; display: flex;
}
.ak-eye:hover { color: var(--c-text,#111); }

/* Row prioridad + acciones */
.ak-footer { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.ak-select {
    background: var(--c-bg,#f5f6f8);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 7px; color: var(--c-text,#111);
    font-size: 12px; padding: 7px 10px; outline: none; font-family: inherit;
}
.ak-select:focus { border-color: #16a34a; }

.ak-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 13px; border-radius: 7px; font-size: 12px;
    font-weight: 500; cursor: pointer; border: 1px solid transparent;
    font-family: inherit; transition: background .1s;
}
.ak-btn-primary { background: #1e293b; color:#f8fafc; }
.ak-btn-primary:hover { background: #0f172a; }
.ak-btn-outline { background: transparent; color: var(--c-text,#111); border-color: var(--c-border,#e3e6ea); }
.ak-btn-outline:hover { background: var(--c-surf2,#f0f2f5); }
.ak-btn-danger  { background: transparent; color: #dc2626; border-color: rgba(220,38,38,.25); }
.ak-btn-danger:hover { background: rgba(220,38,38,.05); }

.ak-divider { height: 1px; background: var(--c-border,#e3e6ea); }

.ak-info {
    background: var(--c-bg,#f5f6f8);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 8px; padding: 11px 14px;
    font-size: 12px; color: var(--c-sub,#6b7280); line-height: 1.6;
    max-width: 1100px; margin-top: 4px;
}
</style>

<div class="ak-wrap">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:28px;letter-spacing:-.02em">Claves API</h1>

    <div class="ak-grid">
        @foreach($providers as $key => $info)
        @php
            $hasKey = !empty($keys[$key]);
            $isOn   = $activeFlags[$key] ?? true;
        @endphp
        <div class="ak-card">

            <div class="ak-head">
                <div class="ak-icon" style="background:{{ $info['color'] }}">{{ $info['letter'] }}</div>
                <div style="flex:1;min-width:0">
                    <div class="ak-title">{{ $info['label'] }}</div>
                    <div class="ak-desc">{{ $info['desc'] }}</div>
                </div>
                @if($hasKey)
                    <span class="ak-badge {{ $isOn ? 'ak-badge-on' : 'ak-badge-off' }}">
                        <svg fill="currentColor" viewBox="0 0 8 8" width="6" height="6"><circle cx="4" cy="4" r="4"/></svg>
                        {{ $isOn ? 'Activa' : 'Inactiva' }}
                    </span>
                @else
                    <span class="ak-badge ak-badge-off">Sin configurar</span>
                @endif
            </div>

            <div class="ak-field">
                <label class="ak-label">API Key</label>
                <div class="ak-input-wrap" x-data="{ show: false }">
                    <input
                        :type="show ? 'text' : 'password'"
                        class="ak-input"
                        wire:model.defer="keys.{{ $key }}"
                        placeholder="{{ $hasKey ? '••••••••••••••••••••••' : 'Pega tu clave aquí…' }}"
                        autocomplete="off">
                    <button type="button" class="ak-eye" @click="show = !show" tabindex="-1">
                        <svg x-show="!show" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            @if($key === 'meta_whatsapp')
            <div class="ak-field">
                <label class="ak-label">Token de verificación Webhook</label>
                <input type="text" class="ak-input normal-font"
                    wire:model.defer="webhooks.{{ $key }}"
                    placeholder="Token de verificación Meta…" autocomplete="off">
            </div>
            @endif

            <div class="ak-divider"></div>

            <div class="ak-footer">
                <button class="ak-btn ak-btn-primary" wire:click="save('{{ $key }}')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar
                </button>
                @if($hasKey)
                    <button class="ak-btn ak-btn-outline" wire:click="toggleActive('{{ $key }}')">
                        {{ $isOn ? 'Desactivar' : 'Activar' }}
                    </button>
                    <button class="ak-btn ak-btn-danger" wire:click="delete('{{ $key }}')"
                        wire:confirm="¿Eliminar la clave de {{ $info['label'] }}?">
                        Eliminar
                    </button>
                @endif
            </div>

        </div>
        @endforeach
    </div>

    <div class="ak-info" style="margin-top:16px">
        <strong>Telegram:</strong> El token del bot se configura desde
        <a href="{{ route('filament.admin.pages.channels-settings') }}" style="color:#2563eb;text-decoration:none">
            Canales → Telegram Bot
        </a>
        y se guarda directamente en el <code>.env</code>.
    </div>

</div>

{{-- ── API propias de la organización (solo owner/admin) ── --}}
@if($this->isOrgAdmin())
<div style="margin-top:40px;border-top:1px solid var(--c-border,#e3e6ea);padding-top:28px">

    {{-- Keys propias --}}
    <div style="margin-bottom:20px">
        <h2 style="font-size:16px;font-weight:700;color:var(--c-text,#111827);margin:0 0 4px">Llaves API de tu organización</h2>
        <p style="font-size:12.5px;color:var(--c-sub,#6b7280);margin:0 0 20px;line-height:1.6">Opcional. Si las configuras y activas, el bot usará tus propias llaves en lugar de las de la plataforma.</p>
        <div style="max-width:600px">
        <div style="padding:20px 22px;display:flex;flex-direction:column;gap:14px">

            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;color:var(--c-text,#111827)">
                <input type="checkbox" wire:model.live="orgUseOwnKeys" style="width:15px;height:15px;accent-color:#22c55e">
                Usar mis propias llaves API (si están configuradas)
            </label>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label style="font-size:11px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:5px">Groq API Key</label>
                    <input type="password" wire:model="orgGroqKey" class="ak-input" placeholder="gsk_... (dejar vacío para no cambiar)" style="width:100%;box-sizing:border-box">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:5px">Gemini API Key</label>
                    <input type="password" wire:model="orgGeminiKey" class="ak-input" placeholder="AIza... (dejar vacío para no cambiar)" style="width:100%;box-sizing:border-box">
                </div>
            </div>

            <div>
                <button class="ak-btn ak-btn-primary" wire:click="saveOrgKeys" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveOrgKeys">Guardar configuración</span>
                    <span wire:loading wire:target="saveOrgKeys">Guardando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Límites de uso --}}
    <div style="margin-top:28px;padding-top:28px;border-top:1px solid var(--c-border,#e3e6ea)">
        <h2 style="font-size:16px;font-weight:700;color:var(--c-text,#111827);margin:0 0 4px">Límites de uso del bot</h2>
        <p style="font-size:12.5px;color:var(--c-sub,#6b7280);margin:0 0 20px;line-height:1.6">Controla el consumo de tokens de IA para evitar gastos innecesarios.</p>
        <div style="max-width:600px;display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:end">
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:5px">
                    Máx. mensajes de bot por sesión
                </label>
                <input type="number" wire:model="maxMsgPerSession" min="5" max="200" class="ak-input" style="width:100%;box-sizing:border-box">
                <p style="font-size:11px;color:var(--c-sub,#6b7280);margin:4px 0 0">Mín. 5 · Máx. 200</p>
            </div>
            <div>
                <label style="font-size:11px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:5px">
                    Máx. sesiones con bot por día
                </label>
                <input type="number" wire:model="maxSessionsPerDay" min="10" max="10000" class="ak-input" style="width:100%;box-sizing:border-box">
                <p style="font-size:11px;color:var(--c-sub,#6b7280);margin:4px 0 0">Mín. 10 · Máx. 10,000</p>
            </div>
            <div>
                <button class="ak-btn ak-btn-primary" wire:click="saveLimits" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveLimits">Guardar límites</span>
                    <span wire:loading wire:target="saveLimits">Guardando...</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endif

</x-filament-panels::page>
