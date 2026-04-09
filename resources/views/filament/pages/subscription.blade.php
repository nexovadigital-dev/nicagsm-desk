<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }
.lic-wrap { padding: 32px 36px 64px; max-width: 700px; }
.lic-card {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 12px;
    padding: 28px 28px 24px;
    display: flex; flex-direction: column; gap: 20px;
}
.lic-head { display: flex; align-items: center; gap: 16px; }
.lic-icon-wrap {
    width: 52px; height: 52px; border-radius: 14px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.lic-icon-wrap.error { background: #fef2f2; border-color: #fecaca; }
.lic-icon-wrap.warn  { background: #fffbeb; border-color: #fde68a; }
.lic-plan { font-size: 22px; font-weight: 800; color: var(--c-text,#111); letter-spacing: -.02em; }
.lic-sub  { font-size: 12.5px; color: var(--c-sub,#6b7280); margin-top: 2px; }
.lic-divider { height: 1px; background: var(--c-border,#e3e6ea); }
.lic-row  { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
.lic-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--c-sub,#6b7280); }
.lic-value { font-size: 13px; font-weight: 600; color: var(--c-text,#111); font-family: monospace; }
.lic-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 99px;
    font-size: 11px; font-weight: 700;
}
.lic-badge-ok   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.lic-badge-err  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.lic-badge-warn { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.lic-features { display: flex; flex-wrap: wrap; gap: 10px; }
.lic-feat {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 8px; padding: 7px 13px; font-size: 12px; font-weight: 500;
    color: var(--c-text,#111);
}
.lic-feat svg { color: #15803d; }
.lic-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 8px; font-size: 12.5px;
    font-weight: 500; cursor: pointer; border: 1px solid var(--c-border,#e3e6ea);
    background: transparent; color: var(--c-text,#111); font-family: inherit;
    transition: background .1s;
}
.lic-btn:hover { background: var(--c-bg,#f5f6f8); }
</style>

<div class="lic-wrap">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:6px;letter-spacing:-.02em">Licencia</h1>
    <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:0 0 28px;line-height:1.6">
        Estado de tu licencia Partner Edition de Nexova Desk.
    </p>

    <div class="lic-card">
        {{-- Header --}}
        <div class="lic-head">
            @if($licenseStatus === 'active')
                <div class="lic-icon-wrap">
                    <svg width="26" height="26" fill="none" stroke="#15803d" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 12c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.249-8.25-3.286z"/>
                    </svg>
                </div>
            @elseif($licenseStatus === 'unreachable')
                <div class="lic-icon-wrap warn">
                    <svg width="26" height="26" fill="none" stroke="#92400e" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
            @else
                <div class="lic-icon-wrap error">
                    <svg width="26" height="26" fill="none" stroke="#b91c1c" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                </div>
            @endif
            <div>
                <div class="lic-plan">Plan Partner Edition</div>
                <div class="lic-sub">Licencia vitalicia · Nexova Desk</div>
            </div>
        </div>

        <div class="lic-divider"></div>

        {{-- Rows --}}
        <div style="display:flex;flex-direction:column;gap:12px">
            <div class="lic-row">
                <span class="lic-label">Estado</span>
                @if($licenseStatus === 'active')
                    <span class="lic-badge lic-badge-ok">
                        <svg width="10" height="10" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                        Licencia activa
                    </span>
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

        {{-- Features included --}}
        <div>
            <p class="lic-label" style="margin-bottom:10px">Incluido en tu plan</p>
            <div class="lic-features">
                @foreach([
                    'Chat en vivo ilimitado',
                    'Bot de IA con claves propias',
                    'Widget personalizable',
                    'Agentes ilimitados',
                    'Rotación 3 claves Groq',
                    'Soporte técnico',
                    'Actualizaciones incluidas',
                ] as $feat)
                    <span class="lic-feat">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $feat }}
                    </span>
                @endforeach
            </div>
        </div>

        <div class="lic-divider"></div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <button class="lic-btn" wire:click="checkLicense" wire:loading.attr="disabled">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                <span wire:loading.remove wire:target="checkLicense">Verificar ahora</span>
                <span wire:loading wire:target="checkLicense">Verificando...</span>
            </button>
            <p style="font-size:11px;color:var(--c-sub,#6b7280);margin:0">
                La licencia se verifica automáticamente al cargar esta página.
            </p>
        </div>
    </div>
</div>
</x-filament-panels::page>
