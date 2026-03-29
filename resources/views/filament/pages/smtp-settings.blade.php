<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

.sm-wrap { padding: 32px 36px 64px; max-width: 1040px; }

/* Each section: label-left config-right */
.sm-section {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 32px;
    padding: 28px 0;
    border-top: 1px solid var(--c-border,#e3e6ea);
}
.sm-section:first-of-type { border-top: none; padding-top: 0; }
/* section last — no extra rule needed */
@media (max-width: 720px) {
    .sm-section { grid-template-columns: 1fr; gap: 12px; }
}

.sm-section-label {
    padding-top: 2px;
}
.sm-section-label h3 {
    font-size: 14px; font-weight: 600; color: var(--c-text,#111);
    margin: 0 0 6px;
}
.sm-section-label h3 svg { display: none; }
.sm-section-label p {
    font-size: 12.5px; color: var(--c-sub,#6b7280); margin: 0; line-height: 1.6;
}

.sm-panel { /* open — no card border */ }

.sm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 500px) { .sm-grid { grid-template-columns: 1fr; } }

.sm-field { display: flex; flex-direction: column; gap: 4px; }
.sm-label { font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .05em; }
.sm-input, .sm-select { background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px; color: var(--c-text,#111); font-size: 13px; padding: 8px 11px; outline: none; width: 100%; font-family: inherit; transition: border-color .12s; box-sizing: border-box; }
.sm-input:focus, .sm-select:focus { border-color: #3b82f6; }
.sm-input::placeholder { color: var(--c-sub); }

.sm-toggle-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.sm-toggle { position: relative; display: inline-block; width: 38px; height: 21px; flex-shrink: 0; }
.sm-toggle input { opacity: 0; width: 0; height: 0; }
.sm-slider { position: absolute; cursor: pointer; inset: 0; background: var(--c-border,#e3e6ea); border-radius: 99px; transition: background .2s; }
.sm-slider:before { content:''; position: absolute; height: 15px; width: 15px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: transform .2s; }
.sm-toggle input:checked + .sm-slider { background: #3b82f6; }
.sm-toggle input:checked + .sm-slider:before { transform: translateX(17px); }
.sm-toggle-label { font-size: 13px; font-weight: 500; color: var(--c-text,#111); }
.sm-toggle-sub { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 2px; }

.sm-btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; border-radius: 7px; font-size: 12.5px; font-weight: 500; cursor: pointer; border: 1px solid transparent; font-family: inherit; transition: background .1s; }
.sm-btn-primary { background: #1e293b; color:#f8fafc; }
.sm-btn-primary:hover { background: #0f172a; }
.sm-btn-ghost { background: transparent; color: var(--c-sub,#6b7280); border-color: var(--c-border,#e3e6ea); }
.sm-btn-ghost:hover { background: var(--c-surf2,#f0f2f5); }
.sm-alert { padding: 9px 13px; border-radius: 7px; font-size: 12px; font-weight: 500; }
.sm-alert-success { background: rgba(5,150,105,.07); border:1px solid rgba(5,150,105,.2); color: #059669; }
.sm-alert-error   { background: rgba(220,38,38,.07); border:1px solid rgba(220,38,38,.2); color: #dc2626; }
.sm-actions { margin-top: 14px; display: flex; align-items: center; gap: 10px; }
</style>

<div class="sm-wrap">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:28px;letter-spacing:-.02em">Email & SMTP</h1>

    {{-- ── Notificaciones ── --}}
    <div class="sm-section">
        <div class="sm-section-label">
            <h3>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Notificaciones
            </h3>
            <p>Activa el envío automático de emails al cliente cuando hay una respuesta en su ticket.</p>
        </div>
        <div class="sm-panel">
            <div class="sm-toggle-row">
                <div>
                    <div class="sm-toggle-label">Notificar al cliente</div>
                    <div class="sm-toggle-sub">Email automático cuando bot o agente responde</div>
                </div>
                <label class="sm-toggle">
                    <input type="checkbox" wire:model.live="notificationsEnabled">
                    <span class="sm-slider"></span>
                </label>
            </div>
        </div>
    </div>

    {{-- ── Servidor SMTP ── --}}
    <div class="sm-section">
        <div class="sm-section-label">
            <h3>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Servidor SMTP
            </h3>
            <p>Gmail, Outlook, Mailtrap, Amazon SES u otro proveedor compatible con SMTP.</p>
        </div>
        <div class="sm-panel">
            <div class="sm-grid">
                <div class="sm-field" style="grid-column:1/-1">
                    <label class="sm-label">Host</label>
                    <input type="text" class="sm-input" wire:model="host" placeholder="smtp.gmail.com">
                </div>
                <div class="sm-field">
                    <label class="sm-label">Puerto</label>
                    <input type="number" class="sm-input" wire:model="port" placeholder="587">
                </div>
                <div class="sm-field">
                    <label class="sm-label">Cifrado</label>
                    <select class="sm-select" wire:model="encryption">
                        <option value="tls">TLS (recomendado)</option>
                        <option value="ssl">SSL</option>
                        <option value="none">Sin cifrado</option>
                    </select>
                </div>
                <div class="sm-field">
                    <label class="sm-label">Usuario</label>
                    <input type="text" class="sm-input" wire:model="username" placeholder="tu@email.com" autocomplete="off">
                </div>
                <div class="sm-field">
                    <label class="sm-label">Contraseña</label>
                    <input type="password" class="sm-input" wire:model="password" placeholder="••••••••" autocomplete="new-password">
                </div>
                <div class="sm-field">
                    <label class="sm-label">Email remitente</label>
                    <input type="email" class="sm-input" wire:model="fromAddress" placeholder="noreply@nexova.com">
                </div>
                <div class="sm-field">
                    <label class="sm-label">Nombre remitente</label>
                    <input type="text" class="sm-input" wire:model="fromName" placeholder="Nexova Chat">
                </div>
            </div>

            <div class="sm-actions">
                <button class="sm-btn sm-btn-primary" wire:click="save" wire:loading.attr="disabled">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar
                </button>
            </div>
        </div>
    </div>

    {{-- ── Probar ── --}}
    <div class="sm-section">
        <div class="sm-section-label">
            <h3>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Probar configuración
            </h3>
            <p>Envía un email de prueba para verificar que los datos SMTP son correctos.</p>
        </div>
        <div class="sm-panel">
            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                <input type="email" class="sm-input" wire:model="testEmail" placeholder="email@destino.com" style="max-width:260px">
                <button class="sm-btn sm-btn-ghost" wire:click="sendTest" wire:loading.attr="disabled">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Enviar prueba
                </button>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
