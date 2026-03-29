<x-filament-panels::page>
<style>
.sp-wrap  { display:flex; flex-direction:column; gap:24px; max-width:600px; }
.sp-card  { background:var(--fi-color-white,#fff); border:1px solid var(--c-border,#e5e7eb); border-radius:12px; padding:24px; }
.sp-title { font-size:15px; font-weight:700; color:var(--c-text,#111827); margin:0 0 4px; }
.sp-sub   { font-size:13px; color:var(--c-sub,#6b7280); margin:0 0 20px; }
.sp-field { margin-bottom:14px; }
.sp-label { display:block; font-size:12px; font-weight:700; color:var(--c-sub,#6b7280); text-transform:uppercase; letter-spacing:.04em; margin-bottom:5px; }
.sp-input { width:100%; padding:8px 12px; border:1.5px solid var(--c-border,#e5e7eb); border-radius:8px; font-size:13px; font-family:inherit; color:var(--c-text,#111827); background:var(--fi-color-white,#fff); box-sizing:border-box; outline:none; transition:border-color .15s; }
.sp-input:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.12); }
.sp-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.sp-btn   { display:inline-flex; align-items:center; gap:6px; padding:9px 20px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; border:none; font-family:inherit; transition:opacity .15s; }
.sp-btn:hover { opacity:.88; }
.sp-sep   { height:1px; background:var(--c-border,#e5e7eb); margin:18px 0; }
.sp-select { width:100%; padding:8px 12px; border:1.5px solid var(--c-border,#e5e7eb); border-radius:8px; font-size:13px; font-family:inherit; background:var(--fi-color-white,#fff); color:var(--c-text,#111827); outline:none; }
.sp-select:focus { border-color:#22c55e; }
.sp-2fa-badge { display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:99px;font-size:12px;font-weight:700; }
</style>

<div class="sp-wrap">

    {{-- ── PROFILE ── --}}
    <div class="sp-card">
        <p class="sp-title">Datos personales</p>
        <p class="sp-sub">Nombre y contraseña de acceso al panel</p>

        <div class="sp-field">
            <label class="sp-label">Nombre</label>
            <input wire:model="name" class="sp-input" placeholder="Tu nombre">
        </div>
        <div class="sp-field">
            <label class="sp-label">Email</label>
            <input value="{{ auth()->user()->email }}" class="sp-input" disabled
                   style="opacity:.55;cursor:not-allowed" title="El email no se puede cambiar desde aquí">
        </div>

        <div class="sp-sep"></div>

        <p style="font-size:13px;font-weight:700;color:var(--c-text,#111827);margin:0 0 12px">Cambiar contraseña</p>
        <div class="sp-field">
            <label class="sp-label">Contraseña actual</label>
            <input wire:model="currentPassword" type="password" class="sp-input" placeholder="Contraseña actual">
        </div>
        <div class="sp-grid2">
            <div class="sp-field">
                <label class="sp-label">Nueva contraseña</label>
                <input wire:model="newPassword" type="password" class="sp-input" placeholder="Mínimo 8 caracteres">
            </div>
            <div class="sp-field">
                <label class="sp-label">Confirmar contraseña</label>
                <input wire:model="confirmPassword" type="password" class="sp-input" placeholder="Repite la contraseña">
            </div>
        </div>

        <button wire:click="saveProfile" class="sp-btn" style="background:#22c55e;color:#fff;margin-top:4px">
            Guardar perfil
        </button>
    </div>

    {{-- ── 2FA ── --}}
    <div class="sp-card">
        <p class="sp-title">Autenticación de dos factores (2FA)</p>
        <p class="sp-sub">Protege tu cuenta con un código TOTP (Google Authenticator, Authy, etc.)</p>

        @if($this->{'2faActive'})
            <span class="sp-2fa-badge" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;margin-bottom:16px">
                <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>
                2FA activado
            </span>
            <br>
            <button wire:click="disable2fa" class="sp-btn" style="background:#fee2e2;color:#b91c1c;margin-top:12px">
                Desactivar 2FA
            </button>
        @elseif($showSetup2fa)
            <div style="margin-bottom:16px">
                <p style="font-size:13px;color:var(--c-text,#111827);margin:0 0 12px">
                    Escanea este QR con tu app autenticadora y luego introduce el código de 6 dígitos:
                </p>
                <div style="display:inline-block;padding:12px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;margin-bottom:14px">
                    {!! $qrSvg !!}
                </div>
                <div style="display:flex;gap:10px;align-items:center">
                    <input wire:model="twoFactorCode" class="sp-input" placeholder="Código de 6 dígitos"
                           maxlength="6" style="max-width:180px;font-size:18px;letter-spacing:.2em;text-align:center">
                    <button wire:click="confirm2fa" class="sp-btn" style="background:#22c55e;color:#fff">
                        Verificar y activar
                    </button>
                    <button wire:click="$set('showSetup2fa', false)" class="sp-btn" style="background:#f3f4f6;color:#374151">
                        Cancelar
                    </button>
                </div>
            </div>
        @else
            <span class="sp-2fa-badge" style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;margin-bottom:16px">
                <span style="width:7px;height:7px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
                2FA desactivado
            </span>
            <br>
            <button wire:click="begin2faSetup" class="sp-btn" style="background:#1a1f14;color:#fff;margin-top:12px">
                Configurar 2FA
            </button>
        @endif
    </div>

    {{-- ── SMTP ── --}}
    <div class="sp-card">
        <p class="sp-title">Servidor SMTP</p>
        <p class="sp-sub">Configura el correo saliente para verificaciones, notificaciones y alertas</p>

        <div class="sp-grid2">
            <div class="sp-field">
                <label class="sp-label">Host SMTP</label>
                <input wire:model="smtpHost" class="sp-input" placeholder="smtp.hostinger.com">
            </div>
            <div class="sp-field">
                <label class="sp-label">Puerto</label>
                <input wire:model="smtpPort" class="sp-input" placeholder="587">
            </div>
        </div>
        <div class="sp-grid2">
            <div class="sp-field">
                <label class="sp-label">Usuario</label>
                <input wire:model="smtpUsername" class="sp-input" placeholder="no-reply@tudominio.com">
            </div>
            <div class="sp-field">
                <label class="sp-label">Contraseña <span style="font-size:11px;color:#9ca3af">(en blanco = no cambiar)</span></label>
                <input wire:model="smtpPassword" type="password" class="sp-input" placeholder="••••••••">
            </div>
        </div>
        <div class="sp-grid2">
            <div class="sp-field">
                <label class="sp-label">Cifrado</label>
                <select wire:model="smtpEncryption" class="sp-select">
                    <option value="tls">TLS</option>
                    <option value="ssl">SSL</option>
                    <option value="">Ninguno</option>
                </select>
            </div>
            <div class="sp-field">
                <label class="sp-label">Email remitente</label>
                <input wire:model="smtpFromAddress" class="sp-input" placeholder="soporte@tudominio.com">
            </div>
        </div>
        <div class="sp-field">
            <label class="sp-label">Nombre remitente</label>
            <input wire:model="smtpFromName" class="sp-input" placeholder="Nexova Desk">
        </div>

        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:4px">
            <button wire:click="saveSmtp" class="sp-btn" style="background:#22c55e;color:#fff">
                Guardar SMTP
            </button>
            <div style="display:flex;gap:8px;align-items:center">
                <input wire:model="testEmailTo" class="sp-input" placeholder="prueba@email.com"
                       style="max-width:200px">
                <button wire:click="testSmtp" class="sp-btn" style="background:#1a1f14;color:#fff">
                    Enviar prueba
                </button>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
