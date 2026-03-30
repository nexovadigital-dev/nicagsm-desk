<x-filament-panels::page>
<style>
.mc-wrap   { max-width:620px; display:flex; flex-direction:column; gap:20px; }
.mc-card   { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
.mc-head   { padding:16px 20px; border-bottom:1px solid #e2e8f0; }
.mc-title  { font-size:14px; font-weight:800; color:#0f172a; margin:0 0 2px; }
.mc-sub    { font-size:12px; color:#64748b; margin:0; }
.mc-body   { padding:20px; display:flex; flex-direction:column; gap:14px; }
.mc-foot   { padding:14px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.mc-grid2  { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.mc-label  { display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em; margin-bottom:5px; }
.mc-hint   { font-size:10.5px; color:#94a3b8; font-weight:400; text-transform:none; letter-spacing:0; }
.mc-input  { width:100%; padding:8px 11px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; font-family:inherit; color:#0f172a; background:#fff; box-sizing:border-box; outline:none; transition:border .15s,box-shadow .15s; }
.mc-input:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.1); }
.mc-select { width:100%; padding:8px 11px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; font-family:inherit; color:#0f172a; background:#fff; outline:none; transition:border .15s; cursor:pointer; }
.mc-select:focus { border-color:#22c55e; }
.mc-btn    { display:inline-flex; align-items:center; gap:6px; padding:8px 18px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; font-family:inherit; transition:opacity .15s; white-space:nowrap; }
.mc-btn:hover { opacity:.85; }
.mc-badge  { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:99px; font-size:11px; font-weight:600; }
</style>

<div class="mc-wrap">

    {{-- Header --}}
    <div>
        <h1 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;letter-spacing:-.02em">Servidor de correo</h1>
        <p style="font-size:13px;color:#64748b;margin:0">Configura el SMTP para envío de OTPs, notificaciones y alertas.</p>
    </div>

    {{-- SMTP Config card --}}
    <div class="mc-card">
        <div class="mc-head">
            <p class="mc-title">Configuración SMTP</p>
            <p class="mc-sub">Las credenciales se guardan cifradas en la base de datos.</p>
        </div>
        <div class="mc-body">

            {{-- Host + Port --}}
            <div style="display:grid;grid-template-columns:1fr 110px;gap:12px">
                <div>
                    <label class="mc-label">Host SMTP</label>
                    <input wire:model="smtpHost" class="mc-input" placeholder="smtp.ionos.com">
                </div>
                <div>
                    <label class="mc-label">Puerto</label>
                    <input wire:model="smtpPort" class="mc-input" placeholder="587">
                </div>
            </div>

            {{-- User + Pass --}}
            <div class="mc-grid2">
                <div>
                    <label class="mc-label">Usuario</label>
                    <input wire:model="smtpUsername" class="mc-input" placeholder="no-reply@tudominio.com">
                </div>
                <div>
                    <label class="mc-label">Contraseña <span class="mc-hint">(vacío = sin cambios)</span></label>
                    <input wire:model="smtpPassword" type="password" class="mc-input" placeholder="••••••••">
                </div>
            </div>

            {{-- Encryption + From --}}
            <div style="display:grid;grid-template-columns:120px 1fr;gap:12px">
                <div>
                    <label class="mc-label">Cifrado</label>
                    <select wire:model="smtpEncryption" class="mc-select">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="">Ninguno</option>
                    </select>
                </div>
                <div>
                    <label class="mc-label">Email remitente</label>
                    <input wire:model="smtpFromAddress" class="mc-input" placeholder="soporte@tudominio.com">
                </div>
            </div>

            {{-- From name --}}
            <div>
                <label class="mc-label">Nombre remitente</label>
                <input wire:model="smtpFromName" class="mc-input" placeholder="Nexova Desk">
            </div>
        </div>

        <div class="mc-foot">
            <button wire:click="save" class="mc-btn" style="background:#0f172a;color:#fff">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Guardar configuración
            </button>
            <div style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:5px">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                Contraseña cifrada AES-256
            </div>
        </div>
    </div>

    {{-- Test email card --}}
    <div class="mc-card">
        <div class="mc-head">
            <p class="mc-title">Correo de prueba</p>
            <p class="mc-sub">Verifica que la configuración funciona enviando un correo ahora.</p>
        </div>
        <div class="mc-body">
            <div>
                <label class="mc-label">Destinatario</label>
                <input wire:model="testEmailTo" class="mc-input" placeholder="correo@ejemplo.com">
                <p style="font-size:11px;color:#94a3b8;margin-top:5px">
                    Pre-rellenado con tu email de administrador. Puedes cambiarlo.
                </p>
            </div>
        </div>
        <div class="mc-foot">
            <button wire:click="testEmail" class="mc-btn" style="background:#22c55e;color:#0d1117">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Enviar correo de prueba
            </button>
            <div wire:loading wire:target="testEmail" style="font-size:12px;color:#64748b">Enviando…</div>
        </div>
    </div>

    {{-- Info --}}
    <div style="padding:14px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;gap:10px;align-items:flex-start">
        <svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" width="15" height="15" style="flex-shrink:0;margin-top:1px" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p style="font-size:12px;color:#166534;margin:0;line-height:1.6">
            El correo de prueba usa la configuración guardada en DB, no el archivo <code style="font-size:11px;background:rgba(0,0,0,.06);padding:1px 5px;border-radius:4px">.env</code>.
            Guarda primero antes de probar si acabas de cambiar credenciales.
        </p>
    </div>

</div>
</x-filament-panels::page>
