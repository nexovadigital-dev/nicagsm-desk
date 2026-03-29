<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

/* ─── Wrapper ─── */
.ap-wrap { padding: 32px 36px 64px; max-width: 1040px; }

/* ─── Page title ─── */
.ap-title {
    font-size: 22px; font-weight: 700;
    color: var(--c-text,#111827);
    margin-bottom: 20px;
}

/* ─── Tabs ─── */
.ap-tabs {
    display: flex; gap: 0;
    border-bottom: 1px solid var(--c-border,#e3e6ea);
    margin-bottom: 32px;
}
.ap-tab {
    padding: 8px 16px 10px;
    font-size: 13px; font-weight: 500;
    color: var(--c-sub,#6b7280);
    background: none; border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    cursor: pointer; font-family: inherit;
    transition: color .12s, border-color .12s;
}
.ap-tab:hover { color: var(--c-text,#111827); }
.ap-tab.active {
    color: var(--c-text,#111827); font-weight: 600;
    border-bottom-color: #3b82f6;
}

/* ─── Section row (Tremor pattern) ─── */
.ap-section {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 32px;
    padding: 28px 0;
    border-top: 1px solid var(--c-border,#e3e6ea);
}
.ap-section:first-child { border-top: none; padding-top: 0; }
@media (max-width: 720px) {
    .ap-section { grid-template-columns: 1fr; gap: 16px; }
}

.ap-section-info { }
.ap-section-title {
    font-size: 14px; font-weight: 600;
    color: var(--c-text,#111827); margin-bottom: 6px;
}
.ap-section-desc {
    font-size: 12.5px; color: var(--c-sub,#6b7280);
    line-height: 1.6;
}

.ap-section-body { }

/* ─── Form fields ─── */
.ap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.ap-grid.single { grid-template-columns: 1fr; }
@media (max-width: 560px) { .ap-grid { grid-template-columns: 1fr; } }

.ap-field { display: flex; flex-direction: column; gap: 5px; }
.ap-label {
    font-size: 11.5px; font-weight: 500;
    color: var(--c-sub,#6b7280);
}
.ap-input {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 7px; color: var(--c-text,#111);
    font-size: 13px; padding: 8px 12px; outline: none;
    width: 100%; font-family: inherit; transition: border-color .12s;
    box-sizing: border-box;
}
.ap-input:focus { border-color: #3b82f6; }
.ap-input::placeholder { color: var(--c-sub); opacity: .5; }

/* ─── Availability selector ─── */
.ap-status-row { display: flex; flex-direction: column; gap: 2px; margin-top: 2px; }
.ap-status-opt {
    display: flex; align-items: center; gap: 9px;
    padding: 8px 12px;
    border-left: 2px solid transparent;
    border-top: none; border-right: none; border-bottom: none;
    background: transparent;
    cursor: pointer; font-family: inherit;
    transition: background .12s, border-color .12s;
    width: 100%; text-align: left;
    border-radius: 0 6px 6px 0;
}
.ap-status-opt:hover { background: var(--c-bg,#f5f6f8); }
.ap-status-opt.active-online  { border-left-color: #22c55e; background: var(--c-bg,#f5f6f8); }
.ap-status-opt.active-busy    { border-left-color: #f59e0b; background: var(--c-bg,#f5f6f8); }
.ap-status-opt.active-offline { border-left-color: #9ca3af; background: var(--c-bg,#f5f6f8); }
.ap-status-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.ap-status-dot.online  { background: #22c55e; }
.ap-status-dot.busy    { background: #f59e0b; }
.ap-status-dot.offline { background: #9ca3af; }
.ap-status-text {
    font-size: 13px; font-weight: 500;
    color: var(--c-sub,#6b7280); flex: 1; transition: color .12s;
}
.ap-status-opt.active-online .ap-status-text,
.ap-status-opt.active-busy .ap-status-text,
.ap-status-opt.active-offline .ap-status-text {
    color: var(--c-text,#111827); font-weight: 600;
}

/* ─── Buttons ─── */
.ap-actions { margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px; }
.ap-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 8px 18px; border-radius: 7px; font-size: 13px; font-weight: 500;
    cursor: pointer; transition: background .1s; border: 1px solid transparent;
    font-family: inherit; line-height: 1;
}
.ap-btn-primary { background: #1e293b; color: #f8fafc; }
.ap-btn-primary:hover { background: #0f172a; }
.ap-btn-danger  { background: rgba(239,68,68,.08); color: #ef4444; border-color: rgba(239,68,68,.2); }
.ap-btn-danger:hover { background: rgba(239,68,68,.14); }
.ap-btn-ghost   { background: transparent; color: var(--c-sub,#6b7280); border-color: var(--c-border,#e3e6ea); }
.ap-btn-ghost:hover { background: var(--c-surf2,#f0f2f5); }
.ap-btn-success { background: rgba(34,197,94,.08); color: #16a34a; border-color: rgba(34,197,94,.2); }

/* ─── Avatar ─── */
.ap-avatar-zone { display: flex; align-items: center; gap: 16px; }
.ap-avatar-circle {
    width: 64px; height: 64px; border-radius: 12px;
    background: #1e293b;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; font-weight: 700; color: #fff;
    flex-shrink: 0; overflow: hidden; cursor: pointer; position: relative;
}
.ap-avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
.ap-avatar-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.45);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .15s; border-radius: 12px;
}
.ap-avatar-circle:hover .ap-avatar-overlay { opacity: 1; }
@keyframes ap-spin { to { transform: rotate(360deg); } }
.ap-avatar-loading {
    position: absolute; inset: 0; border-radius: 12px;
    background: rgba(10,10,20,.62); z-index: 2;
    display: flex; align-items: center; justify-content: center;
}
.ap-avatar-loading__ring {
    width: 24px; height: 24px; border-radius: 50%;
    border: 2.5px solid rgba(255,255,255,.2);
    border-top-color: #fff; animation: ap-spin .7s linear infinite;
}
.ap-avatar-info { }
.ap-avatar-name { font-size: 14px; font-weight: 600; color: var(--c-text,#111827); margin-bottom: 4px; }
.ap-avatar-actions { display: flex; gap: 10px; align-items: center; }
.ap-upload-link {
    font-size: 12px; color: #3b82f6;
    background: none; border: none; cursor: pointer; font-family: inherit; padding: 0;
    transition: opacity .12s;
}
.ap-upload-link:hover { opacity: .7; }
.ap-upload-link.danger { color: #ef4444; }

/* ─── 2FA ─── */
.ap-2fa-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 9px; border-radius: 5px; font-size: 11px; font-weight: 700; }
.ap-2fa-on  { background: rgba(34,197,94,.08); color: #16a34a; border: 1px solid rgba(34,197,94,.2); }
.ap-2fa-off { background: rgba(239,68,68,.07); color: #ef4444; border: 1px solid rgba(239,68,68,.2); }
.ap-qr-wrap { display: flex; flex-direction: column; align-items: center; gap: 14px; padding: 18px; background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 10px; margin-top: 14px; }
.ap-qr-wrap > svg, .ap-qr-wrap > * > svg { width: 160px !important; height: 160px !important; border-radius: 8px; background: #fff; padding: 8px; }
.ap-secret { font-family: monospace; font-size: 12px; color: #64748b; letter-spacing: .1em; word-break: break-all; text-align: center; }
.ap-qr-hint { font-size: 11px; color: var(--c-sub,#6b7280); text-align: center; line-height: 1.5; max-width: 280px; }
.ap-divider { border: none; border-top: 1px solid var(--c-border,#e3e6ea); margin: 16px 0; }
.ap-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
</style>

<div class="ap-wrap" x-data="{ tab: 'cuenta' }">

    <div class="ap-title">Mi Perfil</div>

    {{-- ─── Tabs ─── --}}
    <div class="ap-tabs">
        <button class="ap-tab" :class="{ active: tab === 'cuenta' }" @click="tab = 'cuenta'">Cuenta</button>
        @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
        <button class="ap-tab" :class="{ active: tab === 'org' }" @click="tab = 'org'">Organización</button>
        @endif
        <button class="ap-tab" :class="{ active: tab === 'seguridad' }" @click="tab = 'seguridad'">Seguridad</button>
    </div>

    {{-- ═══════════════════ TAB: CUENTA ═══════════════════ --}}
    <div x-show="tab === 'cuenta'" x-transition.opacity>

        {{-- Foto de perfil --}}
        <div class="ap-section">
            <div class="ap-section-info">
                <div class="ap-section-title">Foto de perfil</div>
                <div class="ap-section-desc">Esta foto aparece en el panel y en las conversaciones con los clientes.</div>
            </div>
            <div class="ap-section-body">
                <input id="ap-file-input" type="file" accept="image/*" wire:model="avatarFile" style="display:none">
                <div class="ap-avatar-zone">
                    <div class="ap-avatar-circle" onclick="document.getElementById('ap-file-input').click()" title="Cambiar foto">
                        @if($currentAvatarUrl)
                            <img src="{{ $currentAvatarUrl }}" alt="avatar">
                        @else
                            {{ strtoupper(substr($profileName ?: 'A', 0, 1)) }}
                        @endif
                        <div class="ap-avatar-loading" wire:loading wire:target="avatarFile">
                            <div class="ap-avatar-loading__ring"></div>
                        </div>
                        <div class="ap-avatar-overlay" wire:loading.remove wire:target="avatarFile">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ap-avatar-info">
                        <div class="ap-avatar-name">{{ $profileName ?: 'Agente' }}</div>
                        <div class="ap-avatar-actions">
                            <button class="ap-upload-link" onclick="document.getElementById('ap-file-input').click()">Subir foto</button>
                            @if($currentAvatarUrl)
                                <span style="color:var(--c-border);font-size:11px">·</span>
                                <button class="ap-upload-link danger" wire:click="removeAvatar">Eliminar</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Información básica --}}
        <div class="ap-section">
            <div class="ap-section-info">
                <div class="ap-section-title">Información personal</div>
                <div class="ap-section-desc">Tu nombre y email dentro del panel. El email se usa para el inicio de sesión.</div>
            </div>
            <div class="ap-section-body">
                <div class="ap-grid">
                    <div class="ap-field">
                        <label class="ap-label">Nombre completo</label>
                        <input type="text" class="ap-input" wire:model.live="profileName" placeholder="Tu nombre">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Email</label>
                        <input type="email" class="ap-input" wire:model="profileEmail" placeholder="tu@email.com">
                    </div>
                </div>
                <div class="ap-actions">
                    <button class="ap-btn ap-btn-primary" wire:click="saveProfile" wire:loading.attr="disabled">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>

        {{-- Estado de presencia --}}
        <div class="ap-section">
            <div class="ap-section-info">
                <div class="ap-section-title">Estado de presencia</div>
                <div class="ap-section-desc">Indica a tu equipo si estás disponible para atender conversaciones.</div>
            </div>
            <div class="ap-section-body">
                <div class="ap-status-row">
                    <button type="button" class="ap-status-opt {{ $availability === 'online' ? 'active-online' : '' }}" wire:click="$set('availability','online')">
                        <span class="ap-status-dot online"></span>
                        <span class="ap-status-text">En línea</span>
                    </button>
                    <button type="button" class="ap-status-opt {{ $availability === 'busy' ? 'active-busy' : '' }}" wire:click="$set('availability','busy')">
                        <span class="ap-status-dot busy"></span>
                        <span class="ap-status-text">Ocupado</span>
                    </button>
                    <button type="button" class="ap-status-opt {{ $availability === 'offline' ? 'active-offline' : '' }}" wire:click="$set('availability','offline')">
                        <span class="ap-status-dot offline"></span>
                        <span class="ap-status-text">Ausente</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════ TAB: ORGANIZACIÓN ═══════════════════ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'org'" x-transition.opacity>

        <div class="ap-section" style="border-top:none;padding-top:0">
            <div class="ap-section-info">
                <div class="ap-section-title">Datos de la organización</div>
                <div class="ap-section-desc">Nombre e información pública de tu empresa.</div>
            </div>
            <div class="ap-section-body">
                <div class="ap-grid">
                    <div class="ap-field">
                        <label class="ap-label">Nombre de la organización</label>
                        <input type="text" wire:model="orgName" class="ap-input" placeholder="Mi Empresa S.A.">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Sitio web</label>
                        <input type="url" wire:model="orgWebsite" class="ap-input" placeholder="https://miempresa.com">
                    </div>
                </div>
                <div class="ap-actions">
                    <button class="ap-btn ap-btn-primary" wire:click="saveOrg" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveOrg">Guardar organización</span>
                        <span wire:loading wire:target="saveOrg">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="ap-section">
            <div class="ap-section-info">
                <div class="ap-section-title">Email de soporte</div>
                <div class="ap-section-desc">Remitente que verán los clientes en los correos de tickets.</div>
            </div>
            <div class="ap-section-body">
                <div class="ap-grid">
                    <div class="ap-field">
                        <label class="ap-label">Email de soporte</label>
                        <input type="email" wire:model="orgSupportEmail" class="ap-input" placeholder="soporte@miempresa.com">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Nombre del remitente</label>
                        <input type="text" wire:model="orgSupportName" class="ap-input" placeholder="Soporte Mi Empresa">
                    </div>
                </div>
                <div class="ap-actions">
                    <button class="ap-btn ap-btn-primary" wire:click="saveOrg" wire:loading.attr="disabled">Guardar</button>
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- ═══════════════════ TAB: SEGURIDAD ═══════════════════ --}}
    <div x-show="tab === 'seguridad'" x-transition.opacity>

        <div class="ap-section" style="border-top:none;padding-top:0">
            <div class="ap-section-info">
                <div class="ap-section-title">Cambiar contraseña</div>
                <div class="ap-section-desc">Usa una contraseña de al menos 8 caracteres. Evita contraseñas comunes.</div>
            </div>
            <div class="ap-section-body">
                <div class="ap-grid single" style="margin-bottom:12px">
                    <div class="ap-field">
                        <label class="ap-label">Contraseña actual</label>
                        <input type="password" class="ap-input" wire:model="currentPassword" placeholder="••••••••" autocomplete="current-password">
                    </div>
                </div>
                <div class="ap-grid">
                    <div class="ap-field">
                        <label class="ap-label">Nueva contraseña</label>
                        <input type="password" class="ap-input" wire:model="newPassword" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Confirmar contraseña</label>
                        <input type="password" class="ap-input" wire:model="passwordConfirm" placeholder="Repite la contraseña" autocomplete="new-password">
                    </div>
                </div>
                <div class="ap-actions">
                    <button class="ap-btn ap-btn-primary" wire:click="savePassword" wire:loading.attr="disabled">
                        Actualizar contraseña
                    </button>
                </div>
            </div>
        </div>

        <div class="ap-section">
            <div class="ap-section-info">
                <div class="ap-section-title">
                    Autenticación dos factores
                    @if($this->tfaEnabled)
                        <span class="ap-2fa-badge ap-2fa-on" style="margin-left:8px;vertical-align:middle">Activado</span>
                    @else
                        <span class="ap-2fa-badge ap-2fa-off" style="margin-left:8px;vertical-align:middle">Desactivado</span>
                    @endif
                </div>
                <div class="ap-section-desc">Añade una capa extra de seguridad con una app TOTP como Google Authenticator o Aegis.</div>
            </div>
            <div class="ap-section-body">
                @if($this->tfaEnabled)
                    <p style="font-size:12.5px;color:var(--c-sub);margin-bottom:14px;line-height:1.6">
                        Introduce el código de tu app para desactivar el 2FA.
                    </p>
                    <div class="ap-row">
                        <input type="text" class="ap-input" style="width:160px;letter-spacing:.2em;font-family:monospace"
                            wire:model="tfaDisableCode" placeholder="000000" maxlength="6">
                        <button class="ap-btn ap-btn-danger" wire:click="disableTwoFactor">Desactivar 2FA</button>
                    </div>
                @else
                    @if(! $showQr)
                        <button class="ap-btn ap-btn-primary" wire:click="initTwoFactor">
                            Activar 2FA
                        </button>
                    @else
                        <div class="ap-qr-wrap">
                            {!! $qrSvg !!}
                            <p class="ap-qr-hint">Escanea con Google Authenticator, Aegis, Authy, etc.</p>
                            <div>
                                <div style="font-size:10px;color:var(--c-sub);text-align:center;margin-bottom:4px">Clave secreta (manual)</div>
                                <div class="ap-secret">{{ $tfaSecret }}</div>
                            </div>
                        </div>
                        <hr class="ap-divider">
                        <p style="font-size:12.5px;color:var(--c-sub);margin-bottom:12px">Introduce el código de 6 dígitos para confirmar:</p>
                        <div class="ap-row">
                            <input type="text" class="ap-input" style="width:160px;letter-spacing:.2em;font-family:monospace"
                                wire:model="tfaCode" placeholder="000000" maxlength="6">
                            <button class="ap-btn ap-btn-success" wire:click="confirmTwoFactor">Confirmar y activar</button>
                            <button class="ap-btn ap-btn-ghost" wire:click="$set('showQr', false)">Cancelar</button>
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>

</div>
</x-filament-panels::page>
