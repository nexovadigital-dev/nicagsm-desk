<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }
.wl { padding: 20px 0 48px; }
.wl-head { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; }
.wl-head-title { font-size: 17px; font-weight: 700; color: var(--c-text,#111); letter-spacing: -.015em; }
.wl-head-sub   { font-size: 12px; color: var(--c-sub,#6b7280); margin-top: 3px; }

.wl-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 8px; font-size: 13px;
    font-weight: 500; cursor: pointer; border: none;
    background: #1e293b; color: #f8fafc;
    font-family: inherit; text-decoration: none; transition: background .1s;
}
.wl-btn:hover { background: #0f172a; }
.wl-btn-ghost {
    background: var(--c-bg,#f5f6f8); color: var(--c-text,#374151);
    border: 1px solid var(--c-border,#e3e6ea);
}
.wl-btn-ghost:hover { background: var(--c-surf2,#e9ebee); }

.wl-grid { display: flex; flex-direction: column; gap: 10px; }

.wl-card {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 12px;
    padding: 18px 22px;
    display: flex; align-items: center; gap: 16px;
}
.wl-card-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.wl-card-icon svg { color: #64748b; }
.wl-card-info { flex: 1; min-width: 0; }
.wl-card-name   { font-size: 14px; font-weight: 600; color: var(--c-text,#111); }
.wl-card-token  { font-size: 11px; color: var(--c-sub,#9ca3af); font-family: monospace; margin-top: 2px; }
.wl-card-badge  { display: inline-flex; align-items: center; gap: 4px; font-size: 10.5px; font-weight: 600; padding: 2px 8px; border-radius: 99px; margin-top: 5px; }
.wl-badge-on  { background: rgba(59,130,246,.08); color: #2563eb; border: 1px solid rgba(59,130,246,.2); }
.wl-badge-off { background: var(--c-surf2,#f0f2f5); color: var(--c-sub,#6b7280); }
.wl-card-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.wl-icon-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 7px;
    border: 1px solid var(--c-border,#e3e6ea);
    background: var(--c-bg,#f5f6f8); color: var(--c-sub,#6b7280);
    cursor: pointer; transition: background .12s, color .12s;
}
.wl-icon-btn:hover { background: var(--c-surf2,#e9ebee); color: var(--c-text,#111); }
.wl-icon-btn.danger:hover { background: rgba(220,38,38,.07); color: #dc2626; border-color: rgba(220,38,38,.2); }

.wl-empty {
    text-align: center; padding: 60px 20px;
    background: var(--c-surface,#fff);
    border: 1px dashed var(--c-border,#e3e6ea);
    border-radius: 14px;
    color: var(--c-sub,#9ca3af); font-size: 13px; line-height: 1.6;
}
.wl-empty svg { color: var(--c-border,#d1d5db); margin-bottom: 12px; }
</style>

@php $widgets = $this->getWidgets(); @endphp

<div class="wl">

    <div class="wl-head">
        <div>
            <div class="wl-head-title">Mis Widgets</div>
            <div class="wl-head-sub">Crea y gestiona múltiples widgets de chat personalizados</div>
        </div>
        <a href="{{ \App\Filament\Resources\ChatWidgetResource::getUrl('create') }}" class="wl-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo widget
        </a>
    </div>

    <div class="wl-grid">
        @forelse($widgets as $w)
        <div class="wl-card">
            <div class="wl-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div class="wl-card-info">
                <div class="wl-card-name">{{ $w->name }}</div>
                <div class="wl-card-token">Token: {{ $w->token }}</div>
                <span class="wl-card-badge {{ $w->is_active ? 'wl-badge-on' : 'wl-badge-off' }}">
                    <span style="width:5px;height:5px;border-radius:50%;background:currentColor;display:inline-block"></span>
                    {{ $w->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <div class="wl-card-actions">
                {{-- Copiar snippet --}}
                <button type="button" class="wl-icon-btn"
                    title="Copiar código de instalación"
                    x-data
                    @click="navigator.clipboard.writeText(`<script>\n  window.NexovaChatConfig = { apiUrl: '{{ rtrim(config('app.url'),'/') }}', widgetToken: '{{ $w->token }}' };\n<\/script>\n<script src='{{ rtrim(config('app.url'),'/') }}/chat-widget.js' defer><\/script>`); $dispatch('nexova-toast', {type:'success',message:'Código copiado al portapapeles'})">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
                {{-- Toggle activo --}}
                <button type="button" class="wl-icon-btn" title="{{ $w->is_active ? 'Desactivar' : 'Activar' }}"
                    wire:click="toggleActive({{ $w->id }})">
                    @if($w->is_active)
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @endif
                </button>
                {{-- Editar --}}
                <a href="{{ \App\Filament\Resources\ChatWidgetResource::getUrl('edit', ['record' => $w->id]) }}"
                   class="wl-icon-btn" title="Editar widget">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
                {{-- Eliminar --}}
                <button type="button" class="wl-icon-btn danger" title="Eliminar widget"
                    wire:click="delete({{ $w->id }})"
                    wire:confirm="¿Seguro que deseas eliminar este widget? Esta acción no se puede deshacer.">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
        @empty
        <div class="wl-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="40" height="40" style="display:block;margin:0 auto">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <div style="font-weight:600;color:var(--c-text,#374151);margin-bottom:6px">Sin widgets aún</div>
            <div>Crea tu primer widget para empezar a capturar conversaciones.</div>
        </div>
        @endforelse
    </div>

</div>
</x-filament-panels::page>
