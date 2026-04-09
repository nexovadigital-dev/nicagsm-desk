<x-filament-panels::page>
<style>
.wc-page { max-width: 1160px; margin: 0 auto; padding: 20px 0 40px; display: grid; grid-template-columns: 1fr; gap: 18px; }
@media (min-width: 1080px) {
    .wc-page { grid-template-columns: 1fr 320px; align-items: start; }
    .wc-left-col  { display: flex; flex-direction: column; gap: 18px; }
    .wc-right-col { position: sticky; top: 20px; display: flex; flex-direction: column; gap: 18px; }
}
.wc-left-col, .wc-right-col { display: flex; flex-direction: column; gap: 18px; }

.wc-card {
    background: var(--c-surface, #fff);
    border: 1px solid var(--c-border, #e3e6ea);
    border-radius: 12px;
    padding: 22px 26px;
}
.wc-card-title {
    font-size: 13px; font-weight: 700; color: var(--c-text, #111);
    margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
}
.wc-card-title svg { color: #64748b; }

.wc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

.wc-field { display: flex; flex-direction: column; gap: 4px; }
.wc-label { font-size: 11px; font-weight: 600; color: var(--c-sub, #6b7280); text-transform: uppercase; letter-spacing: .05em; }

.wc-input,
.wc-select {
    background: var(--c-bg, #f5f6f8);
    border: 1px solid var(--c-border, #e3e6ea);
    border-radius: 7px;
    color: var(--c-text, #111);
    font-size: 13px;
    padding: 8px 11px;
    outline: none;
    width: 100%;
    font-family: inherit;
    transition: border-color .12s;
    box-sizing: border-box;
}
.wc-input:focus, .wc-select:focus { border-color: #1e293b; }

/* Toggle switch */
.wc-toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 11px 0; border-bottom: 1px solid var(--c-border, #e3e6ea); }
.wc-toggle-row:last-child { border-bottom: none; }
.wc-toggle-label { font-size: 13px; font-weight: 500; color: var(--c-text, #111); }
.wc-toggle-sub   { font-size: 11.5px; color: var(--c-sub, #6b7280); margin-top: 2px; }

.wc-toggle { position: relative; display: inline-block; width: 40px; height: 22px; flex-shrink: 0; }
.wc-toggle input { opacity: 0; width: 0; height: 0; }
.wc-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: var(--c-border, #e3e6ea); border-radius: 99px; transition: background .2s;
}
.wc-slider:before {
    content:''; position: absolute;
    height: 16px; width: 16px; left: 3px; bottom: 3px;
    background: white; border-radius: 50%; transition: transform .2s;
}
.wc-toggle input:checked + .wc-slider { background: #22c55e; }
.wc-toggle input:checked + .wc-slider:before { transform: translateX(18px); }

/* Buttons */
.wc-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 7px; font-size: 13px;
    font-weight: 500; cursor: pointer; border: 1px solid transparent;
    font-family: inherit; transition: background .1s;
}
.wc-btn-primary { background: #1e293b; color:#f8fafc; border-color: #1e293b; }
.wc-btn-primary:hover { background: #6d28d9; }
.wc-btn-ghost { background: transparent; color: var(--c-sub, #6b7280); border-color: var(--c-border, #e3e6ea); }
.wc-btn-ghost:hover { background: var(--c-surf2, #f0f2f5); }
.wc-btn-danger { background: transparent; color: #dc2626; border-color: rgba(220,38,38,.25); font-size: 12px; padding: 6px 12px; }
.wc-btn-danger:hover { background: rgba(220,38,38,.06); }

.wc-alert { padding: 10px 14px; border-radius: 7px; font-size: 12px; font-weight: 500; margin-top: 12px; }
.wc-alert-success { background: rgba(5,150,105,.07); border:1px solid rgba(5,150,105,.2); color: #059669; }
.wc-alert-error   { background: rgba(220,38,38,.07);  border:1px solid rgba(220,38,38,.2);  color: #dc2626; }

.wc-color-row { display: flex; align-items: center; gap: 10px; }
.wc-color-picker { width: 40px; height: 38px; border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px; cursor: pointer; padding: 3px; background: none; }

/* Segment control */
.wc-seg { display: flex; background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 8px; padding: 3px; gap: 2px; }
.wc-seg-btn { flex: 1; padding: 6px 10px; border-radius: 6px; font-size: 12px; font-weight: 500; border: none; background: transparent; color: var(--c-sub,#6b7280); cursor: pointer; font-family: inherit; transition: background .12s, color .12s; }
.wc-seg-btn.active { background: var(--c-surface,#fff); color: var(--c-text,#111); box-shadow: 0 1px 3px rgba(0,0,0,.1); }

/* Widget preview */
.wc-preview { background: var(--c-surf2, #f0f2f5); border: 1px solid var(--c-border, #e3e6ea);
    border-radius: 12px; padding: 20px; display: flex; justify-content: center; margin-top: 18px; }
.wc-widget-mock { width: 300px; background: #fff; border-radius: 14px;
    box-shadow: 0 8px 32px rgba(0,0,0,.12); overflow: hidden; font-family: 'Inter', sans-serif; }
.wc-widget-header { padding: 14px 16px; color: #fff; display: flex; align-items: center; gap: 10px; }
.wc-widget-avatar { width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; }
.wc-widget-name { font-size: 13px; font-weight: 600; }
.wc-widget-sub  { font-size: 11px; opacity: .8; }
.wc-widget-body { padding: 14px 16px; }
.wc-widget-bubble { background: #f1f5f9; border-radius: 12px 12px 12px 4px;
    padding: 9px 13px; font-size: 12px; color: #374151; display: inline-block; max-width: 80%; }

/* ── Pre-chat field builder ── */
.pcb-field {
    background: var(--c-bg, #f5f6f8);
    border: 1px solid var(--c-border, #e3e6ea);
    border-radius: 10px;
    padding: 0;
    overflow: hidden;
    transition: border-color .15s;
}
.pcb-field:hover { border-color: #1e293b; }
.pcb-field-header {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 14px; cursor: pointer;
    border-bottom: 1px solid transparent; transition: border-color .15s;
}
.pcb-field-header.open { border-bottom-color: var(--c-border, #e3e6ea); }
.pcb-field-drag { color: var(--c-sub, #9ca3af); cursor: grab; display: flex; }
.pcb-field-type-badge {
    font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 99px;
    text-transform: uppercase; letter-spacing: .04em; flex-shrink: 0;
}
.pcb-field-name { font-size: 13px; font-weight: 500; color: var(--c-text, #111); flex: 1; min-width: 0; }
.pcb-field-req-badge { font-size: 10px; font-weight: 600; color: #dc2626; background: rgba(220,38,38,.08); border: 1px solid rgba(220,38,38,.18); padding: 2px 7px; border-radius: 99px; }
.pcb-field-opt-badge { font-size: 10px; font-weight: 600; color: var(--c-sub, #6b7280); background: var(--c-surf2, #f0f2f5); border: 1px solid var(--c-border, #e3e6ea); padding: 2px 7px; border-radius: 99px; }
.pcb-field-body { padding: 14px; display: flex; flex-direction: column; gap: 12px; }
.pcb-body-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.pcb-body-actions { display: flex; align-items: center; justify-content: space-between; padding-top: 4px; }
.pcb-icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; border: none; cursor: pointer; background: transparent; color: var(--c-sub, #6b7280); transition: background .12s, color .12s; }
.pcb-icon-btn:hover { background: var(--c-surf2, #f0f2f5); color: var(--c-text, #111); }
.pcb-icon-btn.danger:hover { background: rgba(220,38,38,.08); color: #dc2626; }
.pcb-chevron { transition: transform .2s; color: var(--c-sub, #9ca3af); display: flex; }
.type-text    { background: #f1f5f9; color: #64748b; }
.type-email   { background: rgba(59,130,246,.1); color: #3b82f6; }
.type-tel     { background: rgba(16,185,129,.1); color: #10b981; }
.type-select  { background: rgba(245,158,11,.1); color: #f59e0b; }
.pcb-add-btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 11px; border-radius: 9px; border: 2px dashed var(--c-border, #e3e6ea); background: transparent; color: var(--c-sub, #6b7280); font-size: 13px; font-weight: 500; cursor: pointer; font-family: inherit; transition: border-color .15s, color .15s, background .15s; }
.pcb-add-btn:hover { border-color: #16a34a; color: #16a34a; background: rgba(34,197,94,.04); }
.pcb-empty { text-align: center; padding: 28px 20px; color: var(--c-sub, #9ca3af); font-size: 13px; }

/* FAQ & Social items */
.wc-item-row { display: flex; gap: 10px; align-items: flex-start; padding: 12px; background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 9px; }
.wc-item-fields { flex: 1; display: flex; flex-direction: column; gap: 8px; }

/* Working hours table */
.wc-hours-table { width: 100%; border-collapse: collapse; }
.wc-hours-table th { font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .04em; padding: 4px 8px; text-align: left; }
.wc-hours-table td { padding: 6px 8px; vertical-align: middle; }
.wc-hours-table tr { border-bottom: 1px solid var(--c-border,#e3e6ea); }
.wc-hours-table tr:last-child { border-bottom: none; }

@keyframes spin { to { transform: rotate(360deg); } }
.fi-page-header, .fi-breadcrumbs { display: none !important; }
</style>

<div class="wc-page">
<div class="wc-left-col">

    {{-- ── Identidad del bot ── --}}
    <div class="wc-card">
        <div class="wc-card-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Identidad del bot
        </div>

        <div class="wc-grid">
            <div class="wc-field">
                <label class="wc-label">Nombre del bot</label>
                <input type="text" class="wc-input" wire:model.live="botName" placeholder="Nexova IA">
            </div>
            <div class="wc-field">
                <label class="wc-label">Color de acento</label>
                <div class="wc-color-row">
                    <input type="color" class="wc-color-picker" wire:model.live="accentColor">
                    <input type="text" class="wc-input" wire:model.live="accentColor" placeholder="#3b82f6" style="font-family:monospace">
                </div>
            </div>
            <div class="wc-field" style="grid-column:1/-1">
                <label class="wc-label">Mensaje de bienvenida</label>
                <input type="text" class="wc-input" wire:model.live="welcomeMessage" placeholder="Hola, ¿en qué te puedo ayudar?">
            </div>
        </div>

    </div>

    {{-- ── Apariencia y posición ── --}}
    <div class="wc-card">
        <div class="wc-card-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
            </svg>
            Apariencia y posición
        </div>

        <div class="wc-grid">
            <div class="wc-field">
                <label class="wc-label">Posición del widget</label>
                <div class="wc-seg">
                    <button type="button" class="wc-seg-btn {{ $widgetPosition === 'left' ? 'active' : '' }}"
                        wire:click="$set('widgetPosition','left')">← Izquierda</button>
                    <button type="button" class="wc-seg-btn {{ $widgetPosition === 'right' ? 'active' : '' }}"
                        wire:click="$set('widgetPosition','right')">Derecha →</button>
                </div>
            </div>

            <div class="wc-field">
                <label class="wc-label">Tamaño del widget</label>
                <div class="wc-seg">
                    <button type="button" class="wc-seg-btn {{ $widgetSize === 'sm' ? 'active' : '' }}"
                        wire:click="$set('widgetSize','sm')">S</button>
                    <button type="button" class="wc-seg-btn {{ $widgetSize === 'md' ? 'active' : '' }}"
                        wire:click="$set('widgetSize','md')">M</button>
                    <button type="button" class="wc-seg-btn {{ $widgetSize === 'lg' ? 'active' : '' }}"
                        wire:click="$set('widgetSize','lg')">L</button>
                    <button type="button" class="wc-seg-btn {{ $widgetSize === 'xl' ? 'active' : '' }}"
                        wire:click="$set('widgetSize','xl')">XL</button>
                </div>
            </div>

            <div class="wc-field">
                <label class="wc-label">Efecto de atención (FAB)</label>
                <select class="wc-select" wire:model="attentionEffect">
                    <option value="none">Sin efecto</option>
                    <option value="bounce">Rebotar</option>
                    <option value="pulse">Pulsar</option>
                    <option value="spin">Girar</option>
                    <option value="shake">Sacudir</option>
                </select>
            </div>

            <div class="wc-field">
                <label class="wc-label">Pantalla inicial al abrir</label>
                <div class="wc-seg">
                    <button type="button" class="wc-seg-btn {{ $defaultScreen === 'home' ? 'active' : '' }}"
                        wire:click="$set('defaultScreen','home')">Inicio</button>
                    <button type="button" class="wc-seg-btn {{ $defaultScreen === 'chat' ? 'active' : '' }}"
                        wire:click="$set('defaultScreen','chat')">Chat</button>
                </div>
            </div>

            <div class="wc-field">
                <label class="wc-label">Mostrar en dispositivos</label>
                <div class="wc-seg">
                    <button type="button" class="wc-seg-btn {{ $showOn === 'both' ? 'active' : '' }}"
                        wire:click="$set('showOn','both')">Todos</button>
                    <button type="button" class="wc-seg-btn {{ $showOn === 'desktop' ? 'active' : '' }}"
                        wire:click="$set('showOn','desktop')">Escritorio</button>
                    <button type="button" class="wc-seg-btn {{ $showOn === 'mobile' ? 'active' : '' }}"
                        wire:click="$set('showOn','mobile')">Móvil</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Mensaje flotante (preview bubble) ── --}}
    <div class="wc-card">
        <div class="wc-card-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Mensaje flotante
        </div>

        <div class="wc-toggle-row" style="border:none;padding-bottom:0">
            <div>
                <div class="wc-toggle-label">Mostrar burbuja sobre el botón</div>
                <div class="wc-toggle-sub">Aparece un mensaje encima del FAB para captar atención</div>
            </div>
            <label class="wc-toggle">
                <input type="checkbox" wire:model.live="previewMessageEnabled">
                <span class="wc-slider"></span>
            </label>
        </div>

        @if($previewMessageEnabled)
        <div class="wc-field" style="margin-top:14px">
            <label class="wc-label">Texto del mensaje flotante</label>
            <input type="text" class="wc-input" wire:model="previewMessage"
                placeholder="¿Necesitas ayuda? ¡Estamos aquí! 👋">
        </div>
        @endif
    </div>

    {{-- ── Preguntas frecuentes (FAQ) ── --}}
    <div class="wc-card">
        <div class="wc-card-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Preguntas frecuentes (FAQ)
        </div>

        <div class="wc-toggle-row" style="border:none;padding-bottom:0">
            <div>
                <div class="wc-toggle-label">Mostrar FAQs en el widget</div>
                <div class="wc-toggle-sub">El usuario puede hacer clic en preguntas frecuentes antes de escribir</div>
            </div>
            <label class="wc-toggle">
                <input type="checkbox" wire:model.live="faqEnabled">
                <span class="wc-slider"></span>
            </label>
        </div>

        @if($faqEnabled)
        <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px">
            @forelse($faqItems as $i => $faq)
            <div class="wc-item-row">
                <div class="wc-item-fields">
                    <input type="text" class="wc-input" wire:model.live="faqItems.{{ $i }}.question"
                        placeholder="Pregunta frecuente...">
                    <textarea class="wc-input" wire:model="faqItems.{{ $i }}.answer"
                        placeholder="Respuesta automática..." rows="2" style="resize:vertical"></textarea>
                </div>
                <button type="button" class="pcb-icon-btn danger" wire:click="removeFaq({{ $i }})"
                    wire:confirm="¿Eliminar esta pregunta?">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            @empty
            <div class="pcb-empty">Sin preguntas frecuentes. Agrega una para que los usuarios puedan hacer clic.</div>
            @endforelse

            <button type="button" class="pcb-add-btn" wire:click="addFaq">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar pregunta
            </button>
        </div>
        @endif
    </div>

    {{-- ── Canales sociales ── --}}
    <div class="wc-card">
        <div class="wc-card-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            Canales de contacto rápido
        </div>

        <p style="font-size:12px;color:var(--c-sub);margin-bottom:14px;line-height:1.5">
            Agrega enlaces a WhatsApp, Telegram u otros canales que aparecen en la pantalla de inicio del widget.
        </p>

        <div style="display:flex;flex-direction:column;gap:8px">
            @forelse($socialChannels as $i => $ch)
            <div class="wc-item-row">
                <div class="wc-item-fields">
                    <div style="display:grid;grid-template-columns:140px 1fr 1fr;gap:8px">
                        <select class="wc-select" wire:model.live="socialChannels.{{ $i }}.type">
                            <option value="whatsapp">WhatsApp</option>
                            <option value="telegram">Telegram</option>
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                            <option value="email">Email</option>
                            <option value="phone">Teléfono</option>
                            <option value="other">Otro</option>
                        </select>
                        <input type="text" class="wc-input" wire:model="socialChannels.{{ $i }}.label"
                            placeholder="Etiqueta (ej: Soporte WA)">
                        <input type="text" class="wc-input" wire:model="socialChannels.{{ $i }}.url"
                            placeholder="URL o número (ej: https://wa.me/...)">
                    </div>
                </div>
                <button type="button" class="pcb-icon-btn danger" wire:click="removeSocialChannel({{ $i }})"
                    wire:confirm="¿Eliminar este canal?">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            @empty
            <div class="pcb-empty">Sin canales de contacto. Agrega WhatsApp, Telegram u otros.</div>
            @endforelse

            <button type="button" class="pcb-add-btn" wire:click="addSocialChannel">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar canal
            </button>
        </div>
    </div>

    {{-- ── Horario de atención ── --}}
    <div class="wc-card">
        <div class="wc-card-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Horario de atención
        </div>

        <div class="wc-toggle-row" style="border:none;padding-bottom:0">
            <div>
                <div class="wc-toggle-label">Activar horario de atención</div>
                <div class="wc-toggle-sub">Fuera del horario, el widget mostrará un mensaje de no disponibilidad</div>
            </div>
            <label class="wc-toggle">
                <input type="checkbox" wire:model.live="workingHoursEnabled">
                <span class="wc-slider"></span>
            </label>
        </div>

        @if($workingHoursEnabled)
        <div style="margin-top:16px">
            <table class="wc-hours-table">
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Activo</th>
                        <th>Desde</th>
                        <th>Hasta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['mon','tue','wed','thu','fri','sat','sun'] as $day)
                    <tr>
                        <td style="font-size:13px;font-weight:500;color:var(--c-text)">
                            {{ $workingHours[$day]['label'] ?? $day }}
                        </td>
                        <td>
                            <label class="wc-toggle" style="width:34px;height:19px">
                                <input type="checkbox" wire:model.live="workingHours.{{ $day }}.enabled">
                                <span class="wc-slider"></span>
                            </label>
                        </td>
                        <td>
                            <input type="time" class="wc-input" style="width:110px"
                                wire:model="workingHours.{{ $day }}.from"
                                {{ !($workingHours[$day]['enabled'] ?? false) ? 'disabled' : '' }}>
                        </td>
                        <td>
                            <input type="time" class="wc-input" style="width:110px"
                                wire:model="workingHours.{{ $day }}.to"
                                {{ !($workingHours[$day]['enabled'] ?? false) ? 'disabled' : '' }}>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="wc-field" style="margin-top:14px">
                <label class="wc-label">Mensaje fuera de horario</label>
                <input type="text" class="wc-input" wire:model="offlineMessage"
                    placeholder="Estamos fuera de horario. Te responderemos pronto.">
            </div>
        </div>
        @endif
    </div>

    {{-- ── Comportamiento ── --}}
    <div class="wc-card">
        <div class="wc-card-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Comportamiento
        </div>

        <div class="wc-toggle-row">
            <div>
                <div class="wc-toggle-label">Mostrar marca Nexova Digital Solutions</div>
                <div class="wc-toggle-sub">Muestra "Powered by Nexova Digital Solutions" en el pie del widget</div>
            </div>
            <label class="wc-toggle">
                <input type="checkbox" wire:model.live="showBranding">
                <span class="wc-slider"></span>
            </label>
        </div>

        <div class="wc-toggle-row">
            <div>
                <div class="wc-toggle-label">Sonidos de notificación</div>
                <div class="wc-toggle-sub">Reproduce sonido al enviar y recibir mensajes en el widget</div>
            </div>
            <label class="wc-toggle">
                <input type="checkbox" wire:model.live="soundEnabled">
                <span class="wc-slider"></span>
            </label>
        </div>

        <div class="wc-toggle-row">
            <div>
                <div class="wc-toggle-label">Pedir calificación al cerrar</div>
                <div class="wc-toggle-sub">El usuario puede valorar la conversación de 1 a 5 estrellas</div>
            </div>
            <label class="wc-toggle">
                <input type="checkbox" wire:model.live="requireRating">
                <span class="wc-slider"></span>
            </label>
        </div>

        @if($requireRating)
        <div class="wc-field" style="margin-top:14px">
            <label class="wc-label">Mensaje de calificación</label>
            <input type="text" class="wc-input" wire:model="ratingMessage" placeholder="¿Cómo fue tu experiencia?">
        </div>
        @endif
    </div>

    {{-- ── Formulario pre-chat ── --}}
    <div class="wc-card">
        <div class="wc-card-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Formulario pre-chat
        </div>

        <div class="wc-toggle-row" style="border:none;padding-bottom:0">
            <div>
                <div class="wc-toggle-label">Solicitar datos antes del chat</div>
                <div class="wc-toggle-sub">El visitante completa su información antes de iniciar</div>
            </div>
            <label class="wc-toggle">
                <input type="checkbox" wire:model.live="preChatEnabled">
                <span class="wc-slider"></span>
            </label>
        </div>

        @if($preChatEnabled)
        <div style="margin-top:20px;display:flex;flex-direction:column;gap:8px">
            @forelse($preChatFields as $i => $field)
            @php
                $type    = $field['type'] ?? 'text';
                $label   = $field['label'] ?? 'Campo';
                $enabled = $field['enabled'] ?? true;
            @endphp
            <div class="pcb-field" x-data="{ open: false }" style="{{ !$enabled ? 'opacity:.55' : '' }}">
                <div class="pcb-field-header" :class="open ? 'open' : ''" @click="open = !open">
                    <div class="pcb-field-drag">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                        </svg>
                    </div>
                    <span class="pcb-field-type-badge type-{{ $type }}">{{ $type }}</span>
                    <span class="pcb-field-name">{{ $label ?: 'Sin nombre' }}</span>
                    @if($field['required'] ?? false)
                        <span class="pcb-field-req-badge">Requerido</span>
                    @else
                        <span class="pcb-field-opt-badge">Opcional</span>
                    @endif
                    <div style="display:flex;gap:2px;align-items:center" @click.stop>
                        <button type="button" class="pcb-icon-btn" wire:click="moveUp({{ $i }})" title="Subir" {{ $i === 0 ? 'disabled' : '' }}>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button type="button" class="pcb-icon-btn" wire:click="moveDown({{ $i }})" title="Bajar" {{ $i === count($preChatFields) - 1 ? 'disabled' : '' }}>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <div class="pcb-chevron" :style="open ? 'transform:rotate(180deg)' : ''">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div class="pcb-field-body" x-show="open" x-collapse>
                    <div class="pcb-body-grid">
                        <div class="wc-field">
                            <label class="wc-label">Etiqueta</label>
                            <input type="text" class="wc-input" wire:model.live="preChatFields.{{ $i }}.label" placeholder="Ej: Nombre completo">
                        </div>
                        <div class="wc-field">
                            <label class="wc-label">Tipo de campo</label>
                            <select class="wc-select" wire:model.live="preChatFields.{{ $i }}.type">
                                <option value="text">Texto</option>
                                <option value="email">Email</option>
                                <option value="tel">Teléfono</option>
                                <option value="select">Selección</option>
                            </select>
                        </div>
                    </div>
                    <div class="pcb-body-actions">
                        <div style="display:flex;align-items:center;gap:16px">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12.5px;color:var(--c-text,#111);font-weight:500">
                                <label class="wc-toggle" style="width:34px;height:19px">
                                    <input type="checkbox" wire:model.live="preChatFields.{{ $i }}.required">
                                    <span class="wc-slider"></span>
                                </label>
                                Requerido
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12.5px;color:var(--c-sub,#6b7280)">
                                <label class="wc-toggle" style="width:34px;height:19px">
                                    <input type="checkbox" wire:model.live="preChatFields.{{ $i }}.enabled">
                                    <span class="wc-slider"></span>
                                </label>
                                Activo
                            </label>
                        </div>
                        <button type="button" class="pcb-icon-btn danger" wire:click="removeField({{ $i }})" wire:confirm="¿Eliminar este campo?">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="pcb-empty">No hay campos configurados. Haz clic en "Agregar campo" para empezar.</div>
            @endforelse

            <button type="button" class="pcb-add-btn" wire:click="addField">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Agregar campo
            </button>
        </div>
        @endif
    </div>

</div>{{-- /.wc-left-col --}}

<div class="wc-right-col">

    {{-- ── Preview ── --}}
    <div class="wc-card" style="padding:18px">
        <div class="wc-card-title" style="margin-bottom:14px">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Vista previa
        </div>
        <div style="background:var(--c-surf2,#f0f2f5);border-radius:10px;padding:16px;display:flex;justify-content:center">
            <div class="wc-widget-mock" style="width:260px">
                <div class="wc-widget-header" style="background:{{ $accentColor }}">
                    <div class="wc-widget-avatar">{{ strtoupper(substr($botName ?: 'N', 0, 1)) }}</div>
                    <div>
                        <div class="wc-widget-name">{{ $botName ?: 'Nexova IA' }}</div>
                        <div class="wc-widget-sub">En línea · Asistente IA</div>
                    </div>
                </div>
                <div class="wc-widget-body" style="min-height:90px">
                    <div class="wc-widget-bubble">{{ $welcomeMessage ?: 'Hola, ¿en qué te puedo ayudar?' }}</div>
                </div>
                {{-- FAB preview --}}
                <div style="background:var(--c-surf2,#f0f2f5);padding:12px;display:flex;justify-content:{{ $widgetPosition === 'left' ? 'flex-start' : 'flex-end' }}">
                    <div style="width:40px;height:40px;border-radius:50%;background:{{ $accentColor }};display:flex;align-items:center;justify-content:center">
                        <svg fill="white" viewBox="0 0 24 24" width="18" height="18">
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        @if($previewMessageEnabled && $previewMessage)
        <div style="margin-top:12px;padding:10px 12px;background:var(--c-bg,#f5f6f8);border:1px solid var(--c-border,#e3e6ea);border-radius:8px;font-size:12px;color:var(--c-text,#111)">
            <span style="font-size:10px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px">Burbuja flotante</span>
            {{ $previewMessage }}
        </div>
        @endif
    </div>

    {{-- ── Guardar ── --}}
    <div class="wc-card" style="padding:16px">
        <button class="wc-btn wc-btn-primary" wire:click="save" wire:loading.attr="disabled" style="width:100%;justify-content:center">
            <span wire:loading.remove wire:target="save" style="display:flex;align-items:center;gap:6px">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar configuración
            </span>
            <span wire:loading wire:target="save" style="display:flex;align-items:center;gap:6px">
                <svg style="animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Guardando...
            </span>
        </button>
    </div>

</div>{{-- /.wc-right-col --}}

</div>
</x-filament-panels::page>
