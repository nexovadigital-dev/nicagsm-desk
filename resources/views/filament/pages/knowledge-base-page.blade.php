<x-filament-panels::page>
<style>
.kb-page { display: flex; flex-direction: column; gap: 16px; padding: 32px 36px 64px; max-width: 1040px; }

/* Stats bar */
.kb-stats { display: flex; gap: 12px; flex-wrap: wrap; }
.kb-stat {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 10px;
    padding: 12px 18px;
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 100px;
}
.kb-stat-value { font-size: 22px; font-weight: 700; color: var(--c-text,#111); line-height: 1; }
.kb-stat-label { font-size: 11px; color: var(--c-sub,#6b7280); font-weight: 500; }

/* Toolbar */
.kb-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 10px;
    padding: 12px 16px;
}
.kb-search-wrap { position: relative; flex: 1; min-width: 160px; }
.kb-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--c-sub,#9ca3af); pointer-events: none; }
.kb-search { background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px; color: var(--c-text,#111); font-size: 13px; padding: 7px 10px 7px 32px; outline: none; width: 100%; font-family: inherit; }
.kb-search:focus { border-color: #16a34a; }

.kb-filter-group { display: flex; gap: 4px; background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px; padding: 3px; }
.kb-filter-btn { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: 500; border: none; background: transparent; color: var(--c-sub,#6b7280); cursor: pointer; font-family: inherit; transition: background .1s, color .1s; }
.kb-filter-btn.active { background: var(--c-surface,#fff); color: var(--c-text,#111); box-shadow: 0 1px 3px rgba(0,0,0,.08); }

.kb-toggle-active { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: var(--c-sub,#6b7280); cursor: pointer; font-weight: 500; }

.kb-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 7px; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; font-family: inherit; white-space: nowrap; }
.kb-btn-primary { background: #1e293b; color:#f8fafc; }
.kb-btn-primary:hover { background: #0f172a; }
.kb-btn-ghost { background: transparent; color: var(--c-sub,#6b7280); border-color: var(--c-border,#e3e6ea); }
.kb-btn-ghost:hover { background: var(--c-surf2,#f0f2f5); }
.kb-btn-danger { background: transparent; color: #ef4444; border-color: rgba(239,68,68,.2); font-size: 12px; padding: 5px 10px; }
.kb-btn-danger:hover { background: rgba(239,68,68,.05); }
.kb-btn-sm { padding: 5px 10px; font-size: 12px; }

/* List */
.kb-list { display: flex; flex-direction: column; gap: 8px; }
.kb-item {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 10px;
    padding: 16px 18px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    transition: border-color .15s;
}
.kb-item:hover { border-color: rgba(34,197,94,.3); }
.kb-item.inactive { opacity: .55; }
.kb-item-icon {
    width: 36px; height: 36px; border-radius: 8px;
    background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    color: #64748b; flex-shrink: 0;
}
.kb-item-body { flex: 1; min-width: 0; }
.kb-item-title { font-size: 14px; font-weight: 600; color: var(--c-text,#111); margin-bottom: 4px; }
.kb-item-preview { font-size: 12.5px; color: var(--c-sub,#6b7280); line-height: 1.5; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.kb-item-meta { display: flex; align-items: center; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
.kb-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
.kb-badge-manual   { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
.kb-badge-scrape     { background: rgba(14,165,233,.1); color: #0ea5e9; }
.kb-badge-web_scrape { background: rgba(99,102,241,.1); color: #6366f1; }
.kb-badge-external   { background: rgba(245,158,11,.1); color: #d97706; }
.kb-badge-active   { background: rgba(34,197,94,.1); color: #16a34a; }
.kb-badge-inactive { background: rgba(156,163,175,.1); color: #6b7280; }
.kb-item-date { font-size: 11px; color: var(--c-sub,#9ca3af); }
.kb-item-actions { display: flex; gap: 6px; align-items: center; flex-shrink: 0; }

/* Icon buttons */
.kb-icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--c-border,#e3e6ea); background: transparent; cursor: pointer; color: var(--c-sub,#6b7280); transition: background .1s, color .1s; }
.kb-icon-btn:hover { background: var(--c-surf2,#f0f2f5); color: var(--c-text,#111); }
.kb-icon-btn.danger:hover { background: rgba(239,68,68,.07); color: #ef4444; border-color: rgba(239,68,68,.25); }

/* Form modal overlay */
.kb-modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.45);
    display: flex; align-items: flex-start; justify-content: center;
    z-index: 9999; padding: 40px 16px; overflow-y: auto;
}
.kb-modal {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 14px;
    padding: 28px 30px;
    width: 100%;
    max-width: 640px;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
}
.kb-modal-title { font-size: 16px; font-weight: 700; color: var(--c-text,#111); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
.kb-modal-close { width: 28px; height: 28px; border-radius: 6px; border: none; background: var(--c-bg,#f5f6f8); cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--c-sub,#6b7280); }
.kb-modal-close:hover { background: var(--c-surf2,#e5e7eb); }
.kb-form-field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
.kb-form-label { font-size: 11px; font-weight: 600; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .05em; }
.kb-form-input, .kb-form-textarea, .kb-form-select {
    background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px;
    color: var(--c-text,#111); font-size: 13px; padding: 9px 12px; outline: none;
    width: 100%; font-family: inherit; box-sizing: border-box;
}
.kb-form-input:focus, .kb-form-textarea:focus, .kb-form-select:focus { border-color: #16a34a; }
.kb-form-textarea { resize: vertical; min-height: 150px; line-height: 1.6; }
.kb-form-actions { display: flex; align-items: center; gap: 10px; margin-top: 20px; }
.kb-alert { padding: 10px 14px; border-radius: 7px; font-size: 12px; font-weight: 500; margin-top: 10px; }
.kb-alert-success { background: rgba(5,150,105,.07); border:1px solid rgba(5,150,105,.2); color: #059669; }
.kb-alert-error   { background: rgba(220,38,38,.07); border:1px solid rgba(220,38,38,.2); color: #dc2626; }

/* Toggle */
.kb-toggle { position: relative; display: inline-block; width: 36px; height: 20px; flex-shrink: 0; }
.kb-toggle input { opacity: 0; width: 0; height: 0; }
.kb-slider { position: absolute; cursor: pointer; inset: 0; background: var(--c-border,#e3e6ea); border-radius: 99px; transition: background .2s; }
.kb-slider:before { content:''; position: absolute; height: 14px; width: 14px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: transform .2s; }
.kb-toggle input:checked + .kb-slider { background: #22c55e; }
.kb-toggle input:checked + .kb-slider:before { transform: translateX(16px); }

.kb-empty { text-align: center; padding: 60px 20px; color: var(--c-sub,#9ca3af); font-size: 14px; background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e3e6ea); border-radius: 10px; }
.kb-empty-icon { font-size: 40px; margin-bottom: 12px; }

.fi-page-header, .fi-breadcrumbs { display: none !important; }
</style>

<div class="kb-page">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:4px;letter-spacing:-.02em">Base de conocimiento</h1>

    {{-- ── Stats ── --}}
    <div class="kb-stats">
        <div class="kb-stat">
            <div class="kb-stat-value">{{ $this->stats['total'] }}</div>
            <div class="kb-stat-label">Total artículos</div>
        </div>
        <div class="kb-stat">
            <div class="kb-stat-value">{{ $this->stats['active'] }}</div>
            <div class="kb-stat-label">Activos</div>
        </div>
        <div class="kb-stat">
            <div class="kb-stat-value">{{ $this->stats['manual'] }}</div>
            <div class="kb-stat-label">Manuales</div>
        </div>
        <div class="kb-stat">
            <div class="kb-stat-value">{{ $this->stats['scrape'] }}</div>
            <div class="kb-stat-label">Web scraping</div>
        </div>
        <div class="kb-stat">
            <div class="kb-stat-value">{{ $this->stats['external'] }}</div>
            <div class="kb-stat-label">Externos</div>
        </div>
    </div>

    {{-- ── Toolbar ── --}}
    <div class="kb-toolbar">
        <div class="kb-search-wrap">
            <svg class="kb-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" class="kb-search" wire:model.live.debounce.300ms="search" placeholder="Buscar artículos...">
        </div>

        <div class="kb-filter-group">
            <button type="button" class="kb-filter-btn {{ $filterSource === 'all'      ? 'active' : '' }}" wire:click="$set('filterSource','all')">Todos</button>
            <button type="button" class="kb-filter-btn {{ $filterSource === 'manual'   ? 'active' : '' }}" wire:click="$set('filterSource','manual')">Manual</button>
            <button type="button" class="kb-filter-btn {{ $filterSource === 'scrape'   ? 'active' : '' }}" wire:click="$set('filterSource','scrape')">Scraping</button>
            <button type="button" class="kb-filter-btn {{ $filterSource === 'external' ? 'active' : '' }}" wire:click="$set('filterSource','external')">Externos</button>
        </div>

        {{-- Filtro por canal/widget --}}
        @if($this->widgets->count() > 0)
        <div class="kb-filter-group">
            <button type="button" class="kb-filter-btn {{ $filterChannel === 'all'    ? 'active' : '' }}" wire:click="$set('filterChannel','all')">Todos los canales</button>
            <button type="button" class="kb-filter-btn {{ $filterChannel === 'global' ? 'active' : '' }}" wire:click="$set('filterChannel','global')">Global</button>
            @foreach($this->widgets as $w)
            <button type="button" class="kb-filter-btn {{ $filterChannel === (string)$w->id ? 'active' : '' }}" wire:click="$set('filterChannel','{{ $w->id }}')">{{ $w->name }}</button>
            @endforeach
        </div>
        @endif

        <label class="kb-toggle-active">
            <label class="kb-toggle">
                <input type="checkbox" wire:model.live="filterActive">
                <span class="kb-slider"></span>
            </label>
            Solo activos
        </label>

        <button class="kb-btn kb-btn-primary" wire:click="openCreate">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo artículo
        </button>

        {{-- Escanear sitio web --}}
        @if($this->orgWebsite)
        <button class="kb-btn kb-btn-ghost" wire:click="scrapeOrgWebsite" wire:loading.attr="disabled" wire:target="scrapeOrgWebsite"
            title="Escanear {{ $this->orgWebsite }}">
            <svg wire:loading.remove wire:target="scrapeOrgWebsite" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <svg wire:loading wire:target="scrapeOrgWebsite" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" style="animation:spin 1s linear infinite">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span wire:loading.remove wire:target="scrapeOrgWebsite">Escanear sitio web</span>
            <span wire:loading wire:target="scrapeOrgWebsite">Escaneando...</span>
        </button>
        @if($this->lastWebScrape)
        <span style="font-size:11px;color:var(--c-sub,#9ca3af)">Último escaneo: {{ $this->lastWebScrape }}</span>
        @endif
        @endif
    </div>

    <style>@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}</style>

    {{-- ── Lista ── --}}
    <div class="kb-list">
        @forelse($this->entries as $item)
        <div class="kb-item {{ !$item->is_active ? 'inactive' : '' }}">
            <div class="kb-item-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="kb-item-body">
                <div class="kb-item-title">{{ $item->title }}</div>
                <div class="kb-item-preview">{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 120) }}</div>
                <div class="kb-item-meta">
                    <span class="kb-badge kb-badge-{{ $item->source ?? 'manual' }}">{{ $item->source ?? 'manual' }}</span>
                    <span class="kb-badge {{ $item->is_active ? 'kb-badge-active' : 'kb-badge-inactive' }}">
                        {{ $item->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                    @if($item->widget_id && $item->widget)
                    <span class="kb-badge" style="background:rgba(99,102,241,.1);color:#6366f1">{{ $item->widget->name }}</span>
                    @else
                    <span class="kb-badge" style="background:#f0fdf4;color:#15803d">Global</span>
                    @endif
                    <span class="kb-item-date">{{ $item->updated_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="kb-item-actions">
                <button class="kb-icon-btn" wire:click="toggleActive({{ $item->id }})" title="{{ $item->is_active ? 'Desactivar' : 'Activar' }}">
                    @if($item->is_active)
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    @endif
                </button>
                @if($item->source === 'scrape')
                <button class="kb-icon-btn" wire:click="rescrape({{ $item->id }})" wire:loading.attr="disabled" title="Re-scrapear URL">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
                @endif
                <button class="kb-icon-btn" wire:click="openEdit({{ $item->id }})" title="Editar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button class="kb-icon-btn danger" wire:click="delete({{ $item->id }})" wire:confirm="¿Eliminar este artículo?" title="Eliminar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
        @empty
        <div class="kb-empty">
            <div class="kb-empty-icon">📚</div>
            @if($search)
                Sin resultados para "{{ $search }}"
            @else
                No hay artículos en la base de conocimiento.<br>
                <small style="font-size:12px">Haz clic en "Nuevo artículo" o usa <code>php artisan nexova:scrape-url</code> para indexar una URL.</small>
            @endif
        </div>
        @endforelse
    </div>

    @if($msg && !$showForm)
        <div class="kb-alert kb-alert-{{ $msgType }}">{{ $msg }}</div>
    @endif

</div>

{{-- ── Modal formulario ── --}}
@if($showForm)
<div class="kb-modal-overlay" wire:click.self="cancelForm">
    <div class="kb-modal">
        <div class="kb-modal-title">
            {{ $editingId ? 'Editar artículo' : 'Nuevo artículo' }}
            <button type="button" class="kb-modal-close" wire:click="cancelForm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="kb-form-field">
            <label class="kb-form-label">Título</label>
            <input type="text" class="kb-form-input" wire:model="formTitle" placeholder="Ej: Cómo restablecer mi contraseña">
        </div>

        <div class="kb-form-field">
            @if($formSource === 'scrape')
                <label class="kb-form-label">URL a indexar</label>
                <input type="url" class="kb-form-input" wire:model="formContent" placeholder="https://ejemplo.com/pagina">
                <small style="font-size:11px;color:var(--c-sub,#9ca3af);margin-top:3px">Al guardar, el sistema descargará y extraerá el contenido de esta página automáticamente.</small>
            @else
                <label class="kb-form-label">Contenido</label>
                <textarea class="kb-form-textarea" wire:model="formContent" placeholder="Escribe el contenido del artículo..."></textarea>
            @endif
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="kb-form-field" style="margin-bottom:0">
                <label class="kb-form-label">Fuente</label>
                <select class="kb-form-select" wire:model="formSource">
                    <option value="manual">Manual</option>
                    <option value="scrape">Web Scraping</option>
                    <option value="external">Externo</option>
                </select>
            </div>
            <div class="kb-form-field" style="margin-bottom:0">
                <label class="kb-form-label">Estado</label>
                <label style="display:flex;align-items:center;gap:8px;margin-top:8px;cursor:pointer;font-size:13px;color:var(--c-text,#111)">
                    <label class="kb-toggle">
                        <input type="checkbox" wire:model="formActive">
                        <span class="kb-slider"></span>
                    </label>
                    Activo
                </label>
            </div>
        </div>

        {{-- Selector de canal/widget --}}
        @if($this->widgets->count() > 0)
        <div class="kb-form-field" style="margin-top:14px">
            <label class="kb-form-label">Canal asignado</label>
            <select class="kb-form-select" wire:model="formWidgetId">
                <option value="">— Global (todos los canales) —</option>
                @foreach($this->widgets as $w)
                <option value="{{ $w->id }}">{{ $w->name }}</option>
                @endforeach
            </select>
            <small style="font-size:11px;color:var(--c-sub,#9ca3af);margin-top:4px;display:block">Global = disponible en todos los canales. Elige un canal para que este artículo sea exclusivo de ese bot o widget.</small>
        </div>
        @endif

        @if($msg)
            <div class="kb-alert kb-alert-{{ $msgType }}">{{ $msg }}</div>
        @endif

        <div class="kb-form-actions">
            <button class="kb-btn kb-btn-primary" wire:click="save" wire:loading.attr="disabled">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span wire:loading.remove wire:target="save">
                    @if($formSource === 'scrape') {{ $editingId ? 'Re-scrapear y guardar' : 'Scrapear y crear' }}
                    @else {{ $editingId ? 'Guardar cambios' : 'Crear artículo' }}
                    @endif
                </span>
                <span wire:loading wire:target="save">Procesando...</span>
            </button>
            <button class="kb-btn kb-btn-ghost" wire:click="cancelForm">Cancelar</button>
        </div>
    </div>
</div>
@endif

</x-filament-panels::page>
