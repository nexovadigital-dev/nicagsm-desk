<x-filament-panels::page>
<style>
/* ── KB Global Reset ───────────────────────────────── */
.nx-kb { font-family: inherit; }

/* ── Channel Picker ──────────────────────────────────── */
.nx-kb-picker {
    max-width: 820px;
    margin: 0 auto;
    padding: 8px 0 48px;
}
.nx-kb-picker__hero {
    text-align: center;
    padding: 32px 0 28px;
}
.nx-kb-picker__hero h1 {
    font-size: 22px;
    font-weight: 700;
    color: var(--c-h, #111827);
    margin: 0 0 6px;
}
.nx-kb-picker__hero p {
    font-size: 14px;
    color: var(--c-sub, #6b7280);
    margin: 0;
}
.nx-kb-channels {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 14px;
    margin-top: 8px;
}
.nx-kb-channel-card {
    background: var(--nx-bg, #fff);
    border: 1.5px solid var(--nx-bd, #e5e7eb);
    border-radius: 14px;
    padding: 20px;
    cursor: pointer;
    text-align: left;
    transition: border-color .15s, box-shadow .15s, transform .1s;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    width: 100%;
}
.nx-kb-channel-card:hover {
    border-color: var(--nx-accent, #22c55e);
    box-shadow: 0 4px 16px rgba(34,197,94,.12);
    transform: translateY(-1px);
}
.nx-kb-channel-card__icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: var(--nx-bg2, #f3f4f6);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background .15s;
}
.nx-kb-channel-card:hover .nx-kb-channel-card__icon {
    background: #dcfce7;
}
.nx-kb-channel-card__body { flex: 1; min-width: 0; }
.nx-kb-channel-card__name {
    font-size: 14px;
    font-weight: 600;
    color: var(--c-h, #111827);
    margin: 0 0 3px;
}
.nx-kb-channel-card__meta {
    font-size: 12px;
    color: var(--c-sub, #9ca3af);
}
.nx-kb-channel-card__count {
    font-size: 11px;
    font-weight: 700;
    background: var(--nx-bg2, #f3f4f6);
    color: var(--c-sub, #6b7280);
    border-radius: 99px;
    padding: 2px 8px;
    flex-shrink: 0;
    align-self: flex-start;
}
.nx-kb-channel-card--global .nx-kb-channel-card__icon {
    background: #ede9fe;
}
.nx-kb-channel-card--global:hover .nx-kb-channel-card__icon {
    background: #ddd6fe;
}

/* ── Article Manager (post-selection) ──────────────── */
.nx-kb-mgr { padding: 4px 0 40px; }

/* Breadcrumb / header */
.nx-kb-mgr__header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.nx-kb-back-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--c-sub, #6b7280);
    background: none;
    border: 1.5px solid var(--nx-bd, #e5e7eb);
    border-radius: 7px;
    padding: 5px 11px;
    cursor: pointer;
    transition: background .12s, border-color .12s;
    white-space: nowrap;
}
.nx-kb-back-btn:hover { background: var(--nx-bg2, #f3f4f6); border-color: #d1d5db; }
.nx-kb-channel-pill {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 14px;
    font-weight: 700;
    color: var(--c-h, #111827);
}
.nx-kb-channel-pill svg { color: var(--nx-accent, #22c55e); }

/* Toolbar */
.nx-kb-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    background: var(--nx-bg, #fff);
    border: 1.5px solid var(--nx-bd, #e5e7eb);
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 18px;
}
.nx-kb-search {
    position: relative;
    flex: 1;
    min-width: 180px;
}
.nx-kb-search input {
    width: 100%;
    padding: 7px 10px 7px 32px;
    border-radius: 8px;
    border: 1.5px solid var(--nx-bd, #e5e7eb);
    background: var(--nx-bg2, #f9fafb);
    font-size: 13px;
    color: inherit;
    outline: none;
    font-family: inherit;
    box-sizing: border-box;
    transition: border-color .15s;
}
.nx-kb-search input:focus { border-color: var(--nx-accent, #22c55e); }
.nx-kb-search__icon {
    position: absolute;
    left: 9px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    opacity: .4;
}
.nx-kb-filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }
.nx-kb-pill {
    font-size: 12px;
    font-weight: 600;
    padding: 4px 11px;
    border-radius: 99px;
    border: 1.5px solid var(--nx-bd, #e5e7eb);
    background: none;
    color: var(--c-sub, #6b7280);
    cursor: pointer;
    transition: all .12s;
    white-space: nowrap;
}
.nx-kb-pill:hover { border-color: #9ca3af; color: var(--c-h, #111827); }
.nx-kb-pill--active {
    background: var(--nx-accent, #22c55e);
    border-color: var(--nx-accent, #22c55e);
    color: #fff;
}
.nx-kb-active-toggle {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--c-sub, #6b7280);
    cursor: pointer;
    white-space: nowrap;
}
.nx-kb-add-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    padding: 7px 14px;
    border-radius: 8px;
    background: var(--nx-accent, #22c55e);
    color: #fff;
    border: none;
    cursor: pointer;
    transition: opacity .12s;
    white-space: nowrap;
}
.nx-kb-add-btn:hover { opacity: .88; }

/* Scrape button */
.nx-kb-scrape-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 8px;
    background: none;
    border: 1.5px solid var(--nx-bd, #e5e7eb);
    color: var(--c-sub, #6b7280);
    cursor: pointer;
    transition: all .12s;
    white-space: nowrap;
}
.nx-kb-scrape-btn:hover:not(:disabled) {
    border-color: #9ca3af;
    color: var(--c-h, #111827);
    background: var(--nx-bg2, #f3f4f6);
}
.nx-kb-scrape-btn:disabled { opacity: .5; cursor: not-allowed; }

/* Stats strip */
.nx-kb-stats {
    display: flex;
    gap: 10px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.nx-kb-stat {
    background: var(--nx-bg, #fff);
    border: 1.5px solid var(--nx-bd, #e5e7eb);
    border-radius: 10px;
    padding: 10px 16px;
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 90px;
}
.nx-kb-stat__value { font-size: 20px; font-weight: 700; color: var(--c-h, #111827); }
.nx-kb-stat__label { font-size: 11px; color: var(--c-sub, #9ca3af); }

/* Article cards */
.nx-kb-list { display: flex; flex-direction: column; gap: 6px; }
.nx-kb-article {
    background: var(--nx-bg, #fff);
    border: 1.5px solid var(--nx-bd, #e5e7eb);
    border-radius: 11px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: border-color .12s, box-shadow .12s;
}
.nx-kb-article:hover { border-color: #d1d5db; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.nx-kb-article__icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--nx-bg2, #f3f4f6);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #9ca3af;
}
.nx-kb-article__body { flex: 1; min-width: 0; }
.nx-kb-article__title {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--c-h, #111827);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.nx-kb-article__meta {
    font-size: 11.5px;
    color: var(--c-sub, #9ca3af);
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.nx-kb-tag {
    font-size: 10.5px;
    font-weight: 700;
    padding: 1px 7px;
    border-radius: 99px;
    letter-spacing: .3px;
    text-transform: uppercase;
}
.nx-kb-tag--manual    { background: #dbeafe; color: #1d4ed8; }
.nx-kb-tag--scrape    { background: #dcfce7; color: #15803d; }
.nx-kb-tag--web_scrape{ background: #dcfce7; color: #15803d; }
.nx-kb-tag--external  { background: #ede9fe; color: #7c3aed; }
.nx-kb-tag--active    { background: #dcfce7; color: #15803d; }
.nx-kb-tag--inactive  { background: #f3f4f6; color: #9ca3af; }

.nx-kb-article__actions { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
.nx-kb-icon-btn {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    border: 1.5px solid transparent;
    background: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--c-sub, #9ca3af);
    transition: all .12s;
}
.nx-kb-icon-btn:hover { background: var(--nx-bg2, #f3f4f6); color: var(--c-h, #374151); border-color: var(--nx-bd, #e5e7eb); }
.nx-kb-icon-btn--danger:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
.nx-kb-icon-btn--rescrape:hover { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }

/* Empty state */
.nx-kb-empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--c-sub, #9ca3af);
}
.nx-kb-empty svg { margin: 0 auto 12px; display: block; opacity: .4; }
.nx-kb-empty h3 { font-size: 15px; font-weight: 600; color: var(--c-h, #374151); margin: 0 0 6px; }
.nx-kb-empty p  { font-size: 13px; margin: 0; }

/* Alert / toast */
.nx-kb-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 14px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 16px;
    animation: nx-fadein .2s ease;
}
.nx-kb-alert--success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.nx-kb-alert--error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
@keyframes nx-fadein { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

/* Modal overlay */
.nx-kb-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(2px);
    animation: nx-fadein .15s ease;
}
.nx-kb-modal {
    background: var(--nx-bg, #fff);
    border-radius: 16px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
    animation: nx-modal-in .18s ease;
}
@keyframes nx-modal-in {
    from { opacity: 0; transform: translateY(10px) scale(.98); }
    to   { opacity: 1; transform: translateY(0)   scale(1);    }
}
.nx-kb-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px 14px;
    border-bottom: 1.5px solid var(--nx-bd, #e5e7eb);
}
.nx-kb-modal__title { font-size: 16px; font-weight: 700; color: var(--c-h, #111827); }
.nx-kb-modal__close {
    width: 30px; height: 30px;
    background: none; border: none; cursor: pointer;
    border-radius: 7px; display: flex; align-items: center; justify-content: center;
    color: var(--c-sub, #9ca3af); transition: background .12s;
}
.nx-kb-modal__close:hover { background: var(--nx-bg2, #f3f4f6); }
.nx-kb-modal__body { padding: 20px 22px; }
.nx-kb-form-field { margin-bottom: 16px; }
.nx-kb-form-field label { display: block; font-size: 12.5px; font-weight: 600; color: var(--c-sub, #374151); margin-bottom: 6px; }
.nx-kb-form-field input,
.nx-kb-form-field textarea,
.nx-kb-form-field select {
    width: 100%; padding: 9px 12px; border-radius: 8px;
    border: 1.5px solid var(--nx-bd, #e5e7eb);
    background: var(--nx-bg, #fff); font-size: 13.5px;
    color: inherit; outline: none; font-family: inherit;
    box-sizing: border-box; transition: border-color .15s;
}
.nx-kb-form-field input:focus,
.nx-kb-form-field textarea:focus,
.nx-kb-form-field select:focus { border-color: var(--nx-accent, #22c55e); }
.nx-kb-form-field textarea { resize: vertical; min-height: 120px; }
.nx-kb-form-row { display: flex; gap: 12px; }
.nx-kb-form-row .nx-kb-form-field { flex: 1; }
.nx-kb-modal__footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 8px;
    padding: 14px 22px 18px;
    border-top: 1.5px solid var(--nx-bd, #e5e7eb);
}
.nx-kb-btn-ghost {
    font-size: 13px; font-weight: 600; padding: 7px 16px;
    border-radius: 8px; border: 1.5px solid var(--nx-bd, #e5e7eb);
    background: none; color: var(--c-sub, #374151); cursor: pointer; transition: background .12s;
}
.nx-kb-btn-ghost:hover { background: var(--nx-bg2, #f3f4f6); }
.nx-kb-btn-primary {
    font-size: 13px; font-weight: 600; padding: 7px 18px;
    border-radius: 8px; border: none;
    background: var(--nx-accent, #22c55e); color: #fff; cursor: pointer; transition: opacity .12s;
}
.nx-kb-btn-primary:hover { opacity: .88; }

/* Spinner */
.nx-kb-spin {
    display: inline-block;
    width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: nx-spin .6s linear infinite;
}
@keyframes nx-spin { to { transform: rotate(360deg); } }

.nx-kb-section-title {
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--c-sub, #9ca3af);
    margin: 0 0 12px;
}
</style>

<div class="nx-kb" wire:poll.10s="$refresh">

{{-- ══════════════════════════════════════════════════════════════
     PANTALLA 1 — Selector de canal (widget-first)
════════════════════════════════════════════════════════════════ --}}
@if (! $channelSelected)

@php $widgets = $this->widgets; $globalCount = $this->globalArticlesCount; @endphp

<div class="nx-kb-picker">
    <div class="nx-kb-picker__hero">
        <h1>Base de Conocimiento</h1>
        <p>Elige el canal cuya base de conocimiento deseas gestionar</p>
    </div>

    @if ($msg)
    <div class="nx-kb-alert nx-kb-alert--{{ $msgType }}" style="max-width:700px;margin:0 auto 18px">
        {{ $msg }}
        <button wire:click="$set('msg',null)" style="margin-left:auto;background:none;border:none;cursor:pointer;opacity:.6">✕</button>
    </div>
    @endif

    <div class="nx-kb-channels">
        {{-- Tarjeta Global --}}
        <button wire:click="selectChannel(null)" class="nx-kb-channel-card nx-kb-channel-card--global">
            <div class="nx-kb-channel-card__icon">
                <svg fill="none" stroke="#7c3aed" viewBox="0 0 24 24" width="20" height="20" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="nx-kb-channel-card__body">
                <p class="nx-kb-channel-card__name">Global</p>
                <p class="nx-kb-channel-card__meta">Disponible en todos los canales</p>
            </div>
            <span class="nx-kb-channel-card__count">{{ $globalCount }}</span>
        </button>

        {{-- Tarjetas de cada widget --}}
        @foreach ($widgets as $w)
        <button wire:click="selectChannel({{ $w->id }})" class="nx-kb-channel-card">
            <div class="nx-kb-channel-card__icon">
                <svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" width="20" height="20" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div class="nx-kb-channel-card__body">
                <p class="nx-kb-channel-card__name">{{ $w->name }}</p>
                <p class="nx-kb-channel-card__meta">Solo para este widget</p>
            </div>
            <span class="nx-kb-channel-card__count">{{ $w->articles_count ?? 0 }}</span>
        </button>
        @endforeach

        {{-- Estado vacío si no hay widgets --}}
        @if ($widgets->isEmpty())
        <div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:var(--c-sub,#9ca3af)">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28" style="margin:0 auto 10px;display:block;opacity:.4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <p style="font-size:14px;margin:0">No hay widgets activos para esta organización.<br>Crea uno en <strong>Mis Widgets</strong> primero.</p>
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     PANTALLA 2 — Gestión de artículos del canal seleccionado
════════════════════════════════════════════════════════════════ --}}
@else

@php
    $entries   = $this->entries;
    $stats     = $this->stats;
    $widgets   = $this->widgets;
    $isGlobal  = $selectedWidgetId === null;
    $activeWidget = $isGlobal ? null : $widgets->firstWhere('id', $selectedWidgetId);
    $channelName  = $isGlobal ? 'Global' : ($activeWidget?->name ?? 'Widget');
    $orgWebsite   = $this->orgWebsite;
    $lastScrape   = $this->lastWebScrape;
@endphp

<div class="nx-kb-mgr">

    {{-- Header con breadcrumb --}}
    <div class="nx-kb-mgr__header">
        <button wire:click="backToChannels" class="nx-kb-back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Canales
        </button>
        <svg fill="none" stroke="#d1d5db" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <div class="nx-kb-channel-pill">
            @if ($isGlobal)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            @endif
            {{ $channelName }}
        </div>
    </div>

    {{-- Alert --}}
    @if ($msg)
    <div class="nx-kb-alert nx-kb-alert--{{ $msgType }}">
        {{ $msg }}
        <button wire:click="$set('msg',null)" style="margin-left:auto;background:none;border:none;cursor:pointer;opacity:.6">✕</button>
    </div>
    @endif

    {{-- Stats strip --}}
    <div class="nx-kb-stats">
        <div class="nx-kb-stat">
            <span class="nx-kb-stat__value">{{ $entries->count() }}</span>
            <span class="nx-kb-stat__label">Total</span>
        </div>
        <div class="nx-kb-stat">
            <span class="nx-kb-stat__value">{{ $entries->where('is_active', true)->count() }}</span>
            <span class="nx-kb-stat__label">Activos</span>
        </div>
        <div class="nx-kb-stat">
            <span class="nx-kb-stat__value">{{ $entries->where('source', 'manual')->count() }}</span>
            <span class="nx-kb-stat__label">Manuales</span>
        </div>
        <div class="nx-kb-stat">
            <span class="nx-kb-stat__value">{{ $entries->whereIn('source', ['scrape','web_scrape'])->count() }}</span>
            <span class="nx-kb-stat__label">Web scraping</span>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="nx-kb-toolbar">
        {{-- Búsqueda --}}
        <div class="nx-kb-search">
            <svg class="nx-kb-search__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar artículos…">
        </div>

        {{-- Filtros fuente --}}
        <div class="nx-kb-filter-pills">
            <button wire:click="$set('filterSource','all')"     class="nx-kb-pill {{ $filterSource === 'all'     ? 'nx-kb-pill--active' : '' }}">Todos</button>
            <button wire:click="$set('filterSource','manual')"  class="nx-kb-pill {{ $filterSource === 'manual'  ? 'nx-kb-pill--active' : '' }}">Manual</button>
            <button wire:click="$set('filterSource','web_scrape')" class="nx-kb-pill {{ $filterSource === 'web_scrape' ? 'nx-kb-pill--active' : '' }}">Scraping</button>
        </div>

        {{-- Toggle solo activos --}}
        <label class="nx-kb-active-toggle">
            <input type="checkbox" wire:model.live="filterActive" style="accent-color:var(--nx-accent,#22c55e)">
            Solo activos
        </label>

        {{-- Scrape sitio web --}}
        @if ($orgWebsite)
        <button wire:click="scrapeOrgWebsite" class="nx-kb-scrape-btn" wire:loading.attr="disabled" wire:target="scrapeOrgWebsite" {{ $isScraping ? 'disabled' : '' }}>
            <span wire:loading.remove wire:target="scrapeOrgWebsite">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Escanear sitio
            </span>
            <span wire:loading wire:target="scrapeOrgWebsite">
                <span class="nx-kb-spin" style="border-top-color:#6b7280"></span> Escaneando…
            </span>
        </button>
        @if ($lastScrape)
        <span style="font-size:11px;color:var(--c-sub,#9ca3af);white-space:nowrap">Último: {{ $lastScrape }}</span>
        @endif
        @endif

        {{-- Nuevo artículo --}}
        <button wire:click="openCreate" class="nx-kb-add-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo artículo
        </button>
    </div>

    {{-- Lista de artículos --}}
    <div class="nx-kb-list">
        @forelse ($entries as $entry)
        <div class="nx-kb-article" wire:key="kb-{{ $entry->id }}">
            <div class="nx-kb-article__icon">
                @if (in_array($entry->source, ['scrape','web_scrape']))
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                @endif
            </div>

            <div class="nx-kb-article__body">
                <div class="nx-kb-article__title">{{ $entry->title }}</div>
                <div class="nx-kb-article__meta">
                    <span class="nx-kb-tag nx-kb-tag--{{ $entry->source }}">
                        {{ match($entry->source) { 'manual' => 'Manual', 'scrape','web_scrape' => 'Web', 'external' => 'Externo', default => $entry->source } }}
                    </span>
                    <span class="nx-kb-tag {{ $entry->is_active ? 'nx-kb-tag--active' : 'nx-kb-tag--inactive' }}">
                        {{ $entry->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span>{{ $entry->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            <div class="nx-kb-article__actions">
                {{-- Toggle activo --}}
                <button wire:click="toggleActive({{ $entry->id }})"
                        class="nx-kb-icon-btn"
                        title="{{ $entry->is_active ? 'Desactivar' : 'Activar' }}">
                    @if ($entry->is_active)
                    <svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" width="15" height="15" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    @else
                    <svg fill="none" stroke="#9ca3af" viewBox="0 0 24 24" width="15" height="15" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                    @endif
                </button>

                {{-- Re-scrape (si es scraping) --}}
                @if (in_array($entry->source, ['scrape','web_scrape']))
                <button wire:click="rescrape({{ $entry->id }})" class="nx-kb-icon-btn nx-kb-icon-btn--rescrape" title="Re-escanear">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
                @endif

                {{-- Editar --}}
                <button wire:click="openEdit({{ $entry->id }})" class="nx-kb-icon-btn" title="Editar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>

                {{-- Eliminar --}}
                <button wire:click="delete({{ $entry->id }})"
                        wire:confirm="¿Eliminar este artículo? Esta acción no se puede deshacer."
                        class="nx-kb-icon-btn nx-kb-icon-btn--danger"
                        title="Eliminar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
        @empty
        <div class="nx-kb-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="40" height="40" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3>Sin artículos en este canal</h3>
            <p>Crea el primero con el botón <strong>"Nuevo artículo"</strong> o escanea tu sitio web.</p>
        </div>
        @endforelse
    </div>
</div>

@endif {{-- /channelSelected --}}

{{-- ══════════════════════════════════════════════════════════════
     MODAL — Crear / Editar artículo
════════════════════════════════════════════════════════════════ --}}
@if ($showForm)
<div class="nx-kb-overlay" wire:click.self="cancelForm">
    <div class="nx-kb-modal">
        <div class="nx-kb-modal__header">
            <span class="nx-kb-modal__title">{{ $editingId ? 'Editar artículo' : 'Nuevo artículo' }}</span>
            <button class="nx-kb-modal__close" wire:click="cancelForm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="nx-kb-modal__body">

            {{-- Alerta interna del form --}}
            @if ($msg && $msgType === 'error')
            <div class="nx-kb-alert nx-kb-alert--error" style="margin-bottom:14px">{{ $msg }}</div>
            @endif

            {{-- Fuente --}}
            <div class="nx-kb-form-field">
                <label>Tipo de artículo</label>
                <select wire:model.live="formSource">
                    <option value="manual">Manual — escribe el contenido</option>
                    <option value="scrape">Scraping de URL</option>
                </select>
            </div>

            {{-- Título --}}
            <div class="nx-kb-form-field">
                <label>Título <span style="color:#dc2626">*</span></label>
                <input type="text" wire:model="formTitle" placeholder="Ej: Política de devoluciones">
            </div>

            {{-- Contenido / URL --}}
            @if ($formSource === 'scrape')
            <div class="nx-kb-form-field">
                <label>URL a escanear <span style="color:#dc2626">*</span></label>
                <input type="url" wire:model="formContent" placeholder="https://tutienda.com/pagina-de-ejemplo">
                <p style="font-size:11.5px;color:var(--c-sub,#9ca3af);margin:5px 0 0">Se extraerá el contenido de texto de esta página automáticamente.</p>
            </div>
            @else
            <div class="nx-kb-form-field">
                <label>Contenido <span style="color:#dc2626">*</span></label>
                <textarea wire:model="formContent" placeholder="Escribe el contenido del artículo…" style="min-height:150px"></textarea>
            </div>
            @endif

            {{-- Canal + estado --}}
            <div class="nx-kb-form-row">
                <div class="nx-kb-form-field">
                    <label>Canal</label>
                    <select wire:model="formWidgetId">
                        <option value="">Global (todos los canales)</option>
                        @foreach ($this->widgets as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="nx-kb-form-field" style="flex:0 0 auto;display:flex;flex-direction:column;justify-content:flex-end">
                    <label style="margin-bottom:10px">Estado</label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:500;cursor:pointer">
                        <input type="checkbox" wire:model="formActive" style="accent-color:var(--nx-accent,#22c55e);width:16px;height:16px">
                        Activo
                    </label>
                </div>
            </div>
        </div>
        <div class="nx-kb-modal__footer">
            <button class="nx-kb-btn-ghost" wire:click="cancelForm">Cancelar</button>
            <button class="nx-kb-btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ $editingId ? 'Actualizar' : 'Guardar artículo' }}</span>
                <span wire:loading wire:target="save"><span class="nx-kb-spin"></span> Guardando…</span>
            </button>
        </div>
    </div>
</div>
@endif

</div>{{-- /nx-kb --}}
</x-filament-panels::page>
