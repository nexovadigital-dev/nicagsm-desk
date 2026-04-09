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
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:6px;letter-spacing:-.02em">Claves API</h1>
    <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:0 0 28px;max-width:600px;line-height:1.6">
        Configura claves API propias para que tu organización use sus propios proveedores de IA en lugar de las claves globales de la plataforma.
    </p>
</div>

{{-- ── Claves API de IA (solo owner/admin) ── --}}
@if($this->isOrgAdmin())
<div style="margin-top:40px;border-top:1px solid var(--c-border,#e3e6ea);padding-top:28px">

    <div style="margin-bottom:20px">
        <h2 style="font-size:16px;font-weight:700;color:var(--c-text,#111827);margin:0 0 4px">Claves API de Inteligencia Artificial</h2>
        <p style="font-size:12.5px;color:var(--c-sub,#6b7280);margin:0 0 8px;line-height:1.6">
            Configura tus propias claves API para el bot de IA. Puedes agregar hasta 3 claves de Groq — el sistema las rotará automáticamente si una falla.
        </p>
        <div style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:99px;background:#f0fdf4;border:1px solid #bbf7d0;font-size:11.5px;font-weight:600;color:#15803d;margin-bottom:20px">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Plan Partner — Claves propias activas
        </div>

        <div style="max-width:640px;display:flex;flex-direction:column;gap:14px">

            {{-- Groq Keys 1-3 --}}
            <div style="background:var(--c-bg,#f9fafb);border:1px solid var(--c-border,#e3e6ea);border-radius:10px;padding:18px 20px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                    <div style="width:28px;height:28px;border-radius:7px;background:#F55036;display:flex;align-items:center;justify-content:center">
                        <span style="font-size:10px;font-weight:800;color:#fff">G</span>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:var(--c-text,#111827)">Groq</div>
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">llama-3.3-70b-versatile — hasta 3 claves en rotación</div>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px">
                    <div>
                        <label style="font-size:10px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px">Clave 1 (principal)</label>
                        <input type="password" wire:model="orgGroqKey" class="ak-input" placeholder="gsk_... (dejar vacío para no cambiar)" style="width:100%;box-sizing:border-box">
                    </div>
                    <div>
                        <label style="font-size:10px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px">Clave 2 (fallback)</label>
                        <input type="password" wire:model="orgGroqKey2" class="ak-input" placeholder="gsk_... (opcional)" style="width:100%;box-sizing:border-box">
                    </div>
                    <div>
                        <label style="font-size:10px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px">Clave 3 (fallback)</label>
                        <input type="password" wire:model="orgGroqKey3" class="ak-input" placeholder="gsk_... (opcional)" style="width:100%;box-sizing:border-box">
                    </div>
                </div>
            </div>

            {{-- Gemini Key --}}
            <div style="background:var(--c-bg,#f9fafb);border:1px solid var(--c-border,#e3e6ea);border-radius:10px;padding:18px 20px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                    <div style="width:28px;height:28px;border-radius:7px;background:#4285F4;display:flex;align-items:center;justify-content:center">
                        <span style="font-size:10px;font-weight:800;color:#fff">G</span>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:var(--c-text,#111827)">Google Gemini</div>
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">gemini-1.5-flash — fallback si Groq no está disponible</div>
                    </div>
                </div>
                <div>
                    <label style="font-size:10px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px">Google AI API Key</label>
                    <input type="password" wire:model="orgGeminiKey" class="ak-input" placeholder="AIza... (dejar vacío para no cambiar)" style="width:100%;box-sizing:border-box">
                </div>
            </div>

            <div>
                <button class="ak-btn ak-btn-primary" wire:click="saveOrgKeys" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveOrgKeys">Guardar claves API</span>
                    <span wire:loading wire:target="saveOrgKeys">Guardando...</span>
                </button>
                <p style="font-size:11px;color:var(--c-sub,#6b7280);margin-top:6px">Las claves se almacenan cifradas. Deja los campos vacíos para mantener las claves actuales.</p>
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
