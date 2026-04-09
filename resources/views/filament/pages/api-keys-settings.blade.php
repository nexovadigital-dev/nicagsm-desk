<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }
.ak-wrap { padding: 28px 36px 64px; max-width: 760px; }

.ak-section { margin-bottom: 32px; }
.ak-section-title {
    font-size: 14px; font-weight: 700; color: var(--c-text,#111827);
    margin: 0 0 2px; letter-spacing: -.01em;
}
.ak-section-sub {
    font-size: 12px; color: var(--c-sub,#6b7280); margin: 0 0 16px; line-height: 1.55;
}

.ak-card {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 10px;
    overflow: hidden;
}
.ak-card-head {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--c-border,#e3e6ea);
    background: var(--c-bg,#f9fafb);
}
.ak-provider-icon {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; color: #fff; flex-shrink: 0;
}
.ak-provider-name { font-size: 13px; font-weight: 700; color: var(--c-text,#111); }
.ak-provider-model { font-size: 11px; color: var(--c-sub,#6b7280); margin-top: 1px; }
.ak-card-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 12px; }

.ak-field { display: flex; flex-direction: column; gap: 5px; }
.ak-field-head { display: flex; align-items: center; justify-content: space-between; }
.ak-label {
    font-size: 10.5px; font-weight: 700; color: var(--c-sub,#6b7280);
    text-transform: uppercase; letter-spacing: .05em;
}
.ak-set-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 99px;
    background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;
}
.ak-unset-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 99px;
    background: var(--c-bg,#f5f6f8); color: var(--c-sub,#6b7280);
    border: 1px solid var(--c-border,#e3e6ea);
}
.ak-input-wrap { position: relative; }
.ak-input {
    width: 100%; box-sizing: border-box;
    background: var(--c-bg,#f5f6f8);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 7px; color: var(--c-text,#111);
    font-size: 12.5px; font-family: monospace;
    padding: 8px 36px 8px 11px; outline: none;
    transition: border-color .12s;
}
.ak-input:focus { border-color: #16a34a; background: #fff; }
.ak-input::placeholder { color: #9ca3af; font-size: 11.5px; }
.ak-eye {
    position: absolute; right: 9px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--c-sub,#9ca3af); padding: 0; display: flex;
    transition: color .1s;
}
.ak-eye:hover { color: var(--c-text,#374151); }

.ak-divider { height: 1px; background: var(--c-border,#e3e6ea); margin: 4px 0; }

.ak-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 7px; font-size: 12.5px;
    font-weight: 500; cursor: pointer; font-family: inherit;
    transition: background .1s; border: 1px solid transparent;
}
.ak-btn-primary { background: #111827; color: #f9fafb; }
.ak-btn-primary:hover { background: #1f2937; }
.ak-btn-primary:disabled { opacity: .5; cursor: default; }

.ak-hint { font-size: 11px; color: var(--c-sub,#9ca3af); margin: 0; line-height: 1.5; }

.ak-limits-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 14px; align-items: end;
}
@media (max-width: 560px) { .ak-limits-grid { grid-template-columns: 1fr; } }

.ak-partner-tag {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 99px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    font-size: 11px; font-weight: 600; color: #15803d;
    margin-bottom: 16px;
}
</style>

<div class="ak-wrap">

    {{-- Page header --}}
    <div style="margin-bottom:28px">
        <h1 style="font-size:21px;font-weight:700;color:var(--c-text,#111827);margin:0 0 5px;letter-spacing:-.02em">
            Inteligencia Artificial
        </h1>
        <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:0;line-height:1.6">
            Configura las claves API del bot de IA y los límites de uso.
        </p>
    </div>

    @if($this->isOrgAdmin())

    {{-- Partner badge --}}
    <div class="ak-partner-tag">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        Plan Partner — Claves propias activas
    </div>

    {{-- ── Groq ── --}}
    <div class="ak-section">
        <p class="ak-section-title">Groq</p>
        <p class="ak-section-sub">
            Proveedor principal de IA. Puedes agregar hasta 3 claves — si la primera falla, el sistema usa la siguiente automáticamente.
        </p>
        <div class="ak-card">
            <div class="ak-card-head">
                <div class="ak-provider-icon" style="background:#F55036">G</div>
                <div>
                    <div class="ak-provider-name">Groq</div>
                    <div class="ak-provider-model">llama-3.3-70b-versatile · rotación automática</div>
                </div>
            </div>
            <div class="ak-card-body">
                {{-- Key 1 --}}
                <div class="ak-field" x-data="{show:false}">
                    <div class="ak-field-head">
                        <span class="ak-label">Clave 1 — Principal</span>
                        @if($groqKey1Set)
                            <span class="ak-set-badge">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                                Configurada
                            </span>
                        @else
                            <span class="ak-unset-badge">Sin configurar</span>
                        @endif
                    </div>
                    <div class="ak-input-wrap">
                        <input :type="show?'text':'password'" wire:model="orgGroqKey"
                            class="ak-input" placeholder="gsk_... (dejar vacío para mantener actual)">
                        <button type="button" class="ak-eye" @click="show=!show" tabindex="-1">
                            <svg x-show="!show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg x-show="show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="ak-divider"></div>

                {{-- Key 2 --}}
                <div class="ak-field" x-data="{show:false}">
                    <div class="ak-field-head">
                        <span class="ak-label">Clave 2 — Fallback</span>
                        @if($groqKey2Set)
                            <span class="ak-set-badge">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                                Configurada
                            </span>
                        @else
                            <span class="ak-unset-badge">Sin configurar</span>
                        @endif
                    </div>
                    <div class="ak-input-wrap">
                        <input :type="show?'text':'password'" wire:model="orgGroqKey2"
                            class="ak-input" placeholder="gsk_... (opcional)">
                        <button type="button" class="ak-eye" @click="show=!show" tabindex="-1">
                            <svg x-show="!show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg x-show="show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="ak-divider"></div>

                {{-- Key 3 --}}
                <div class="ak-field" x-data="{show:false}">
                    <div class="ak-field-head">
                        <span class="ak-label">Clave 3 — Fallback</span>
                        @if($groqKey3Set)
                            <span class="ak-set-badge">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                                Configurada
                            </span>
                        @else
                            <span class="ak-unset-badge">Sin configurar</span>
                        @endif
                    </div>
                    <div class="ak-input-wrap">
                        <input :type="show?'text':'password'" wire:model="orgGroqKey3"
                            class="ak-input" placeholder="gsk_... (opcional)">
                        <button type="button" class="ak-eye" @click="show=!show" tabindex="-1">
                            <svg x-show="!show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg x-show="show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Google Gemini ── --}}
    <div class="ak-section">
        <p class="ak-section-title">Google Gemini</p>
        <p class="ak-section-sub">Fallback secundario si Groq no está disponible.</p>
        <div class="ak-card">
            <div class="ak-card-head">
                <div class="ak-provider-icon" style="background:#4285F4">G</div>
                <div>
                    <div class="ak-provider-name">Google Gemini</div>
                    <div class="ak-provider-model">gemini-1.5-flash · se usa si Groq falla</div>
                </div>
            </div>
            <div class="ak-card-body">
                <div class="ak-field" x-data="{show:false}">
                    <div class="ak-field-head">
                        <span class="ak-label">Google AI API Key</span>
                        @if($geminiKeySet)
                            <span class="ak-set-badge">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                                Configurada
                            </span>
                        @else
                            <span class="ak-unset-badge">Sin configurar</span>
                        @endif
                    </div>
                    <div class="ak-input-wrap">
                        <input :type="show?'text':'password'" wire:model="orgGeminiKey"
                            class="ak-input" placeholder="AIza... (dejar vacío para mantener actual)">
                        <button type="button" class="ak-eye" @click="show=!show" tabindex="-1">
                            <svg x-show="!show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg x-show="show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Botón guardar claves ── --}}
    <div style="margin-bottom:36px">
        <button class="ak-btn ak-btn-primary" wire:click="saveOrgKeys" wire:loading.attr="disabled">
            <svg wire:loading.remove wire:target="saveOrgKeys" width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span wire:loading.remove wire:target="saveOrgKeys">Guardar claves</span>
            <span wire:loading wire:target="saveOrgKeys">Guardando...</span>
        </button>
        <p class="ak-hint" style="margin-top:7px">Las claves se almacenan cifradas. Deja los campos vacíos para mantener las claves actuales.</p>
    </div>

    {{-- ── Límites de uso ── --}}
    <div style="border-top:1px solid var(--c-border,#e3e6ea);padding-top:28px">
        <p class="ak-section-title">Límites del bot</p>
        <p class="ak-section-sub">Controla el consumo de tokens para evitar gastos innecesarios.</p>
        <div class="ak-limits-grid" style="margin-bottom:14px">
            <div class="ak-field">
                <label class="ak-label">Máx. mensajes por sesión</label>
                <input type="number" wire:model="maxMsgPerSession" min="5" max="200" class="ak-input" style="padding-right:11px">
                <p class="ak-hint">Mín. 5 · Máx. 200</p>
            </div>
            <div class="ak-field">
                <label class="ak-label">Máx. sesiones con bot por día</label>
                <input type="number" wire:model="maxSessionsPerDay" min="10" max="10000" class="ak-input" style="padding-right:11px">
                <p class="ak-hint">Mín. 10 · Máx. 10,000</p>
            </div>
        </div>
        <button class="ak-btn ak-btn-primary" wire:click="saveLimits" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="saveLimits">Guardar límites</span>
            <span wire:loading wire:target="saveLimits">Guardando...</span>
        </button>
    </div>

    @endif

</div>
</x-filament-panels::page>
