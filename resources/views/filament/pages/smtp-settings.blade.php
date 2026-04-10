<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

.sm-wrap { padding: 32px 36px 64px; max-width: 1040px; }

.sm-section {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 32px;
    padding: 28px 0;
    border-top: 1px solid var(--c-border,#e3e6ea);
}
.sm-section:first-of-type { border-top: none; padding-top: 0; }
@media (max-width: 720px) {
    .sm-section { grid-template-columns: 1fr; gap: 12px; }
}

.sm-section-label { padding-top: 2px; }
.sm-section-label h3 { font-size: 14px; font-weight: 600; color: var(--c-text,#111); margin: 0 0 6px; }
.sm-section-label p  { font-size: 12.5px; color: var(--c-sub,#6b7280); margin: 0; line-height: 1.6; }

.sm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 500px) { .sm-grid { grid-template-columns: 1fr; } }

.sm-field { display: flex; flex-direction: column; gap: 4px; }
.sm-label { font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .05em; }
.sm-input, .sm-select { background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px; color: var(--c-text,#111); font-size: 13px; padding: 8px 11px; outline: none; width: 100%; font-family: inherit; transition: border-color .12s; box-sizing: border-box; }
.sm-input:focus, .sm-select:focus { border-color: #16a34a; }
.sm-input::placeholder { color: var(--c-sub); }

.sm-toggle-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.sm-toggle { position: relative; display: inline-block; width: 38px; height: 21px; flex-shrink: 0; }
.sm-toggle input { opacity: 0; width: 0; height: 0; }
.sm-slider { position: absolute; cursor: pointer; inset: 0; background: var(--c-border,#e3e6ea); border-radius: 99px; transition: background .2s; }
.sm-slider:before { content:''; position: absolute; height: 15px; width: 15px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: transform .2s; }
.sm-toggle input:checked + .sm-slider { background: #22c55e; }
.sm-toggle input:checked + .sm-slider:before { transform: translateX(17px); }
.sm-toggle-label { font-size: 13px; font-weight: 500; color: var(--c-text,#111); }
.sm-toggle-sub { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 2px; }

.sm-btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; border-radius: 7px; font-size: 12.5px; font-weight: 500; cursor: pointer; border: 1px solid transparent; font-family: inherit; transition: background .1s; }
.sm-btn-primary { background: #1e293b; color:#f8fafc; }
.sm-btn-primary:hover { background: #0f172a; }
.sm-btn-ghost { background: transparent; color: var(--c-sub,#6b7280); border-color: var(--c-border,#e3e6ea); }
.sm-btn-ghost:hover { background: var(--c-surf2,#f0f2f5); }
.sm-actions { margin-top: 14px; display: flex; align-items: center; gap: 10px; }

.sm-notice { padding: 11px 14px; border-radius: 8px; font-size: 12px; line-height: 1.6; }
.sm-notice-info    { background: rgba(59,130,246,.07); border: 1px solid rgba(59,130,246,.2); color: #1d4ed8; }
.sm-notice-success { background: rgba(5,150,105,.07); border: 1px solid rgba(5,150,105,.2); color: #059669; }
.sm-notice code { background: rgba(0,0,0,.06); padding: 1px 5px; border-radius: 4px; font-size: 11.5px; }
</style>

<div class="sm-wrap">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:4px;letter-spacing:-.02em">Email & SMTP</h1>
    <p style="font-size:13px;color:var(--c-sub,#6b7280);margin-bottom:28px">
        Configura cómo se envían los emails de tickets a tus clientes.
    </p>

    {{-- ── Email actual (genérico o personalizado) ── --}}
    <div class="sm-section">
        <div class="sm-section-label">
            <h3>Email de envío</h3>
            <p>Dirección desde la que tus clientes recibirán los emails de tickets y notificaciones.</p>
        </div>
        <div>
            @if($enabled && $fromAddress)
                <div class="sm-notice sm-notice-success">
                    <strong>SMTP configurado</strong><br>
                    Los emails se enviarán desde <code>{{ $fromAddress }}</code> usando tu servidor SMTP.
                </div>
            @else
                <div class="sm-notice" style="background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.25);color:#92400e">
                    <strong>SMTP no configurado</strong><br>
                    Sin SMTP activo, el envío de emails (restablecimiento de contraseña, notificaciones de tickets) no funcionará.<br><br>
                    Configura tu servidor SMTP abajo y actívalo para habilitar el envío de correos.
                </div>
            @endif
        </div>
    </div>

    {{-- ── Notificaciones ── --}}
    <div class="sm-section">
        <div class="sm-section-label">
            <h3>Notificaciones al cliente</h3>
            <p>Email automático al cliente cuando bot o agente responde a su ticket.</p>
        </div>
        <div>
            <div class="sm-toggle-row">
                <div>
                    <div class="sm-toggle-label">Notificar al cliente</div>
                    <div class="sm-toggle-sub">Se envía un email con cada respuesta del agente o bot</div>
                </div>
                <label class="sm-toggle">
                    <input type="checkbox" wire:model.live="notificationsEnabled">
                    <span class="sm-slider"></span>
                </label>
            </div>
            @if($notificationsEnabled && ! $smtpReady)
                <div class="sm-notice" style="margin-top:10px;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);color:#b91c1c">
                    Las notificaciones están activadas pero el SMTP no está configurado — los emails no se enviarán.
                </div>
            @endif
        </div>
    </div>

    {{-- ── Servidor SMTP ── --}}
    <div class="sm-section">
        <div class="sm-section-label">
            <h3>Servidor SMTP propio</h3>
            <p>Gmail, Outlook, Amazon SES, Brevo u otro proveedor SMTP. Deja vacío para usar el servidor de la plataforma.</p>
        </div>
        <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                <div>
                    <div class="sm-toggle-label">Usar mi SMTP</div>
                    <div class="sm-toggle-sub">Activa para enviar desde tu propio servidor</div>
                </div>
                <label class="sm-toggle">
                    <input type="checkbox" wire:model.live="enabled">
                    <span class="sm-slider"></span>
                </label>
            </div>

            @if($enabled)
            <div class="sm-grid">
                <div class="sm-field" style="grid-column:1/-1">
                    <label class="sm-label">Host SMTP</label>
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
                    <label class="sm-label">Usuario SMTP</label>
                    <input type="text" class="sm-input" wire:model="username" placeholder="tu@dominio.com" autocomplete="off">
                </div>
                <div class="sm-field">
                    <label class="sm-label">Contraseña SMTP</label>
                    <input type="password" class="sm-input" wire:model="password" placeholder="••••••••" autocomplete="new-password">
                </div>
                <div class="sm-field">
                    <label class="sm-label">Email remitente (FROM)</label>
                    <input type="email" class="sm-input" wire:model="fromAddress" placeholder="soporte@tudominio.com">
                </div>
                <div class="sm-field">
                    <label class="sm-label">Nombre remitente</label>
                    <input type="text" class="sm-input" wire:model="fromName" placeholder="Soporte Acme">
                </div>
            </div>
            @endif

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

    {{-- ── Probar configuración ── --}}
    @php
        $smtpReady = $enabled && !empty($host) && !empty($username) && !empty($fromAddress);
    @endphp
    <div class="sm-section">
        <div class="sm-section-label">
            <h3>Probar envío</h3>
            <p>Verifica que el email llega correctamente antes de activarlo para tus clientes.</p>
        </div>
        <div>
            @if(! $smtpReady)
                <div class="sm-notice" style="background:rgba(100,116,139,.07);border:1px solid rgba(100,116,139,.2);color:var(--c-sub,#6b7280)">
                    Configura y guarda tu SMTP primero para poder hacer una prueba de envío.
                </div>
            @else
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                    <input type="email" class="sm-input" wire:model="testEmail" placeholder="email@destino.com" style="max-width:260px">
                    <button class="sm-btn sm-btn-ghost" wire:click="sendTest" wire:loading.attr="disabled">
                        <svg wire:loading wire:target="sendTest" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" style="animation:spin 1s linear infinite">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <svg wire:loading.remove wire:target="sendTest" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span wire:loading.remove wire:target="sendTest">Enviar prueba</span>
                        <span wire:loading wire:target="sendTest">Enviando…</span>
                    </button>
                </div>
                <p style="font-size:11.5px;color:var(--c-sub,#6b7280);margin-top:8px">
                    Se enviará desde <strong>{{ $fromAddress }}</strong> usando tu servidor SMTP.
                </p>
            @endif
        </div>
    </div>

</div>
</x-filament-panels::page>
