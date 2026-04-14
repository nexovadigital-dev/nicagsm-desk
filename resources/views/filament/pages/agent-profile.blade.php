<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

/* Wrapper */
.ap-wrap { padding: 32px 36px 64px; max-width: 1040px; }
.ap-title { font-size: 22px; font-weight: 800; color: var(--c-text,#111); letter-spacing: -.03em; margin-bottom: 6px; }
.ap-subtitle { font-size: 13px; color: var(--c-sub,#6b7280); margin-bottom: 28px; }

/* Tabs */
.ap-tabs { display: flex; gap: 2px; border-bottom: 1.5px solid var(--c-border,#e3e6ea); margin-bottom: 32px; overflow-x: auto; }
.ap-tab { padding: 9px 18px; font-size: 13px; font-weight: 500; color: var(--c-sub,#6b7280); border: none; background: none; cursor: pointer; border-bottom: 2.5px solid transparent; margin-bottom: -1.5px; white-space: nowrap; transition: color .15s, border-color .15s; border-radius: 0; }
.ap-tab:hover { color: var(--c-text,#111); }
.ap-tab.active { color: var(--c-primary,#16a34a); border-bottom-color: var(--c-primary,#16a34a); font-weight: 700; }

/* Section rows */
.ap-section { display: grid; grid-template-columns: 200px 1fr; gap: 32px 48px; padding: 32px 0; border-top: 1px solid var(--c-border,#e3e6ea); align-items: start; }
.ap-section.no-top { border-top: none; padding-top: 0; }
.ap-section-title { font-size: 14px; font-weight: 700; color: var(--c-text,#111); margin-bottom: 4px; }
.ap-section-desc { font-size: 12px; color: var(--c-sub,#6b7280); line-height: 1.55; }

/* Fields */
.ap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.ap-field { display: flex; flex-direction: column; gap: 5px; }
.ap-label { font-size: 12px; font-weight: 600; color: var(--c-text,#111); }
.ap-input { border: 1.5px solid var(--c-border,#e3e6ea); border-radius: 8px; padding: 8px 12px; font-size: 13px; color: var(--c-text,#111); background: var(--c-surface,#fff); transition: border-color .15s; width: 100%; }
.ap-input:focus { outline: none; border-color: var(--c-primary,#16a34a); }
select.ap-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 32px; }

/* Actions */
.ap-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; align-items: center; }
.ap-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; border: none; transition: all .15s; }
.ap-btn-primary { background: var(--c-primary,#16a34a); color: #fff; }
.ap-btn-primary:hover { opacity: .88; }
.ap-btn-ghost { background: var(--c-bg,#f5f6f8); color: var(--c-text,#111); border: 1.5px solid var(--c-border,#e3e6ea); }
.ap-btn-ghost:hover { background: var(--c-border,#e3e6ea); }

/* Toggle */
.ap-toggle { position: relative; display: inline-block; width: 38px; height: 22px; flex-shrink: 0; }
.ap-toggle input { opacity: 0; width: 0; height: 0; }
.ap-slider { position: absolute; inset: 0; background: #d1d5db; border-radius: 99px; transition: .2s; cursor: pointer; }
.ap-slider:before { content: ''; position: absolute; width: 16px; height: 16px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
input:checked + .ap-slider { background: var(--c-primary,#16a34a); }
input:checked + .ap-slider:before { transform: translateX(16px); }
.ap-toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; }
.ap-toggle-label { font-size: 13px; font-weight: 600; color: var(--c-text,#111); }
.ap-toggle-sub { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 2px; }

/* Notices */
.ap-notice { padding: 12px 14px; border-radius: 8px; font-size: 12.5px; line-height: 1.55; }
.ap-notice-info    { background: #eff6ff; border-left: 3px solid #3b82f6; color: #1e40af; }
.ap-notice-success { background: #f0fdf4; border-left: 3px solid #16a34a; color: #166534; }
.ap-notice-warn    { background: #fffbeb; border-left: 3px solid #f59e0b; color: #92400e; }
.ap-notice-error   { background: #fef2f2; border-left: 3px solid #dc2626; color: #991b1b; }

/* Avatar */
.ap-avatar-zone { width: 80px; height: 80px; border-radius: 50%; border: 2px dashed var(--c-border,#e3e6ea); display: flex; align-items: center; justify-content: center; cursor: pointer; overflow: hidden; position: relative; background: var(--c-bg,#f5f6f8); }
.ap-avatar-zone img { width: 100%; height: 100%; object-fit: cover; }
.ap-avatar-zone:hover { border-color: var(--c-primary,#16a34a); }

/* License */
.lic-card { background: var(--c-bg,#f8fafc); border: 1.5px solid var(--c-border,#e3e6ea); border-radius: 12px; padding: 24px 22px; }
.lic-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 99px; font-size: 11.5px; font-weight: 700; margin-bottom: 16px; }
.lic-badge-ok  { background: #dcfce7; color: #166534; }
.lic-badge-err { background: #fee2e2; color: #991b1b; }
.lic-row { display: flex; gap: 8px; align-items: flex-start; margin-bottom: 10px; }
.lic-label { font-size: 11.5px; font-weight: 700; color: var(--c-sub,#6b7280); min-width: 120px; }
.lic-value { font-size: 13px; color: var(--c-text,#111); word-break: break-all; }
.lic-divider { border: none; border-top: 1px solid var(--c-border,#e3e6ea); margin: 16px 0; }
.lic-features { display: flex; flex-wrap: wrap; gap: 8px; }
.lic-feat { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; color: var(--c-text,#111); background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e3e6ea); border-radius: 6px; padding: 4px 10px; }

/* AI key indicator */
.key-set { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: #166534; background: #dcfce7; border-radius: 6px; padding: 2px 8px; margin-bottom: 4px; }

/* Cron */
.cron-subtab-row { display: flex; gap: 4px; flex-wrap: wrap; border-bottom: 1.5px solid var(--c-border,#e3e6ea); margin-bottom: 20px; }
.cron-subtab { padding: 8px 14px; font-size: 12.5px; font-weight: 500; color: var(--c-sub,#6b7280); border: none; background: none; cursor: pointer; border-bottom: 2.5px solid transparent; margin-bottom: -1.5px; white-space: nowrap; transition: all .15s; position: relative; }
.cron-subtab:hover { color: var(--c-text,#111); }
.cron-subtab.active { color: var(--c-primary,#16a34a); border-bottom-color: var(--c-primary,#16a34a); font-weight: 700; }
.cron-endpoint-card { border: 1.5px solid var(--c-border,#e3e6ea); border-radius: 10px; margin-bottom: 12px; overflow: hidden; background: var(--c-surface,#fff); }
.cron-endpoint-head { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-bottom: 1px solid var(--c-border,#e3e6ea); }
.cron-endpoint-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.cron-endpoint-name { font-size: 13px; font-weight: 700; color: var(--c-text,#111); }
.cron-endpoint-desc { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 2px; }
.cron-endpoint-body { padding: 12px 16px; }
.cron-url-row { display: flex; align-items: center; gap: 8px; background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px; padding: 8px 12px; }
.cron-url-text { font-family: monospace; font-size: 12px; color: var(--c-text,#111); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cron-copy-btn { font-size: 11.5px; font-weight: 600; color: var(--c-primary,#16a34a); background: none; border: none; cursor: pointer; white-space: nowrap; padding: 2px 6px; }
.cron-copy-btn:hover { text-decoration: underline; }
.cron-freq { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 8px; }

@media (max-width: 720px) {
    .ap-section { grid-template-columns: 1fr; gap: 16px; }
    .ap-grid { grid-template-columns: 1fr; }
    .ap-wrap { padding: 20px 16px 48px; }
}
</style>

@php
    $appUrl   = rtrim(config('app.url'), '/');
    $smtpReady = $smtpEnabled && $smtpHost && $smtpUsername && $smtpFromAddress;
@endphp

<div class="ap-wrap" x-data="{ tab: 'perfil', cronTab: 'cronjob' }">

    <div class="ap-title">Configuracion Avanzada</div>
    <div class="ap-subtitle">Gestiona tu perfil, organizacion, seguridad e integraciones</div>

    {{-- Tabs --}}
    <div class="ap-tabs">
        <button class="ap-tab" :class="{ active: tab === 'perfil' }" @click="tab = 'perfil'">Perfil</button>
        @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
        <button class="ap-tab" :class="{ active: tab === 'org' }" @click="tab = 'org'">Organizacion</button>
        @endif
        <button class="ap-tab" :class="{ active: tab === 'seguridad' }" @click="tab = 'seguridad'">Seguridad</button>
        @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
        <button class="ap-tab" :class="{ active: tab === 'ia' }" @click="tab = 'ia'">Inteligencia Artificial</button>
        <button class="ap-tab" :class="{ active: tab === 'correo' }" @click="tab = 'correo'">Correo Electronico</button>
        <button class="ap-tab" :class="{ active: tab === 'licencia' }" @click="tab = 'licencia'">Licencia</button>
        <button class="ap-tab" :class="{ active: tab === 'cron' }" @click="tab = 'cron'">Automatizacion</button>
        @endif
    </div>

    {{-- ═══ TAB: PERFIL ═══ --}}
    <div x-show="tab === 'perfil'" x-transition.opacity>

        {{-- Avatar --}}
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Foto de perfil</div>
                <div class="ap-section-desc">Aparece en el Live Inbox y en los tickets asignados a ti.</div>
            </div>
            <div style="display:flex;align-items:center;gap:20px">
                <label class="ap-avatar-zone" style="cursor:pointer">
                    @if($currentAvatarUrl)
                        <img src="{{ $currentAvatarUrl }}" alt="Avatar">
                    @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28" style="color:#9ca3af"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-4 7a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    @endif
                    <input type="file" wire:model="avatarFile" accept="image/*" style="display:none">
                </label>
                <div>
                    <div style="font-size:12.5px;font-weight:600;color:var(--c-text,#111)">{{ auth()->user()->name }}</div>
                    <div style="font-size:11.5px;color:var(--c-sub,#6b7280);margin-top:2px">{{ auth()->user()->email }}</div>
                    @if($currentAvatarUrl)
                    <button class="ap-btn ap-btn-ghost" wire:click="removeAvatar" style="margin-top:8px;font-size:11.5px;padding:4px 10px">Eliminar foto</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Nombre y email --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Informacion personal</div>
                <div class="ap-section-desc">Tu nombre y email dentro del panel de soporte.</div>
            </div>
            <div class="ap-grid">
                <div class="ap-field" style="grid-column:1/-1">
                    <label class="ap-label">Nombre completo</label>
                    <input type="text" class="ap-input" wire:model="profileName" placeholder="Tu nombre">
                </div>
                <div class="ap-field" style="grid-column:1/-1">
                    <label class="ap-label">Correo electronico</label>
                    <input type="email" class="ap-input" wire:model="profileEmail" placeholder="tu@email.com">
                </div>
                <div class="ap-field" style="grid-column:1/-1">
                    <label class="ap-label">Disponibilidad</label>
                    <select class="ap-input" wire:model="availability">
                        <option value="online">En linea</option>
                        <option value="busy">Ocupado</option>
                        <option value="offline">Desconectado</option>
                    </select>
                </div>
                <div style="grid-column:1/-1">
                    <button class="ap-btn ap-btn-primary" wire:click="saveProfile" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveProfile">Guardar cambios</span>
                        <span wire:loading wire:target="saveProfile">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══ TAB: ORGANIZACION ═══ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'org'" x-transition.opacity>

        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Datos de la empresa</div>
                <div class="ap-section-desc">Aparecen en los emails enviados a clientes y en el widget de chat.</div>
            </div>
            <div class="ap-grid">
                <div class="ap-field" style="grid-column:1/-1">
                    <label class="ap-label">Nombre de la empresa</label>
                    <input type="text" class="ap-input" wire:model="orgName" placeholder="Mi empresa S.A.">
                </div>
                <div class="ap-field">
                    <label class="ap-label">Email de soporte</label>
                    <input type="email" class="ap-input" wire:model="orgSupportEmail" placeholder="soporte@empresa.com">
                </div>
                <div class="ap-field">
                    <label class="ap-label">Nombre de soporte</label>
                    <input type="text" class="ap-input" wire:model="orgSupportName" placeholder="Soporte Acme">
                </div>
                <div class="ap-field">
                    <label class="ap-label">Sitio web</label>
                    <input type="url" class="ap-input" wire:model="orgWebsite" placeholder="https://tuempresa.com">
                </div>
                <div class="ap-field">
                    <label class="ap-label">Zona horaria</label>
                    <select class="ap-input" wire:model="orgTimezone">
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" @selected($orgTimezone === $tz)>{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1/-1">
                    <button class="ap-btn ap-btn-primary" wire:click="saveOrg" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveOrg">Guardar organizacion</span>
                        <span wire:loading wire:target="saveOrg">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- ═══ TAB: SEGURIDAD ═══ --}}
    <div x-show="tab === 'seguridad'" x-transition.opacity>

        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Cambiar contrasena</div>
                <div class="ap-section-desc">Usa una contrasena segura de al menos 8 caracteres.</div>
            </div>
            <div class="ap-grid">
                <div class="ap-field" style="grid-column:1/-1">
                    <label class="ap-label">Contrasena actual</label>
                    <input type="password" class="ap-input" wire:model="currentPassword" autocomplete="current-password">
                </div>
                <div class="ap-field">
                    <label class="ap-label">Nueva contrasena</label>
                    <input type="password" class="ap-input" wire:model="newPassword" autocomplete="new-password">
                </div>
                <div class="ap-field">
                    <label class="ap-label">Confirmar nueva contrasena</label>
                    <input type="password" class="ap-input" wire:model="passwordConfirm" autocomplete="new-password">
                </div>
                <div style="grid-column:1/-1">
                    <button class="ap-btn ap-btn-primary" wire:click="savePassword" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="savePassword">Actualizar contrasena</span>
                        <span wire:loading wire:target="savePassword">Actualizando...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- 2FA --}}
        @if(auth()->user()->organization_id)
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Autenticacion de dos factores (2FA)</div>
                <div class="ap-section-desc">Agrega una capa extra de seguridad a tu cuenta.</div>
            </div>
            <div>
                @if($this->tfaEnabled)
                    <div class="ap-notice ap-notice-success" style="margin-bottom:14px">El 2FA esta activado en tu cuenta.</div>
                    <div class="ap-field" style="margin-bottom:12px">
                        <label class="ap-label">Codigo de autenticador para desactivar</label>
                        <input type="text" class="ap-input" wire:model="tfaDisableCode" placeholder="000000" maxlength="6" style="max-width:160px">
                    </div>
                    <button class="ap-btn ap-btn-ghost" wire:click="disableTwoFactor" style="color:#dc2626">Desactivar 2FA</button>
                @else
                    <div class="ap-notice ap-notice-info" style="margin-bottom:14px">El 2FA no esta activado. Actívalo para mayor seguridad.</div>
                    @if($showQr)
                        <div style="margin-bottom:14px">{!! $qrSvg !!}</div>
                        <p style="font-size:11.5px;color:var(--c-sub,#6b7280);margin-bottom:8px">Escanea el QR con tu app de autenticacion (Google Authenticator, Authy, etc.) y luego ingresa el codigo:</p>
                        <div style="display:flex;gap:8px;align-items:center">
                            <input type="text" class="ap-input" wire:model="tfaCode" placeholder="000000" maxlength="6" style="max-width:140px">
                            <button class="ap-btn ap-btn-primary" wire:click="confirmTwoFactor">Verificar y activar</button>
                        </div>
                    @else
                        <button class="ap-btn ap-btn-ghost" wire:click="initTwoFactor">Configurar 2FA</button>
                    @endif
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- ═══ TAB: INTELIGENCIA ARTIFICIAL ═══ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'ia'" x-transition.opacity>

        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Claves de IA</div>
                <div class="ap-section-desc">Configura las claves API para activar el bot de inteligencia artificial. Por seguridad los campos siempre aparecen vacios — eso no significa que no esten guardadas.</div>
            </div>
            <div>
                <div class="ap-notice ap-notice-info" style="margin-bottom:18px">
                    Las claves se guardan cifradas en tu base de datos. Introduce una clave nueva para actualizarla.
                </div>
                <div class="ap-grid">
                    <div class="ap-field" style="grid-column:1/-1">
                        <label class="ap-label">Clave Groq #1 (principal)</label>
                        @if($groqKey1Set)<span class="key-set">Guardada</span>@endif
                        <input type="password" class="ap-input" wire:model="orgGroqKey" placeholder="gsk_..." autocomplete="new-password">
                    </div>
                    <div class="ap-field" style="grid-column:1/-1">
                        <label class="ap-label">Clave Groq #2 (rotacion)</label>
                        @if($groqKey2Set)<span class="key-set">Guardada</span>@endif
                        <input type="password" class="ap-input" wire:model="orgGroqKey2" placeholder="gsk_..." autocomplete="new-password">
                    </div>
                    <div class="ap-field" style="grid-column:1/-1">
                        <label class="ap-label">Clave Groq #3 (rotacion)</label>
                        @if($groqKey3Set)<span class="key-set">Guardada</span>@endif
                        <input type="password" class="ap-input" wire:model="orgGroqKey3" placeholder="gsk_..." autocomplete="new-password">
                    </div>
                    <div class="ap-field" style="grid-column:1/-1">
                        <label class="ap-label">Clave Google Gemini</label>
                        @if($geminiKeySet)<span class="key-set">Guardada</span>@endif
                        <input type="password" class="ap-input" wire:model="orgGeminiKey" placeholder="AIza..." autocomplete="new-password">
                    </div>
                </div>
                <div class="ap-actions">
                    <button class="ap-btn ap-btn-primary" wire:click="saveOrgKeys" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveOrgKeys">Guardar claves de IA</span>
                        <span wire:loading wire:target="saveOrgKeys">Guardando...</span>
                    </button>
                </div>

                <div class="ap-section" style="border-top:1px solid var(--c-border,#e3e6ea);margin-top:24px;padding-top:24px;display:block">
                    <div class="ap-section-title" style="margin-bottom:4px">Limites del bot</div>
                    <div class="ap-section-desc" style="margin-bottom:16px">Controla cuantos mensajes puede manejar el bot por sesion y cuantas sesiones por dia.</div>
                    <div class="ap-grid">
                        <div class="ap-field">
                            <label class="ap-label">Mensajes maximos por sesion</label>
                            <input type="number" class="ap-input" wire:model="maxMsgPerSession" min="5" max="200">
                        </div>
                        <div class="ap-field">
                            <label class="ap-label">Sesiones de bot por dia</label>
                            <input type="number" class="ap-input" wire:model="maxSessionsPerDay" min="10" max="10000">
                        </div>
                    </div>
                    <div class="ap-actions">
                        <button class="ap-btn ap-btn-ghost" wire:click="saveLimits" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveLimits">Guardar limites</span>
                            <span wire:loading wire:target="saveLimits">Guardando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- ═══ TAB: CORREO ELECTRONICO ═══ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'correo'" x-transition.opacity>

        {{-- Estado --}}
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Estado del correo</div>
                <div class="ap-section-desc">Direccion desde la que tus clientes recibiran los emails de los tickets.</div>
            </div>
            <div>
                @if($smtpEnabled)
                    @if($smtpReady)
                        <div class="ap-notice ap-notice-success"><strong>SMTP configurado</strong><br>Los emails se envian desde <code>{{ $smtpFromAddress }}</code>.</div>
                    @else
                        <div class="ap-notice ap-notice-warn"><strong>SMTP incompleto</strong><br>Activaste tu SMTP pero faltan campos requeridos (Host, Usuario, Remitente).</div>
                    @endif
                @else
                    <div class="ap-notice ap-notice-info"><strong>SMTP del sistema</strong><br>Se usa el servidor por defecto de la plataforma. Activa tu SMTP propio para personalizar el remitente.</div>
                @endif
            </div>
        </div>

        {{-- Notificaciones --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Notificaciones al cliente</div>
                <div class="ap-section-desc">Email automatico al cliente cuando el agente o bot responde a su ticket.</div>
            </div>
            <div>
                <div class="ap-toggle-row">
                    <div>
                        <div class="ap-toggle-label">Notificar al cliente</div>
                        <div class="ap-toggle-sub">Se envia un email con cada respuesta del agente</div>
                    </div>
                    <label class="ap-toggle">
                        <input type="checkbox" wire:model.live="smtpNotificationsEnabled">
                        <span class="ap-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        {{-- SMTP --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Servidor SMTP propio</div>
                <div class="ap-section-desc">Usa tu propio servidor: Gmail, Outlook, Amazon SES, Brevo, o el correo de tu hosting.</div>
            </div>
            <div>
                <div class="ap-toggle-row" style="margin-bottom:16px">
                    <div>
                        <div class="ap-toggle-label">Usar mi SMTP</div>
                        <div class="ap-toggle-sub">Desactiva para usar el servidor de la plataforma</div>
                    </div>
                    <label class="ap-toggle">
                        <input type="checkbox" wire:model.live="smtpEnabled">
                        <span class="ap-slider"></span>
                    </label>
                </div>
                @if($smtpEnabled)
                <div class="ap-grid">
                    <div class="ap-field" style="grid-column:1/-1">
                        <label class="ap-label">Host SMTP</label>
                        <input type="text" class="ap-input" wire:model="smtpHost" placeholder="smtp.gmail.com">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Puerto</label>
                        <input type="number" class="ap-input" wire:model="smtpPort" placeholder="587">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Cifrado</label>
                        <select class="ap-input" wire:model="smtpEncryption">
                            <option value="tls">TLS (recomendado, puerto 587)</option>
                            <option value="ssl">SSL (puerto 465)</option>
                            <option value="none">Sin cifrado</option>
                        </select>
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Usuario SMTP</label>
                        <input type="text" class="ap-input" wire:model="smtpUsername" placeholder="tu@dominio.com" autocomplete="off">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Contrasena SMTP</label>
                        <input type="password" class="ap-input" wire:model="smtpPassword" placeholder="..." autocomplete="new-password">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Email remitente (FROM)</label>
                        <input type="email" class="ap-input" wire:model="smtpFromAddress" placeholder="soporte@tudominio.com">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Nombre remitente</label>
                        <input type="text" class="ap-input" wire:model="smtpFromName" placeholder="Soporte Acme">
                    </div>
                </div>
                @endif
                <div class="ap-actions">
                    <button class="ap-btn ap-btn-primary" wire:click="saveSmtp" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveSmtp">Guardar SMTP</span>
                        <span wire:loading wire:target="saveSmtp">Guardando...</span>
                    </button>
                    @if($smtpReady)
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                        <input type="email" class="ap-input" wire:model="smtpTestEmail" placeholder="email@prueba.com" style="max-width:220px">
                        <button class="ap-btn ap-btn-ghost" wire:click="sendSmtpTest" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="sendSmtpTest">Enviar prueba</span>
                            <span wire:loading wire:target="sendSmtpTest">Enviando...</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- IMAP --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Recibir respuestas por email (IMAP)</div>
                <div class="ap-section-desc">Cuando un cliente responde al email del ticket, el mensaje aparece automaticamente en el panel.</div>
            </div>
            <div>
                <div class="ap-notice ap-notice-info" style="margin-bottom:16px">
                    Usa el mismo correo que el FROM de SMTP. El sistema revisa el buzon periodicamente y detecta las respuestas por el codigo del ticket en el asunto (ej: TKT-00001).
                </div>
                <div class="ap-toggle-row">
                    <div>
                        <div class="ap-toggle-label">Activar recepcion IMAP</div>
                        <div class="ap-toggle-sub">Permite procesar respuestas de clientes desde el buzon de entrada</div>
                    </div>
                    <label class="ap-toggle">
                        <input type="checkbox" wire:model.live="imapEnabled">
                        <span class="ap-slider"></span>
                    </label>
                </div>
                @if($imapEnabled)
                <div class="ap-grid" style="margin-top:14px">
                    <div class="ap-field" style="grid-column:1/-1">
                        <label class="ap-label">Host IMAP</label>
                        <input type="text" class="ap-input" wire:model="imapHost" placeholder="imap.hostinger.com">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Puerto</label>
                        <input type="number" class="ap-input" wire:model="imapPort" placeholder="993">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Cifrado</label>
                        <select class="ap-input" wire:model="imapEncryption">
                            <option value="ssl">SSL (recomendado, puerto 993)</option>
                            <option value="tls">TLS</option>
                            <option value="none">Sin cifrado</option>
                        </select>
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Usuario IMAP</label>
                        <input type="text" class="ap-input" wire:model="imapUsername" placeholder="tu@dominio.com" autocomplete="off">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Contrasena IMAP</label>
                        <input type="password" class="ap-input" wire:model="imapPassword" placeholder="..." autocomplete="new-password">
                    </div>
                    <div class="ap-field" style="grid-column:1/-1">
                        <label class="ap-label">Carpeta a revisar</label>
                        <input type="text" class="ap-input" wire:model="imapFolder" placeholder="INBOX">
                    </div>
                </div>
                @endif
                <div class="ap-actions">
                    <button class="ap-btn ap-btn-primary" wire:click="saveImap" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveImap">Guardar IMAP</span>
                        <span wire:loading wire:target="saveImap">Guardando...</span>
                    </button>
                    @if($imapEnabled && $imapHost)
                    <button class="ap-btn ap-btn-ghost" wire:click="testImapConnection" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="testImapConnection">Probar conexion</span>
                        <span wire:loading wire:target="testImapConnection">Probando...</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- ═══ TAB: LICENCIA ═══ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'licencia'" x-transition.opacity>
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Estado de la licencia</div>
                <div class="ap-section-desc">Verifica que tu licencia Partner este activa.</div>
            </div>
            <div>
                <div class="lic-card">
                    @if($licenseValid)
                        <div class="lic-badge lic-badge-ok">Licencia activa</div>
                    @else
                        <div class="lic-badge lic-badge-err">Licencia no verificada</div>
                    @endif
                    <div class="lic-row">
                        <span class="lic-label">Dominio</span>
                        <span class="lic-value">{{ $installedDomain }}</span>
                    </div>
                    <div class="lic-row">
                        <span class="lic-label">Estado</span>
                        <span class="lic-value">{{ $licenseStatus ?? 'No verificado' }}</span>
                    </div>
                    <div class="lic-row">
                        <span class="lic-label">Ultimo chequeo</span>
                        <span class="lic-value">{{ $licenseCheckedAt ?: 'Nunca' }}</span>
                    </div>
                    <div class="lic-divider"></div>
                    <p class="lic-label" style="margin-bottom:10px">Incluido en tu plan</p>
                    <div class="lic-features">
                        @foreach(['Chat en vivo ilimitado','Bot de IA con claves propias','Widget personalizable','Agentes ilimitados','Integracion Telegram','Tickets por email (IMAP)','Actualizaciones incluidas'] as $feat)
                            <span class="lic-feat"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>{{ $feat }}</span>
                        @endforeach
                    </div>
                    <div class="lic-divider"></div>
                    <button class="ap-btn ap-btn-ghost" wire:click="checkLicense" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="checkLicense">Verificar ahora</span>
                        <span wire:loading wire:target="checkLicense">Verificando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ TAB: AUTOMATIZACION ═══ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'cron'" x-transition.opacity>

        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Tareas automaticas</div>
                <div class="ap-section-desc">
                    Nexova Desk necesita ejecutar ciertas tareas en segundo plano:<br><br>
                    📧 <strong>Revisar tu buzon</strong> para capturar respuestas de clientes a tickets<br>
                    🔑 <strong>Verificar la licencia</strong> una vez al dia<br><br>
                    Configurarlas en tu hosting o usa un servicio externo gratuito.
                </div>
            </div>
            <div>
                <div class="ap-notice ap-notice-info">
                    Para que las respuestas lleguen rapido, el cron de IMAP debe ejecutarse <strong>cada 1 minuto</strong>. Si tu hosting solo permite cada 5 minutos, seguira funcionando con un poco mas de demora.
                </div>
            </div>
        </div>

        <div class="ap-section">
            <div>
                <div class="ap-section-title">Como configurarlo</div>
                <div class="ap-section-desc">Elige segun tu servidor o preferencia.</div>
            </div>
            <div>
                <div class="cron-subtab-row">
                    <button class="cron-subtab" :class="{ active: cronTab === 'cronjob' }" @click="cronTab = 'cronjob'">🌐 Servicio gratuito</button>
                    <button class="cron-subtab" :class="{ active: cronTab === 'hosting' }" @click="cronTab = 'hosting'">🖥️ Mi Hosting</button>
                    <button class="cron-subtab" :class="{ active: cronTab === 'vps' }" @click="cronTab = 'vps'">⚙️ VPS / Servidor</button>
                    <button class="cron-subtab" :class="{ active: cronTab === 'nexova' }" @click="cronTab = 'nexova'" style="position:relative">
                        ⚡ Nexova Fast-Cron
                        <span style="position:absolute;top:-8px;right:-4px;background:#f59e0b;color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:99px">Pronto</span>
                    </button>
                </div>

                {{-- CRON-JOB.ORG --}}
                <div x-show="cronTab === 'cronjob'">
                    <div class="ap-notice ap-notice-success" style="margin-bottom:14px">
                        <strong>Opcion recomendada si no tienes VPS.</strong><br>
                        <a href="https://cron-job.org" target="_blank" style="color:#059669;font-weight:600">Cron-Job.org</a> es gratuito y llama a estas URLs automaticamente.
                    </div>
                    <p style="font-size:12.5px;color:var(--c-sub,#6b7280);margin-bottom:14px;line-height:1.6">
                        Entra a <a href="https://cron-job.org" target="_blank" style="color:#16a34a">cron-job.org</a> → New Cron Job → pega la URL → elige la frecuencia → Guardar. Tipo: <strong>GET</strong>.
                    </p>
                    @foreach([
                        ['icon'=>'📧','color'=>'#3b82f6','name'=>'Revisar correo (IMAP)','desc'=>'Detecta respuestas de clientes y las agrega al ticket','url'=>$appUrl.'/api/cron/imap','freq'=>'Cada 1 minuto'],
                        ['icon'=>'⚡','color'=>'#8b5cf6','name'=>'Worker general','desc'=>'Alternativa: ejecuta todas las tareas de una vez','url'=>$appUrl.'/api/cron/worker','freq'=>'Cada 1 minuto'],
                        ['icon'=>'🔑','color'=>'#f59e0b','name'=>'Verificar licencia','desc'=>'Confirma que la licencia sigue activa','url'=>$appUrl.'/api/cron/license','freq'=>'1 vez al dia'],
                    ] as $ep)
                    <div class="cron-endpoint-card">
                        <div class="cron-endpoint-head">
                            <div class="cron-endpoint-icon" style="background:{{ $ep['color'] }}20;font-size:16px">{{ $ep['icon'] }}</div>
                            <div>
                                <div class="cron-endpoint-name">{{ $ep['name'] }}</div>
                                <div class="cron-endpoint-desc">{{ $ep['desc'] }}</div>
                            </div>
                        </div>
                        <div class="cron-endpoint-body">
                            <div class="cron-url-row">
                                <span class="cron-url-text">{{ $ep['url'] }}</span>
                                <button class="cron-copy-btn" onclick="navigator.clipboard.writeText('{{ $ep['url'] }}').then(()=>{this.textContent='Copiado';setTimeout(()=>this.textContent='Copiar',1500)})">Copiar</button>
                            </div>
                            <div class="cron-freq">Frecuencia: <strong>{{ $ep['freq'] }}</strong></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- HOSTING --}}
                <div x-show="cronTab === 'hosting'">
                    <div class="ap-notice ap-notice-info" style="margin-bottom:14px">
                        Hostings como <strong>Hostinger</strong>, <strong>cPanel</strong>, <strong>Plesk</strong> tienen Cron Jobs en su panel. Agrega cada tarea con el comando de abajo.
                    </div>
                    <p style="font-size:12.5px;color:var(--c-sub,#6b7280);margin-bottom:14px;line-height:1.6">
                        <strong>Hostinger:</strong> Panel → Avanzado → Cron Jobs<br>
                        <strong>cPanel:</strong> Cron Jobs → Add New Cron Job
                    </p>
                    @foreach([
                        ['icon'=>'📧','color'=>'#3b82f6','name'=>'Revisar correo (IMAP)','desc'=>'Detecta y agrega respuestas de clientes','url'=>$appUrl.'/api/cron/imap','freq'=>'*/1 * * * *'],
                        ['icon'=>'⚡','color'=>'#8b5cf6','name'=>'Worker general','desc'=>'Ejecuta todas las tareas automaticas','url'=>$appUrl.'/api/cron/worker','freq'=>'*/1 * * * *'],
                        ['icon'=>'🔑','color'=>'#f59e0b','name'=>'Verificar licencia','desc'=>'Confirma la licencia del sistema','url'=>$appUrl.'/api/cron/license','freq'=>'0 3 * * *'],
                    ] as $ep)
                    <div class="cron-endpoint-card">
                        <div class="cron-endpoint-head">
                            <div class="cron-endpoint-icon" style="background:{{ $ep['color'] }}20;font-size:16px">{{ $ep['icon'] }}</div>
                            <div>
                                <div class="cron-endpoint-name">{{ $ep['name'] }}</div>
                                <div class="cron-endpoint-desc">{{ $ep['desc'] }}</div>
                            </div>
                        </div>
                        <div class="cron-endpoint-body">
                            <div class="cron-url-row">
                                <span class="cron-url-text">curl {{ $ep['url'] }}</span>
                                <button class="cron-copy-btn" onclick="navigator.clipboard.writeText('curl {{ $ep['url'] }}').then(()=>{this.textContent='Copiado';setTimeout(()=>this.textContent='Copiar',1500)})">Copiar</button>
                            </div>
                            <div class="cron-freq">Expresion: <strong><code>{{ $ep['freq'] }}</code></strong></div>
                        </div>
                    </div>
                    @endforeach
                    <div class="ap-notice ap-notice-warn" style="margin-top:10px">
                        Tu panel no acepta <code>curl</code>? Usa la URL directa con metodo <strong>GET</strong>.
                    </div>
                </div>

                {{-- VPS --}}
                <div x-show="cronTab === 'vps'">
                    <div class="ap-notice ap-notice-info" style="margin-bottom:14px">
                        Conectate por SSH → escribe <code>crontab -e</code> → agrega estas lineas → guarda:
                    </div>
                    <div class="cron-endpoint-card">
                        <div class="cron-endpoint-body">
                            <div style="background:var(--c-bg,#f5f6f8);border:1px solid var(--c-border,#e3e6ea);border-radius:7px;padding:14px 16px;font-family:monospace;font-size:12px;color:var(--c-text,#111);line-height:1.8;overflow-x:auto">
                                # Revisar correo cada minuto<br>
                                * * * * * curl -s {{ $appUrl }}/api/cron/imap >/dev/null 2>&1<br><br>
                                # Verificar licencia a las 3:00 AM<br>
                                0 3 * * * curl -s {{ $appUrl }}/api/cron/license >/dev/null 2>&1
                            </div>
                            <button class="cron-copy-btn" style="margin-top:8px"
                                onclick="navigator.clipboard.writeText(`* * * * * curl -s {{ $appUrl }}/api/cron/imap >/dev/null 2>&1\n0 3 * * * curl -s {{ $appUrl }}/api/cron/license >/dev/null 2>&1`).then(()=>{this.textContent='Copiado';setTimeout(()=>this.textContent='Copiar todo',1500)})">
                                Copiar todo
                            </button>
                        </div>
                    </div>
                </div>

                {{-- NEXOVA FAST-CRON --}}
                <div x-show="cronTab === 'nexova'">
                    <div style="text-align:center;padding:48px 24px">
                        <div style="font-size:40px;margin-bottom:16px">⚡</div>
                        <div style="font-size:20px;font-weight:800;color:var(--c-text,#111);margin-bottom:8px">Nexova Fast-Cron</div>
                        <div style="font-size:13px;color:var(--c-sub,#6b7280);max-width:420px;margin:0 auto 20px;line-height:1.7">
                            Nuestro servicio nativo: alta frecuencia, monitoreo automatico, reintentos ante fallos y alertas si una tarea deja de funcionar.
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-bottom:28px">
                            @foreach(['Sin configuracion manual','Cada 30 segundos','Reintentos automaticos','Alertas por email','Historial'] as $feat)
                            <span style="background:var(--c-bg,#f5f6f8);border:1px solid var(--c-border,#e3e6ea);border-radius:8px;padding:6px 14px;font-size:12px;font-weight:500">✓ {{ $feat }}</span>
                            @endforeach
                        </div>
                        <a href="#" style="display:inline-flex;align-items:center;gap:8px;background:#1e293b;color:#f8fafc;padding:10px 22px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;opacity:.6;cursor:not-allowed">
                            Disponible proximamente
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Verificar estado IMAP --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Verificar estado del correo</div>
                <div class="ap-section-desc">Comprueba en cualquier momento si la conexion IMAP esta funcionando.</div>
            </div>
            <div>
                <div class="cron-url-row">
                    <span class="cron-url-text">{{ $appUrl }}/api/cron/imap-status</span>
                    <button class="cron-copy-btn" onclick="navigator.clipboard.writeText('{{ $appUrl }}/api/cron/imap-status').then(()=>{this.textContent='Copiado';setTimeout(()=>this.textContent='Copiar',1500)})">Copiar</button>
                </div>
                <p style="font-size:11.5px;color:var(--c-sub,#6b7280);margin-top:8px;line-height:1.5">
                    Abre esta URL en tu navegador — si ves "Conexion IMAP activa" el buzon esta correctamente configurado.
                </p>
            </div>
        </div>
    </div>
    @endif

</div>
</x-filament-panels::page>
