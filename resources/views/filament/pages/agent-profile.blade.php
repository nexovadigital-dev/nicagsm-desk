<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

/* ─── Wrapper ─── */
.ap-wrap { padding: 32px 36px 64px; max-width: 1040px; }

/* ─── Page title ─── */
.ap-title { font-size: 22px; font-weight: 700; color: var(--c-text,#111827); margin-bottom: 20px; }

/* ─── Tabs scrollable ─── */
.ap-tabs-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; margin-bottom: 32px; scrollbar-width: none; }
.ap-tabs-scroll::-webkit-scrollbar { display: none; }
.ap-tabs { display: flex; gap: 0; border-bottom: 1px solid var(--c-border,#e3e6ea); min-width: max-content; }
.ap-tab {
    padding: 8px 16px 10px; font-size: 13px; font-weight: 500;
    color: var(--c-sub,#6b7280); background: none; border: none;
    border-bottom: 2px solid transparent; margin-bottom: -1px;
    cursor: pointer; font-family: inherit; white-space: nowrap;
    transition: color .12s, border-color .12s;
}
.ap-tab:hover { color: var(--c-text,#111827); }
.ap-tab.active { color: var(--c-text,#111827); font-weight: 600; border-bottom-color: #22c55e; }

/* ─── Section row ─── */
.ap-section {
    display: grid; grid-template-columns: 240px 1fr; gap: 32px;
    padding: 28px 0; border-top: 1px solid var(--c-border,#e3e6ea);
}
.ap-section:first-child, .ap-section.no-top { border-top: none; padding-top: 0; }
@media (max-width: 720px) { .ap-section { grid-template-columns: 1fr; gap: 16px; } }
.ap-section-title { font-size: 14px; font-weight: 600; color: var(--c-text,#111827); margin-bottom: 6px; }
.ap-section-desc { font-size: 12.5px; color: var(--c-sub,#6b7280); line-height: 1.6; }

/* ─── Form fields ─── */
.ap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.ap-grid.single { grid-template-columns: 1fr; }
@media (max-width: 560px) { .ap-grid { grid-template-columns: 1fr; } }
.ap-field { display: flex; flex-direction: column; gap: 5px; }
.ap-label { font-size: 11.5px; font-weight: 500; color: var(--c-sub,#6b7280); }
.ap-input, .ap-select {
    background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 7px; color: var(--c-text,#111); font-size: 13px;
    padding: 8px 12px; outline: none; width: 100%; font-family: inherit;
    transition: border-color .12s; box-sizing: border-box;
}
.ap-input:focus, .ap-select:focus { border-color: #16a34a; }
.ap-input::placeholder { color: var(--c-sub); opacity: .5; }

/* ─── Toggle ─── */
.ap-toggle-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.ap-toggle { position: relative; display: inline-block; width: 38px; height: 21px; flex-shrink: 0; }
.ap-toggle input { opacity: 0; width: 0; height: 0; }
.ap-slider { position: absolute; cursor: pointer; inset: 0; background: var(--c-border,#e3e6ea); border-radius: 99px; transition: background .2s; }
.ap-slider:before { content:''; position: absolute; height: 15px; width: 15px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: transform .2s; }
.ap-toggle input:checked + .ap-slider { background: #22c55e; }
.ap-toggle input:checked + .ap-slider:before { transform: translateX(17px); }
.ap-toggle-label { font-size: 13px; font-weight: 500; color: var(--c-text,#111); }
.ap-toggle-sub { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 2px; }

/* ─── Availability ─── */
.ap-status-row { display: flex; flex-direction: column; gap: 2px; margin-top: 2px; }
.ap-status-opt {
    display: flex; align-items: center; gap: 9px; padding: 8px 12px;
    border-left: 2px solid transparent; border-top: none; border-right: none; border-bottom: none;
    background: transparent; cursor: pointer; font-family: inherit;
    transition: background .12s, border-color .12s; width: 100%; text-align: left; border-radius: 0 6px 6px 0;
}
.ap-status-opt:hover { background: var(--c-bg,#f5f6f8); }
.ap-status-opt.active-online  { border-left-color: #22c55e; background: var(--c-bg,#f5f6f8); }
.ap-status-opt.active-busy    { border-left-color: #f59e0b; background: var(--c-bg,#f5f6f8); }
.ap-status-opt.active-offline { border-left-color: #9ca3af; background: var(--c-bg,#f5f6f8); }
.ap-status-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.ap-status-dot.online  { background: #22c55e; }
.ap-status-dot.busy    { background: #f59e0b; }
.ap-status-dot.offline { background: #9ca3af; }
.ap-status-text { font-size: 13px; font-weight: 500; color: var(--c-sub,#6b7280); flex: 1; transition: color .12s; }
.ap-status-opt.active-online .ap-status-text,
.ap-status-opt.active-busy .ap-status-text,
.ap-status-opt.active-offline .ap-status-text { color: var(--c-text,#111827); font-weight: 600; }

/* ─── Buttons ─── */
.ap-actions { margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; }
.ap-btn {
    display: inline-flex; align-items: center; gap: 5px; padding: 8px 18px;
    border-radius: 7px; font-size: 13px; font-weight: 500; cursor: pointer;
    transition: background .1s; border: 1px solid transparent; font-family: inherit; line-height: 1;
}
.ap-btn-primary { background: #1e293b; color: #f8fafc; }
.ap-btn-primary:hover { background: #0f172a; }
.ap-btn-primary:disabled { opacity:.5; cursor:default; }
.ap-btn-danger  { background: rgba(239,68,68,.08); color: #ef4444; border-color: rgba(239,68,68,.2); }
.ap-btn-danger:hover { background: rgba(239,68,68,.14); }
.ap-btn-ghost   { background: transparent; color: var(--c-sub,#6b7280); border-color: var(--c-border,#e3e6ea); }
.ap-btn-ghost:hover { background: var(--c-surf2,#f0f2f5); }
.ap-btn-success { background: rgba(34,197,94,.08); color: #16a34a; border-color: rgba(34,197,94,.2); }

/* ─── Avatar ─── */
.ap-avatar-zone { display: flex; align-items: center; gap: 16px; }
.ap-avatar-circle {
    width: 64px; height: 64px; border-radius: 12px; background: #1e293b;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; font-weight: 700; color: #fff;
    flex-shrink: 0; overflow: hidden; cursor: pointer; position: relative;
}
.ap-avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
.ap-avatar-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,.45);
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
.ap-avatar-name { font-size: 14px; font-weight: 600; color: var(--c-text,#111827); margin-bottom: 4px; }
.ap-avatar-actions { display: flex; gap: 10px; align-items: center; }
.ap-upload-link { font-size: 12px; color: #16a34a; background: none; border: none; cursor: pointer; font-family: inherit; padding: 0; transition: opacity .12s; }
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

/* ─── Notice boxes ─── */
.ap-notice { padding: 11px 14px; border-radius: 8px; font-size: 12px; line-height: 1.6; }
.ap-notice-info    { background: rgba(59,130,246,.07); border: 1px solid rgba(59,130,246,.2); color: #1d4ed8; }
.ap-notice-success { background: rgba(5,150,105,.07); border: 1px solid rgba(5,150,105,.2); color: #059669; }
.ap-notice-warn    { background: rgba(245,158,11,.07); border: 1px solid rgba(245,158,11,.25); color: #92400e; }
.ap-notice-error   { background: rgba(239,68,68,.06); border: 1px solid rgba(239,68,68,.2); color: #b91c1c; }
.ap-notice code    { background: rgba(0,0,0,.06); padding: 1px 5px; border-radius: 4px; font-size: 11.5px; }
.ap-notice-muted   { background: rgba(100,116,139,.07); border: 1px solid rgba(100,116,139,.2); color: var(--c-sub,#6b7280); }

/* ─── AI Keys ─── */
.ak-card { background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e3e6ea); border-radius: 10px; overflow: hidden; }
.ak-card-head { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--c-border,#e3e6ea); background: var(--c-bg,#f9fafb); }
.ak-provider-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #fff; flex-shrink: 0; }
.ak-provider-name { font-size: 13px; font-weight: 700; color: var(--c-text,#111); }
.ak-provider-model { font-size: 11px; color: var(--c-sub,#6b7280); margin-top: 1px; }
.ak-card-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 12px; }
.ak-field { display: flex; flex-direction: column; gap: 5px; }
.ak-field-head { display: flex; align-items: center; justify-content: space-between; }
.ak-label { font-size: 10.5px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .05em; }
.ak-set-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 99px; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.ak-unset-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 99px; background: var(--c-bg,#f5f6f8); color: var(--c-sub,#6b7280); border: 1px solid var(--c-border,#e3e6ea); }
.ak-input-wrap { position: relative; }
.ak-input { width: 100%; box-sizing: border-box; background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px; color: var(--c-text,#111); font-size: 12.5px; font-family: monospace; padding: 8px 36px 8px 11px; outline: none; transition: border-color .12s; }
.ak-input:focus { border-color: #16a34a; background: #fff; }
.ak-eye { position: absolute; right: 9px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--c-sub,#9ca3af); padding: 0; display: flex; transition: color .1s; }
.ak-eye:hover { color: var(--c-text,#374151); }
.ak-divider { height: 1px; background: var(--c-border,#e3e6ea); margin: 4px 0; }
.ak-hints { font-size: 11px; color: var(--c-sub,#9ca3af); margin: 0; line-height: 1.5; }
.ak-limits-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; align-items: end; }
@media (max-width: 560px) { .ak-limits-grid { grid-template-columns: 1fr; } }

/* ─── Licencia ─── */
.lic-card { background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e3e6ea); border-radius: 12px; padding: 28px 28px 24px; display: flex; flex-direction: column; gap: 20px; }
.lic-head { display: flex; align-items: center; gap: 16px; }
.lic-icon-wrap { width: 52px; height: 52px; border-radius: 14px; background: #f0fdf4; border: 1px solid #bbf7d0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.lic-icon-wrap.error { background: #fef2f2; border-color: #fecaca; }
.lic-icon-wrap.warn  { background: #fffbeb; border-color: #fde68a; }
.lic-plan { font-size: 22px; font-weight: 800; color: var(--c-text,#111); letter-spacing: -.02em; }
.lic-sub  { font-size: 12.5px; color: var(--c-sub,#6b7280); margin-top: 2px; }
.lic-divider { height: 1px; background: var(--c-border,#e3e6ea); }
.lic-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
.lic-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--c-sub,#6b7280); }
.lic-value { font-size: 13px; font-weight: 600; color: var(--c-text,#111); font-family: monospace; }
.lic-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 99px; font-size: 11px; font-weight: 700; }
.lic-badge-ok   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.lic-badge-err  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.lic-badge-warn { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.lic-features { display: flex; flex-wrap: wrap; gap: 10px; }
.lic-feat { display: inline-flex; align-items: center; gap: 6px; background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 8px; padding: 7px 13px; font-size: 12px; font-weight: 500; color: var(--c-text,#111); }
.lic-feat svg { color: #15803d; }

/* ─── Cron ─── */
.cron-subtab-row { display: flex; gap: 0; margin-bottom: 20px; border: 1px solid var(--c-border,#e3e6ea); border-radius: 8px; overflow: hidden; }
.cron-subtab { flex: 1; padding: 8px 12px; font-size: 12.5px; font-weight: 500; background: transparent; border: none; cursor: pointer; font-family: inherit; color: var(--c-sub,#6b7280); transition: background .12s, color .12s; text-align: center; }
.cron-subtab:not(:last-child) { border-right: 1px solid var(--c-border,#e3e6ea); }
.cron-subtab.active { background: #1e293b; color: #f8fafc; }
.cron-endpoint-card { background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e3e6ea); border-radius: 10px; overflow: hidden; margin-bottom: 12px; }
.cron-endpoint-head { display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: var(--c-bg,#f9fafb); border-bottom: 1px solid var(--c-border,#e3e6ea); }
.cron-endpoint-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.cron-endpoint-name { font-size: 13px; font-weight: 700; color: var(--c-text,#111); }
.cron-endpoint-desc { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 1px; }
.cron-endpoint-body { padding: 12px 16px; }
.cron-url-row { display: flex; align-items: center; gap: 8px; background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px; padding: 8px 12px; }
.cron-url-text { flex: 1; font-family: monospace; font-size: 12px; color: var(--c-text,#111); word-break: break-all; }
.cron-copy-btn { background: #1e293b; color: #f8fafc; border: none; border-radius: 6px; padding: 5px 12px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: inherit; white-space: nowrap; transition: background .1s; flex-shrink: 0; }
.cron-copy-btn:hover { background: #0f172a; }
.cron-freq { font-size: 11px; color: var(--c-sub,#6b7280); margin-top: 6px; }
.cron-freq strong { color: var(--c-text,#111); }
</style>

@php
    $appUrl = rtrim(config('app.url'), '/');
    $smtpReady = $smtpEnabled && !empty($smtpHost) && !empty($smtpUsername) && !empty($smtpFromAddress);
@endphp

<div class="ap-wrap" x-data="{ tab: 'cuenta', cronTab: 'cronjob' }">

    <div class="ap-title">Configuración Avanzada</div>

    {{-- ─── Tabs (scrollable) ─── --}}
    <div class="ap-tabs-scroll">
        <div class="ap-tabs">
            <button class="ap-tab" :class="{ active: tab === 'cuenta' }" @click="tab = 'cuenta'">Perfil</button>
            @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
            <button class="ap-tab" :class="{ active: tab === 'org' }" @click="tab = 'org'">Organización</button>
            @endif
            <button class="ap-tab" :class="{ active: tab === 'seguridad' }" @click="tab = 'seguridad'">Seguridad</button>
            @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
            <button class="ap-tab" :class="{ active: tab === 'ia' }" @click="tab = 'ia'">Inteligencia Artificial</button>
            <button class="ap-tab" :class="{ active: tab === 'correo' }" @click="tab = 'correo'">Correo Electrónico</button>
            <button class="ap-tab" :class="{ active: tab === 'licencia' }" @click="tab = 'licencia'">Licencia</button>
            <button class="ap-tab" :class="{ active: tab === 'cron' }" @click="tab = 'cron'">Automatización</button>
            @endif
        </div>
    </div>

    {{-- ═══════════════════ TAB: PERFIL ═══════════════════ --}}
    <div x-show="tab === 'cuenta'" x-transition.opacity>

        {{-- Foto de perfil --}}
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Foto de perfil</div>
                <div class="ap-section-desc">Esta foto aparece en el panel y en las conversaciones con los clientes.</div>
            </div>
            <div>
                <input id="ap-file-input" type="file" accept="image/*" wire:model="avatarFile" style="display:none">
                <div class="ap-avatar-zone">
                    <div class="ap-avatar-circle" onclick="document.getElementById('ap-file-input').click()" title="Cambiar foto">
                        @if($currentAvatarUrl)
                            <img src="{{ $currentAvatarUrl }}" alt="avatar">
                        @else
                            {{ strtoupper(substr($profileName ?: 'A', 0, 1)) }}
                        @endif
                        <div class="ap-avatar-loading" wire:loading wire:target="avatarFile"><div class="ap-avatar-loading__ring"></div></div>
                        <div class="ap-avatar-overlay" wire:loading.remove wire:target="avatarFile">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <div>
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
            <div>
                <div class="ap-section-title">Información personal</div>
                <div class="ap-section-desc">Tu nombre y email dentro del panel. El email se usa para el inicio de sesión.</div>
            </div>
            <div>
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
                    <button class="ap-btn ap-btn-primary" wire:click="saveProfile" wire:loading.attr="disabled">Guardar cambios</button>
                </div>
            </div>
        </div>

        {{-- Estado de presencia --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Estado de presencia</div>
                <div class="ap-section-desc">Indica a tu equipo si estás disponible para atender conversaciones.</div>
            </div>
            <div>
                <div class="ap-status-row">
                    <button type="button" class="ap-status-opt {{ $availability === 'online' ? 'active-online' : '' }}" wire:click="$set('availability','online')">
                        <span class="ap-status-dot online"></span><span class="ap-status-text">En línea</span>
                    </button>
                    <button type="button" class="ap-status-opt {{ $availability === 'busy' ? 'active-busy' : '' }}" wire:click="$set('availability','busy')">
                        <span class="ap-status-dot busy"></span><span class="ap-status-text">Ocupado</span>
                    </button>
                    <button type="button" class="ap-status-opt {{ $availability === 'offline' ? 'active-offline' : '' }}" wire:click="$set('availability','offline')">
                        <span class="ap-status-dot offline"></span><span class="ap-status-text">Ausente</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ TAB: ORGANIZACIÓN ═══════════════════ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'org'" x-transition.opacity>
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Datos de la organización</div>
                <div class="ap-section-desc">Nombre e información pública de tu empresa.</div>
            </div>
            <div>
                <div class="ap-grid">
                    <div class="ap-field">
                        <label class="ap-label">Nombre de la organización</label>
                        <input type="text" wire:model="orgName" class="ap-input" placeholder="Mi Empresa S.A.">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Sitio web</label>
                        <input type="url" wire:model="orgWebsite" class="ap-input" placeholder="https://miempresa.com">
                    </div>
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
                    <button class="ap-btn ap-btn-primary" wire:click="saveOrg" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveOrg">Guardar organización</span>
                        <span wire:loading wire:target="saveOrg">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="ap-section">
            <div>
                <div class="ap-section-title">Zona horaria</div>
                <div class="ap-section-desc">Afecta las fechas de chats, tickets y visitantes en todo el panel.</div>
            </div>
            <div>
                @php
                    $tzGroups = [];
                    foreach (\DateTimeZone::listIdentifiers() as $tz) {
                        $parts = explode('/', $tz, 2);
                        $tzGroups[$parts[0]][] = $tz;
                    }
                    $priority = ['America', 'Europe', 'Asia', 'Africa', 'Pacific', 'Australia'];
                    $sorted = [];
                    foreach ($priority as $p) { if (isset($tzGroups[$p])) $sorted[$p] = $tzGroups[$p]; }
                    foreach ($tzGroups as $g => $list) { if (!isset($sorted[$g])) $sorted[$g] = $list; }
                @endphp
                <div class="ap-field" style="max-width:380px">
                    <label class="ap-label">Zona horaria de la organización</label>
                    <select wire:model="orgTimezone" class="ap-input" style="cursor:pointer">
                        @foreach($sorted as $group => $zones)
                        <optgroup label="{{ $group }}">
                            @foreach($zones as $tz)
                            <option value="{{ $tz }}" @selected($orgTimezone === $tz)>
                                {{ str_replace(['_', '/'], [' ', ' / '], $tz) }}
                                (UTC{{ (new \DateTime('now', new \DateTimeZone($tz)))->format('P') }})
                            </option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                    <p style="font-size:11.5px;color:var(--nx-muted);margin-top:6px">
                        Hora actual: <strong>{{ \Carbon\Carbon::now($orgTimezone ?: 'America/Managua')->format('d/m/Y H:i') }}</strong>
                    </p>
                </div>
                <div class="ap-actions">
                    <button class="ap-btn ap-btn-primary" wire:click="saveOrg" wire:loading.attr="disabled">Guardar zona horaria</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════ TAB: SEGURIDAD ═══════════════════ --}}
    <div x-show="tab === 'seguridad'" x-transition.opacity>
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Cambiar contraseña</div>
                <div class="ap-section-desc">Usa una contraseña de al menos 8 caracteres. Evita contraseñas comunes.</div>
            </div>
            <div>
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
                    <button class="ap-btn ap-btn-primary" wire:click="savePassword" wire:loading.attr="disabled">Actualizar contraseña</button>
                </div>
            </div>
        </div>

        <div class="ap-section">
            <div>
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
            <div>
                @if($this->tfaEnabled)
                    <p style="font-size:12.5px;color:var(--c-sub);margin-bottom:14px;line-height:1.6">Introduce el código de tu app para desactivar el 2FA.</p>
                    <div class="ap-row">
                        <input type="text" class="ap-input" style="width:160px;letter-spacing:.2em;font-family:monospace" wire:model="tfaDisableCode" placeholder="000000" maxlength="6">
                        <button class="ap-btn ap-btn-danger" wire:click="disableTwoFactor">Desactivar 2FA</button>
                    </div>
                @else
                    @if(!$showQr)
                        <button class="ap-btn ap-btn-primary" wire:click="initTwoFactor">Activar 2FA</button>
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
                            <input type="text" class="ap-input" style="width:160px;letter-spacing:.2em;font-family:monospace" wire:model="tfaCode" placeholder="000000" maxlength="6">
                            <button class="ap-btn ap-btn-success" wire:click="confirmTwoFactor">Confirmar y activar</button>
                            <button class="ap-btn ap-btn-ghost" wire:click="$set('showQr', false)">Cancelar</button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════ TAB: INTELIGENCIA ARTIFICIAL ═══════════════════ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'ia'" x-transition.opacity>
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Groq — Proveedor principal</div>
                <div class="ap-section-desc">Hasta 3 claves con rotación automática. Si la primera falla, el sistema usa la siguiente.</div>
            </div>
            <div>
                <div class="ak-card">
                    <div class="ak-card-head">
                        <div class="ak-provider-icon" style="background:#F55036">G</div>
                        <div>
                            <div class="ak-provider-name">Groq</div>
                            <div class="ak-provider-model">llama-3.3-70b-versatile · rotación automática</div>
                        </div>
                    </div>
                    <div class="ak-card-body">
                        @foreach([['orgGroqKey','groqKey1Set','Clave 1 — Principal','gsk_... (principal)'],['orgGroqKey2','groqKey2Set','Clave 2 — Fallback','gsk_... (opcional)'],['orgGroqKey3','groqKey3Set','Clave 3 — Fallback','gsk_... (opcional)']] as [$field,$setField,$label,$ph])
                        <div class="ak-field" x-data="{show:false}">
                            <div class="ak-field-head">
                                <span class="ak-label">{{ $label }}</span>
                                @if($$setField)
                                    <span class="ak-set-badge"><svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>Configurada</span>
                                @else
                                    <span class="ak-unset-badge">Sin configurar</span>
                                @endif
                            </div>
                            <div class="ak-input-wrap">
                                <input :type="show?'text':'password'" wire:model="{{ $field }}" class="ak-input" placeholder="{{ $ph }}">
                                <button type="button" class="ak-eye" @click="show=!show" tabindex="-1">
                                    <svg x-show="!show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>
                        @if(!$loop->last)<div class="ak-divider"></div>@endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="ap-section">
            <div>
                <div class="ap-section-title">Google Gemini — Fallback</div>
                <div class="ap-section-desc">Se usa si Groq no está disponible o las claves han expirado.</div>
            </div>
            <div>
                <div class="ak-card">
                    <div class="ak-card-head">
                        <div class="ak-provider-icon" style="background:#4285F4">G</div>
                        <div>
                            <div class="ak-provider-name">Google Gemini</div>
                            <div class="ak-provider-model">gemini-1.5-flash · fallback secundario</div>
                        </div>
                    </div>
                    <div class="ak-card-body">
                        <div class="ak-field" x-data="{show:false}">
                            <div class="ak-field-head">
                                <span class="ak-label">Google AI API Key</span>
                                @if($geminiKeySet)
                                    <span class="ak-set-badge"><svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>Configurada</span>
                                @else
                                    <span class="ak-unset-badge">Sin configurar</span>
                                @endif
                            </div>
                            <div class="ak-input-wrap">
                                <input :type="show?'text':'password'" wire:model="orgGeminiKey" class="ak-input" placeholder="AIza... (dejar vacío para mantener actual)">
                                <button type="button" class="ak-eye" @click="show=!show" tabindex="-1">
                                    <svg x-show="!show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ap-actions" style="margin-top:16px">
                    <button class="ap-btn ap-btn-primary" wire:click="saveOrgKeys" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveOrgKeys">Guardar claves</span>
                        <span wire:loading wire:target="saveOrgKeys">Guardando...</span>
                    </button>
                </div>
                <p class="ak-hints" style="margin-top:7px;text-align:right">Las claves se almacenan cifradas. Deja los campos vacíos para mantener las actuales.</p>
            </div>
        </div>

        <div class="ap-section">
            <div>
                <div class="ap-section-title">Límites del bot</div>
                <div class="ap-section-desc">Controla el consumo de tokens para evitar gastos innecesarios.</div>
            </div>
            <div>
                <div class="ak-limits-grid" style="margin-bottom:14px">
                    <div class="ak-field">
                        <label class="ak-label">Máx. mensajes por sesión</label>
                        <input type="number" wire:model="maxMsgPerSession" min="5" max="200" class="ap-input">
                        <p class="ak-hints">Mín. 5 · Máx. 200</p>
                    </div>
                    <div class="ak-field">
                        <label class="ak-label">Máx. sesiones con bot por día</label>
                        <input type="number" wire:model="maxSessionsPerDay" min="10" max="10000" class="ap-input">
                        <p class="ak-hints">Mín. 10 · Máx. 10,000</p>
                    </div>
                </div>
                <button class="ap-btn ap-btn-primary" wire:click="saveLimits" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveLimits">Guardar límites</span>
                    <span wire:loading wire:target="saveLimits">Guardando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════ TAB: CORREO ELECTRÓNICO ═══════════════════ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'correo'" x-transition.opacity>

        {{-- Estado actual --}}
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Estado del correo</div>
                <div class="ap-section-desc">Dirección desde la que tus clientes recibirán los emails de los tickets.</div>
            </div>
            <div>
                @if($smtpEnabled)
                    @if($smtpReady)
                        <div class="ap-notice ap-notice-success"><strong>SMTP configurado ✓</strong><br>Los emails se envían desde <code>{{ $smtpFromAddress }}</code> usando tu servidor SMTP.</div>
                    @else
                        <div class="ap-notice ap-notice-warn"><strong>SMTP incompleto</strong><br>Activaste "Usar mi SMTP" pero faltan campos requeridos (Host, Usuario, Remitente) abajo.</div>
                    @endif
                @else
                    <div class="ap-notice ap-notice-info"><strong>SMTP del sistema</strong><br>Se utilizará el servidor de correo por defecto de la plataforma. Activa tu SMTP propio para mayor personalización.</div>
                @endif
            </div>
        </div>

        {{-- Notificaciones --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Notificaciones al cliente</div>
                <div class="ap-section-desc">Email automático al cliente cuando el agente o bot responde a su ticket.</div>
            </div>
            <div>
                <div class="ap-toggle-row">
                    <div>
                        <div class="ap-toggle-label">Notificar al cliente</div>
                        <div class="ap-toggle-sub">Se envía un email con cada respuesta del agente o bot</div>
                    </div>
                    <label class="ap-toggle">
                        <input type="checkbox" wire:model.live="smtpNotificationsEnabled">
                        <span class="ap-slider"></span>
                    </label>
                </div>
                @if($smtpNotificationsEnabled && $smtpEnabled && !$smtpReady)
                    <div class="ap-notice ap-notice-error" style="margin-top:10px">Las notificaciones están activadas pero la configuración SMTP está incompleta. Los emails no se enviarán hasta completarla.</div>
                @endif
            </div>
        </div>

        {{-- SMTP --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Servidor SMTP propio</div>
                <div class="ap-section-desc">Usa tu propio servidor: Gmail, Outlook, Amazon SES, Brevo, o el correo de tu hosting.</div>
            </div>
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
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
                        <input type="text" class="ap-input" wire:model="smtpHost" placeholder="smtp.gmail.com  ó  mail.tudominio.com">
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
                        <label class="ap-label">Contraseña SMTP</label>
                        <input type="password" class="ap-input" wire:model="smtpPassword" placeholder="••••••••" autocomplete="new-password">
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
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar SMTP
                    </button>
                    @if($smtpReady)
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:0">
                        <input type="email" class="ap-input" wire:model="smtpTestEmail" placeholder="email@prueba.com" style="max-width:220px">
                        <button class="ap-btn ap-btn-ghost" wire:click="sendSmtpTest" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="sendSmtpTest">Enviar prueba</span>
                            <span wire:loading wire:target="sendSmtpTest">Enviando…</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- IMAP --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Buzón de entrada (IMAP)</div>
                <div class="ap-section-desc">Cuando el cliente responde al email de un ticket, el mensaje se añade automáticamente al hilo en el panel.</div>
            </div>
            <div>
                <div class="ap-notice ap-notice-info" style="margin-bottom:16px">
                    <strong>¿Cómo funciona?</strong><br>
                    1. El cliente responde al email del ticket<br>
                    2. El sistema detecta <code>TKT-00001</code> en el asunto<br>
                    3. El mensaje aparece en el hilo del panel<br>
                    4. Si el ticket estaba cerrado, se envía aviso automático al cliente<br><br>
                    <strong>Recomendado:</strong> usa la <strong>misma cuenta</strong> de email para SMTP e IMAP.
                </div>

                <div class="ap-toggle-row" style="margin-bottom:16px">
                    <div>
                        <div class="ap-toggle-label">Recibir respuestas por email</div>
                        <div class="ap-toggle-sub">Las respuestas de clientes se añaden al ticket automáticamente</div>
                    </div>
                    <label class="ap-toggle">
                        <input type="checkbox" wire:model.live="imapEnabled">
                        <span class="ap-slider"></span>
                    </label>
                </div>

                @if($imapEnabled)
                <div class="ap-grid">
                    <div class="ap-field" style="grid-column:1/-1">
                        <label class="ap-label">Host IMAP</label>
                        <input type="text" class="ap-input" wire:model="imapHost" placeholder="mail.tudominio.com  ó  imap.gmail.com">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Puerto</label>
                        <input type="number" class="ap-input" wire:model="imapPort" placeholder="993">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Cifrado</label>
                        <select class="ap-input" wire:model="imapEncryption">
                            <option value="ssl">SSL (puerto 993, recomendado)</option>
                            <option value="tls">TLS (puerto 143)</option>
                            <option value="none">Sin cifrado</option>
                        </select>
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Usuario IMAP</label>
                        <input type="text" class="ap-input" wire:model="imapUsername" placeholder="soporte@tudominio.com" autocomplete="off">
                    </div>
                    <div class="ap-field">
                        <label class="ap-label">Contraseña IMAP</label>
                        <input type="password" class="ap-input" wire:model="imapPassword" placeholder="••••••••" autocomplete="new-password">
                    </div>
                    <div class="ap-field" style="grid-column:1/-1">
                        <label class="ap-label">Carpeta / Buzón</label>
                        <input type="text" class="ap-input" wire:model="imapFolder" placeholder="INBOX">
                        <span style="font-size:11px;color:var(--c-sub,#6b7280);margin-top:4px">Normalmente <code>INBOX</code>. En algunos proveedores puede ser otra carpeta.</span>
                    </div>
                </div>
                @else
                <div class="ap-notice ap-notice-muted">Activa la recepción IMAP para configurar el servidor.</div>
                @endif

                <div class="ap-actions">
                    <button class="ap-btn ap-btn-primary" wire:click="saveImap" wire:loading.attr="disabled">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar IMAP
                    </button>
                    @if($imapEnabled && !empty($imapHost))
                    <button class="ap-btn ap-btn-ghost" wire:click="testImapConnection" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="testImapConnection">Probar conexión</span>
                        <span wire:loading wire:target="testImapConnection">Conectando…</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════ TAB: LICENCIA ═══════════════════ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'licencia'" x-transition.opacity>
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">Estado de licencia</div>
                <div class="ap-section-desc">Licencia Partner Edition de Nexova Desk. Se verifica automáticamente al cargar.</div>
            </div>
            <div>
                <div class="lic-card">
                    <div class="lic-head">
                        @if($licenseStatus === 'active')
                            <div class="lic-icon-wrap"><svg width="26" height="26" fill="none" stroke="#15803d" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 12c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.249-8.25-3.286z"/></svg></div>
                        @elseif($licenseStatus === 'unreachable')
                            <div class="lic-icon-wrap warn"><svg width="26" height="26" fill="none" stroke="#92400e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg></div>
                        @else
                            <div class="lic-icon-wrap error"><svg width="26" height="26" fill="none" stroke="#b91c1c" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg></div>
                        @endif
                        <div>
                            <div class="lic-plan">Plan Partner Edition</div>
                            <div class="lic-sub">Licencia vitalicia · Nexova Desk</div>
                        </div>
                    </div>
                    <div class="lic-divider"></div>
                    <div style="display:flex;flex-direction:column;gap:12px">
                        <div class="lic-row">
                            <span class="lic-label">Estado</span>
                            @if($licenseStatus === 'active')
                                <span class="lic-badge lic-badge-ok"><svg width="10" height="10" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>Licencia activa</span>
                            @elseif($licenseStatus === 'unreachable')
                                <span class="lic-badge lic-badge-warn">Sin conexión al servidor de licencias</span>
                            @else
                                <span class="lic-badge lic-badge-err">Licencia inactiva o no encontrada</span>
                            @endif
                        </div>
                        <div class="lic-row">
                            <span class="lic-label">Dominio registrado</span>
                            <span class="lic-value">{{ $installedDomain }}</span>
                        </div>
                        <div class="lic-row">
                            <span class="lic-label">Servidor de licencias</span>
                            <span class="lic-value">{{ $platformUrl }}</span>
                        </div>
                        <div class="lic-row">
                            <span class="lic-label">Última verificación</span>
                            <span style="font-size:13px;color:var(--c-sub,#6b7280)">{{ $licenseCheckedAt }}</span>
                        </div>
                    </div>
                    <div class="lic-divider"></div>
                    <div>
                        <p class="lic-label" style="margin-bottom:10px">Incluido en tu plan</p>
                        <div class="lic-features">
                            @foreach(['Chat en vivo ilimitado','Bot de IA con claves propias','Widget personalizable','Agentes ilimitados','Integración Telegram','Tickets por email (IMAP)','Actualizaciones incluidas'] as $feat)
                                <span class="lic-feat"><svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>{{ $feat }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="lic-divider"></div>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        <button class="ap-btn ap-btn-ghost" wire:click="checkLicense" wire:loading.attr="disabled">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            <span wire:loading.remove wire:target="checkLicense">Verificar ahora</span>
                            <span wire:loading wire:target="checkLicense">Verificando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════ TAB: AUTOMATIZACIÓN ═══════════════════ --}}
    @if(auth()->user()->organization_id && in_array(auth()->user()->role, ['owner','admin']))
    <div x-show="tab === 'cron'" x-transition.opacity>
        <div class="ap-section no-top">
            <div>
                <div class="ap-section-title">¿Qué es un Cron Job?</div>
                <div class="ap-section-desc">
                    Los cron jobs son tareas automáticas que se ejecutan en horarios definidos. Nexova Desk los usa para procesar respuestas de email por IMAP y verificar la licencia del sistema.<br><br>
                    Puedes configurarlos desde tu hosting, un VPS, o un servicio externo gratuito como <strong>cron-job.org</strong>.
                </div>
            </div>
            <div>
                <div class="ap-notice ap-notice-info">
                    <strong>Importante:</strong> el cron de IMAP debe correr <strong>cada 1 minuto</strong> para que las respuestas de email aparezcan rápidamente en el panel. Si tu hosting solo permite cada 5 minutos, será un poco más lento pero funciona igual.
                </div>
            </div>
        </div>

        <div class="ap-section">
            <div>
                <div class="ap-section-title">Cómo configurarlos</div>
                <div class="ap-section-desc">Elige el método según tu tipo de servidor.</div>
            </div>
            <div>
                {{-- Sub-tabs --}}
                <div class="cron-subtab-row">
                    <button class="cron-subtab" :class="{ active: cronTab === 'cronjob' }" @click="cronTab = 'cronjob'">🌐 Cron-Job.org (gratis)</button>
                    <button class="cron-subtab" :class="{ active: cronTab === 'hosting' }" @click="cronTab = 'hosting'">🖥️ Hosting / cPanel</button>
                    <button class="cron-subtab" :class="{ active: cronTab === 'vps' }" @click="cronTab = 'vps'">⚙️ VPS / Linux</button>
                </div>

                {{-- CRON-JOB.ORG --}}
                <div x-show="cronTab === 'cronjob'">
                    <p style="font-size:12.5px;color:var(--c-sub,#6b7280);margin-bottom:16px;line-height:1.6">
                        Ve a <a href="https://cron-job.org" target="_blank" style="color:#16a34a">cron-job.org</a>, crea una cuenta gratuita y agrega cada una de estas URLs como un nuevo cron job. Selecciona frecuencia <strong>cada 1 minuto</strong> para IMAP. Tipo de petición: <strong>GET</strong>.
                    </p>

                    @foreach([
                        ['icon'=>'📧','color'=>'#3b82f6','name'=>'Procesar emails IMAP','desc'=>'Recibe respuestas de clientes y las añade al ticket','url'=>$appUrl.'/api/cron/imap','freq'=>'Cada 1 minuto'],
                        ['icon'=>'⚡','color'=>'#8b5cf6','name'=>'Worker (todos los jobs)','desc'=>'Alternativa: ejecuta todo el scheduler de una vez','url'=>$appUrl.'/api/cron/worker','freq'=>'Cada 1 minuto'],
                        ['icon'=>'🔑','color'=>'#f59e0b','name'=>'Verificar licencia','desc'=>'Comprueba que la licencia Partner siga activa','url'=>$appUrl.'/api/cron/license','freq'=>'Una vez al día'],
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
                                <button class="cron-copy-btn" onclick="navigator.clipboard.writeText('{{ $ep['url'] }}').then(()=>{this.textContent='✓';setTimeout(()=>this.textContent='Copiar',1500)})">Copiar</button>
                            </div>
                            <div class="cron-freq">Frecuencia recomendada: <strong>{{ $ep['freq'] }}</strong></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- HOSTING --}}
                <div x-show="cronTab === 'hosting'">
                    <p style="font-size:12.5px;color:var(--c-sub,#6b7280);margin-bottom:16px;line-height:1.6">
                        En <strong>Hostinger hPanel</strong>, ve a <em>Avanzado → Cron Jobs</em>. Usa el comando <code>curl</code> con cada URL. Si tu panel no acepta caracteres especiales, usa el comando simplificado.
                    </p>
                    @foreach([
                        ['icon'=>'📧','color'=>'#3b82f6','name'=>'Procesar emails IMAP','desc'=>'Añade respuestas de clientes al ticket','url'=>$appUrl.'/api/cron/imap','freq'=>'*/1 * * * *'],
                        ['icon'=>'⚡','color'=>'#8b5cf6','name'=>'Worker (todos los jobs)','desc'=>'Ejecuta todo el scheduler completo','url'=>$appUrl.'/api/cron/worker','freq'=>'*/1 * * * *'],
                        ['icon'=>'🔑','color'=>'#f59e0b','name'=>'Verificar licencia','desc'=>'Verifica la licencia Partner','url'=>$appUrl.'/api/cron/license','freq'=>'0 3 * * *'],
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
                                <button class="cron-copy-btn" onclick="navigator.clipboard.writeText('curl {{ $ep['url'] }}').then(()=>{this.textContent='✓';setTimeout(()=>this.textContent='Copiar',1500)})">Copiar</button>
                            </div>
                            <div class="cron-freq">Expresión cron: <strong><code>{{ $ep['freq'] }}</code></strong></div>
                        </div>
                    </div>
                    @endforeach
                    <div class="ap-notice ap-notice-warn" style="margin-top:12px">
                        <strong>Hosting compartido:</strong> algunos paneles no aceptan <code>curl</code> o caracteres especiales. En ese caso usa la URL directa en la URL del cron (sin <code>curl</code>) y selecciona método GET.
                    </div>
                </div>

                {{-- VPS --}}
                <div x-show="cronTab === 'vps'">
                    <p style="font-size:12.5px;color:var(--c-sub,#6b7280);margin-bottom:16px;line-height:1.6">
                        En un VPS con acceso SSH, edita el crontab con <code>crontab -e</code> y agrega las siguientes líneas:
                    </p>
                    <div class="cron-endpoint-card">
                        <div class="cron-endpoint-body">
                            <div style="background:var(--c-bg,#f5f6f8);border:1px solid var(--c-border,#e3e6ea);border-radius:7px;padding:14px 16px;font-family:monospace;font-size:12px;color:var(--c-text,#111);line-height:1.8;overflow-x:auto;">
                                # Procesar emails IMAP (cada minuto)<br>
                                * * * * * curl -s {{ $appUrl }}/api/cron/imap >/dev/null 2>&1<br><br>
                                # Verificar licencia (a las 3:00 am)<br>
                                0 3 * * * curl -s {{ $appUrl }}/api/cron/license >/dev/null 2>&1
                            </div>
                            <button class="cron-copy-btn" style="margin-top:8px" onclick="navigator.clipboard.writeText('* * * * * curl -s {{ $appUrl }}/api/cron/imap >/dev/null 2>&1\n0 3 * * * curl -s {{ $appUrl }}/api/cron/license >/dev/null 2>&1').then(()=>{this.textContent='✓ Copiado';setTimeout(()=>this.textContent='Copiar todo',1500)})">Copiar todo</button>
                        </div>
                    </div>
                    <div class="ap-notice ap-notice-info" style="margin-top:12px">
                        <strong>Alternativa con artisan:</strong> si tienes PHP en el PATH del servidor, puedes usar directamente:<br>
                        <code>* * * * * php {{ base_path() }}/artisan schedule:run >/dev/null 2>&1</code>
                    </div>
                </div>
            </div>
        </div>

        {{-- Diagnóstico --}}
        <div class="ap-section">
            <div>
                <div class="ap-section-title">Diagnóstico IMAP</div>
                <div class="ap-section-desc">Verifica el estado de la conexión IMAP sin procesar mensajes.</div>
            </div>
            <div>
                <div class="cron-url-row">
                    <span class="cron-url-text">{{ $appUrl }}/api/cron/imap-status</span>
                    <button class="cron-copy-btn" onclick="navigator.clipboard.writeText('{{ $appUrl }}/api/cron/imap-status').then(()=>{this.textContent='✓';setTimeout(()=>this.textContent='Copiar',1500)})">Copiar</button>
                </div>
                <p style="font-size:11.5px;color:var(--c-sub,#6b7280);margin-top:8px;line-height:1.5">
                    Abre esta URL en el navegador para ver: si la conexión IMAP es exitosa, cuántos mensajes hay en el buzón, y cuántos están sin leer (pendientes de procesar).
                </p>
            </div>
        </div>
    </div>
    @endif

</div>
</x-filament-panels::page>
