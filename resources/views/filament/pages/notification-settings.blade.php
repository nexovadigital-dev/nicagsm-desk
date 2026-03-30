<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

/* ─── Wrapper ─── */
.ns-wrap { padding: 32px 36px 64px; max-width: 1040px; }

/* ─── Page title ─── */
.ns-title {
    font-size: 22px; font-weight: 700;
    color: var(--c-text,#111827);
    margin-bottom: 28px;
}

/* ─── Section row (Tremor pattern) ─── */
.ns-section {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 32px;
    padding: 28px 0;
    border-top: 1px solid var(--c-border,#e3e6ea);
}
.ns-section:first-child { border-top: none; padding-top: 0; }
@media (max-width: 720px) {
    .ns-section { grid-template-columns: 1fr; gap: 16px; }
}

.ns-section-title {
    font-size: 14px; font-weight: 600;
    color: var(--c-text,#111827); margin-bottom: 6px;
}
.ns-section-desc {
    font-size: 12.5px; color: var(--c-sub,#6b7280); line-height: 1.6;
}

/* ─── Toggle rows ─── */
.ns-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 13px 0;
    border-bottom: 1px solid var(--c-border,#e3e6ea);
    gap: 24px;
}
.ns-row:first-child { padding-top: 0; }
.ns-row:last-child { border-bottom: none; padding-bottom: 0; }
.ns-row-label { font-size: 13px; font-weight: 500; color: var(--c-text,#111); }
.ns-row-sub   { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 2px; line-height: 1.4; }

/* ─── Toggle switch ─── */
.ns-toggle { position: relative; display: inline-block; width: 36px; height: 20px; flex-shrink: 0; }
.ns-toggle input { opacity: 0; width: 0; height: 0; }
.ns-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: var(--c-border,#e3e6ea);
    border-radius: 99px; transition: background .18s;
}
.ns-slider:before {
    content: ''; position: absolute;
    height: 14px; width: 14px; left: 3px; bottom: 3px;
    background: white; border-radius: 50%; transition: transform .18s;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
}
.ns-toggle input:checked + .ns-slider { background: #22c55e; }
.ns-toggle input:checked + .ns-slider:before { transform: translateX(16px); }

/* ─── Save button ─── */
.ns-footer { margin-top: 12px; padding-top: 24px; border-top: 1px solid var(--c-border,#e3e6ea); display: flex; justify-content: flex-end; }
.ns-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 8px 18px; border-radius: 7px; font-size: 13px; font-weight: 500;
    cursor: pointer; border: 1px solid transparent; font-family: inherit;
}
.ns-btn-primary { background: #1e293b; color: #f8fafc; }
.ns-btn-primary:hover { background: #0f172a; }
</style>

<div class="ns-wrap">

    <div class="ns-title">Notificaciones</div>

    {{-- Panel alerts --}}
    <div class="ns-section">
        <div>
            <div class="ns-section-title">Alertas en panel</div>
            <div class="ns-section-desc">Controla qué conversaciones generan una alerta sonora y badge dentro del panel.</div>
        </div>
        <div>
            <div class="ns-row">
                <div>
                    <div class="ns-row-label">Todas sin leer</div>
                    <div class="ns-row-sub">Cualquier conversación con mensajes nuevos</div>
                </div>
                <label class="ns-toggle">
                    <input type="checkbox" wire:model.live="notifyAllUnread">
                    <span class="ns-slider"></span>
                </label>
            </div>
            <div class="ns-row">
                <div>
                    <div class="ns-row-label">Sin asignar</div>
                    <div class="ns-row-sub">Conversaciones en espera de agente</div>
                </div>
                <label class="ns-toggle">
                    <input type="checkbox" wire:model.live="notifyUnassigned">
                    <span class="ns-slider"></span>
                </label>
            </div>
            <div class="ns-row">
                <div>
                    <div class="ns-row-label">Asignadas a mí</div>
                    <div class="ns-row-sub">El cliente responde en mis conversaciones</div>
                </div>
                <label class="ns-toggle">
                    <input type="checkbox" wire:model.live="notifyAssignedToMe">
                    <span class="ns-slider"></span>
                </label>
            </div>
            <div class="ns-row">
                <div>
                    <div class="ns-row-label">Solo cuando estoy ausente</div>
                    <div class="ns-row-sub">Solo si mi estado es "Ausente"</div>
                </div>
                <label class="ns-toggle">
                    <input type="checkbox" wire:model.live="notifyOnlyWhenOffline">
                    <span class="ns-slider"></span>
                </label>
            </div>
        </div>
    </div>

    {{-- Browser push --}}
    <div class="ns-section">
        <div>
            <div class="ns-section-title">Push del navegador</div>
            <div class="ns-section-desc">Notificaciones nativas del sistema aunque el panel esté en segundo plano o minimizado.</div>
        </div>
        <div>
            <div class="ns-row">
                <div>
                    <div class="ns-row-label">Activar notificaciones push</div>
                    <div class="ns-row-sub">El navegador pedirá permiso la primera vez que lo actives</div>
                </div>
                <label class="ns-toggle">
                    <input type="checkbox" wire:model.live="browserPushEnabled">
                    <span class="ns-slider"></span>
                </label>
            </div>
        </div>
    </div>

    {{-- Email --}}
    <div class="ns-section">
        <div>
            <div class="ns-section-title">Email al agente</div>
            <div class="ns-section-desc">Requiere SMTP configurado en <strong>Email & SMTP</strong>. Los correos se envían desde el remitente de soporte.</div>
        </div>
        <div>
            <div class="ns-row">
                <div>
                    <div class="ns-row-label">Emails de nuevas conversaciones</div>
                    <div class="ns-row-sub">Resumen cuando hay tickets sin atender</div>
                </div>
                <label class="ns-toggle">
                    <input type="checkbox" wire:model.live="emailNotifyEnabled">
                    <span class="ns-slider"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="ns-footer">
        <button class="ns-btn ns-btn-primary" wire:click="save" wire:loading.attr="disabled">
            Guardar preferencias
        </button>
    </div>

</div>
</x-filament-panels::page>
