<x-filament-panels::page>
@php
    \Carbon\Carbon::setLocale('es');
    $orgTimezone    = auth()->user()?->organization?->timezone ?? config('app.timezone', 'UTC');
    $tickets        = $this->tickets;
    $palette        = ['#0ea5e9','#10b981','#f59e0b','#ef4444','#ec4899','#6366f1'];
    $suggestions    = $this->contactSuggestions;
    $availableDepts = $this->availableDepartments;
    $selTicket      = $this->selectedTicket();
    $chatMsgs       = $selTicket ? $this->chatMessages() : collect();
    $agentAvatar    = auth()->user()?->avatar_url ?? null;
@endphp

<style>
/* ─── Reset Filament ─── */
.fi-page-header, .fi-breadcrumbs { display: none !important; }
.fi-page, .fi-page > div, .fi-page > div > div { height: 100%; padding: 0 !important; margin: 0 !important; max-width: none !important; }
.fi-main-ctn { padding: 0 !important; }

/* ─── Root layout ─── */
.tk-root {
    display: flex;
    height: calc(100dvh - 57px);
    overflow: hidden;
    font-family: inherit;
    position: relative;
}

/* ─── Sidebar ─── */
.tk-sidebar {
    width: 340px;
    min-width: 260px;
    max-width: 380px;
    display: flex;
    flex-direction: column;
    border-right: 1px solid var(--nx-border, rgba(128,128,128,.18));
    background: var(--nx-surface, #fff);
    overflow: hidden;
    flex-shrink: 0;
}
.tk-sidebar-header {
    padding: 14px 16px 10px;
    border-bottom: 1px solid var(--nx-border, rgba(128,128,128,.18));
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.tk-sidebar-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.tk-sidebar-title-text { font-size: 15px; font-weight: 700; color: var(--nx-text, #0f172a); }

/* Filters row - compact, 2 per row */
.tk-filters {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5px;
}
.tk-filters .tk-select { min-width: 0; }
.tk-select {
    flex: 1;
    min-width: 110px;
    background: var(--nx-surf2, rgba(128,128,128,.07));
    border: 1px solid var(--nx-border, rgba(128,128,128,.18));
    border-radius: 7px;
    color: var(--nx-text, #0f172a);
    font-size: 11.5px;
    padding: 5px 8px;
    outline: none;
    font-family: inherit;
}
.tk-select:focus { border-color: #22c55e; }
.tk-search-wrap { position: relative; }
.tk-search-icon { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--nx-muted, #9ca3af); pointer-events: none; }
.tk-search {
    width: 100%;
    box-sizing: border-box;
    background: var(--nx-surf2, rgba(128,128,128,.07));
    border: 1px solid var(--nx-border, rgba(128,128,128,.18));
    border-radius: 7px;
    font-size: 12.5px;
    color: var(--nx-text, #0f172a);
    padding: 7px 10px 7px 30px;
    outline: none;
    font-family: inherit;
}
.tk-search:focus { border-color: #22c55e; }
.tk-search::placeholder { color: var(--nx-muted, #9ca3af); }

/* Ticket list */
.tk-list { flex: 1; overflow-y: auto; }
.tk-list-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 11px 16px;
    cursor: pointer;
    border-bottom: 1px solid var(--nx-border, rgba(128,128,128,.1));
    transition: background .12s;
}
.tk-list-item:hover { background: var(--nx-surf2, rgba(128,128,128,.06)); }
.tk-list-item.active { background: var(--nx-surf2, rgba(128,128,128,.08)); border-left: 3px solid #6366f1; }
.tk-list-item.active .tk-li-num { color: #6366f1; }
.tk-li-avatar { width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; }
.tk-li-body { flex: 1; min-width: 0; }
.tk-li-top { display: flex; align-items: center; justify-content: space-between; gap: 4px; }
.tk-li-name { font-size: 13px; font-weight: 600; color: var(--nx-text, #0f172a); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.tk-li-time { font-size: 10.5px; color: var(--nx-muted, #9ca3af); white-space: nowrap; flex-shrink: 0; }
.tk-li-num { font-size: 11px; font-family: ui-monospace,monospace; font-weight: 700; color: #6366f1; }
.tk-li-subject { font-size: 12px; color: var(--nx-muted, #64748b); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 1px; }
.tk-li-foot { display: flex; align-items: center; gap: 5px; margin-top: 3px; }

/* Badges */
.tk-badge {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 7px; border-radius: 99px;
    font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap;
}
.tk-badge-bot    { background: #eef2ff; color: #4338ca; }
.tk-badge-human  { background: #eff6ff; color: #1d4ed8; }
.tk-badge-closed { background: #f1f5f9; color: #475569; }
.tk-badge-high   { background: #fef2f2; color: #dc2626; }
.tk-badge-medium { background: #fffbeb; color: #b45309; }
.tk-badge-low    { background: #f0fdf4; color: #16a34a; }
.tk-badge-normal { background: #f8fafc; color: #64748b; }

/* Empty state */
.tk-list-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; padding: 48px 20px; color: var(--nx-muted, #94a3b8); text-align: center; }
.tk-list-empty p { font-size: 13px; max-width: 200px; line-height: 1.5; }

/* Pagination */
.tk-pages { padding: 8px 14px; border-top: 1px solid var(--nx-border, rgba(128,128,128,.18)); font-size: 11px; color: var(--nx-muted, #94a3b8); display: flex; justify-content: space-between; align-items: center; }

/* New Ticket button */
.tk-btn-new {
    display: inline-flex; align-items: center; gap: 5px;
    background: #1e293b; color: #f8fafc;
    border: none; border-radius: 8px;
    padding: 7px 12px; font-size: 12px; font-weight: 600;
    cursor: pointer; font-family: inherit; white-space: nowrap;
    transition: background .15s;
}
.tk-btn-new:hover { background: #0f172a; }

/* ─── Main chat panel ─── */
.tk-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--nx-bg, #f8fafc);
}

/* Empty state for panel */
.tk-main-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    color: var(--nx-muted, #94a3b8);
    text-align: center;
    padding: 40px;
}
.tk-main-empty p { font-size: 13.5px; max-width: 280px; line-height: 1.6; }

/* Chat header */
.tk-chat-header {
    background: var(--nx-surface, #fff);
    border-bottom: 1px solid var(--nx-border, rgba(128,128,128,.18));
    padding: 12px 20px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex-shrink: 0;
}
.tk-chat-header-top { display: flex; align-items: center; gap: 12px; }
.tk-chat-header-info { flex: 1; min-width: 0; }
.tk-chat-header-name { font-size: 15px; font-weight: 700; color: var(--nx-text, #0f172a); }
.tk-chat-header-sub { font-size: 12px; color: var(--nx-muted, #64748b); margin-top: 2px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.tk-chat-header-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }

.tk-hdr-btn {
    display: inline-flex; align-items: center; gap: 5px;
    border: 1px solid var(--nx-border, rgba(128,128,128,.18));
    background: transparent;
    border-radius: 7px; padding: 5px 11px;
    font-size: 12px; font-weight: 600;
    cursor: pointer; font-family: inherit;
    color: var(--nx-text, #0f172a);
    transition: all .15s;
}
.tk-hdr-btn:hover { background: var(--nx-surf2, rgba(128,128,128,.07)); }
.tk-hdr-btn.green { color: #16a34a; border-color: rgba(34,197,94,.3); }
.tk-hdr-btn.green:hover { background: rgba(34,197,94,.07); }
.tk-hdr-btn.danger { color: #dc2626; border-color: rgba(239,68,68,.3); }
.tk-hdr-btn.danger:hover { background: rgba(239,68,68,.06); }

/* Messages area */
.tk-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px 20px 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

/* Bubbles */
.tk-bubble-wrap {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    margin-bottom: 6px;
}
.tk-bubble-wrap.agent { flex-direction: row-reverse; }

.tk-bubble-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,.15);
    align-self: flex-end;
}

.tk-bubble-col { display: flex; flex-direction: column; max-width: 72%; }
.tk-bubble-wrap.agent .tk-bubble-col { align-items: flex-end; }
.tk-bubble-wrap:not(.agent) .tk-bubble-col { align-items: flex-start; }

.tk-bubble {
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 13.5px;
    line-height: 1.6;
    word-break: break-word;
    white-space: pre-wrap;
    text-align: left;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    animation: tkFadeIn .18s ease;
}
@keyframes tkFadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }

.tk-bubble.user {
    background: #fff;
    color: #0f172a;
    border: 1px solid rgba(0,0,0,.07);
    border-radius: 16px 16px 16px 4px;
}
.tk-bubble.agent {
    background: linear-gradient(135deg, #4338ca, #312e81);
    color: #fff;
    border-radius: 16px 16px 4px 16px;
}
.tk-bubble.bot {
    background: #fff;
    color: #0f172a;
    border: 1px solid rgba(0,0,0,.07);
    border-radius: 16px 16px 16px 4px;
}

.tk-bubble-time { font-size: 10px; color: var(--nx-muted, #9ca3af); margin-top: 3px; padding: 0 2px; }

/* System message */
.tk-sys-msg { text-align: center; font-size: 11px; color: var(--nx-muted, #94a3b8); padding: 6px 0; }

/* Date separator */
.tk-date-sep {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 14px 0 10px;
    color: var(--nx-muted, #94a3b8);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .03em;
    text-transform: uppercase;
}
.tk-date-sep::before,
.tk-date-sep::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--nx-border, rgba(128,128,128,.18));
}

/* Ticket opened card */
.tk-ticket-card {
    margin: 10px auto;
    max-width: 400px;
    background: linear-gradient(135deg, #eff6ff, #f0fdf4);
    border: 1.5px solid #bfdbfe;
    border-radius: 14px;
    padding: 14px 18px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(99,102,241,.08);
}

/* Composer */
.tk-composer {
    background: var(--nx-surface, #fff);
    border-top: 1px solid var(--nx-border, rgba(128,128,128,.18));
    padding: 12px 16px;
    flex-shrink: 0;
}
.tk-composer-row { display: flex; gap: 8px; align-items: flex-end; }
.tk-composer-input {
    flex: 1;
    background: var(--nx-surf2, rgba(128,128,128,.07));
    border: 1px solid var(--nx-border, rgba(128,128,128,.18));
    border-radius: 10px;
    padding: 9px 13px;
    font-size: 13.5px;
    font-family: inherit;
    color: var(--nx-text, #0f172a);
    outline: none;
    resize: none;
    max-height: 120px;
    transition: border-color .15s;
}
.tk-composer-input:focus { border-color: #6366f1; }
.tk-composer-input::placeholder { color: var(--nx-muted, #9ca3af); }
.tk-composer-send {
    display: inline-flex; align-items: center; justify-content: center;
    width: 40px; height: 40px;
    background: #312e81; color: #fff;
    border: none; border-radius: 10px;
    cursor: pointer; flex-shrink: 0;
    transition: background .15s, transform .1s;
}
.tk-composer-send:hover { background: #4338ca; transform: scale(1.04); }
.tk-composer-send:disabled { background: #cbd5e1; cursor: default; transform: none; }
.tk-closed-notice {
    text-align: center;
    font-size: 12px;
    color: var(--nx-muted, #94a3b8);
    padding: 12px 20px;
    border-top: 1px solid var(--nx-border, rgba(128,128,128,.18));
    background: var(--nx-surface, #fff);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* ─── Rating stars ─── */
.tk-rating { display: inline-flex; align-items: center; gap: 2px; font-size: 13px; }
.tk-rating-star { color: #f59e0b; }
.tk-rating-empty { color: var(--nx-border, #d1d5db); }

/* ─── Modal ─── */
.tk-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.4);
    z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}
.tk-modal {
    background: var(--nx-surface, #fff);
    border-radius: 14px;
    width: 100%; max-width: 500px;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
    overflow: hidden;
    display: flex; flex-direction: column;
}
.tk-modal-header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--nx-border, rgba(128,128,128,.18));
    display: flex; align-items: center; justify-content: space-between;
}
.tk-modal-title { font-size: 15px; font-weight: 700; color: var(--nx-text, #0f172a); }
.tk-modal-close { background: none; border: none; cursor: pointer; color: var(--nx-muted, #9ca3af); display: flex; padding: 4px; transition: color .12s; }
.tk-modal-close:hover { color: var(--nx-text, #0f172a); }
.tk-modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 14px; max-height: 60vh; overflow-y: auto; }
.tk-modal-footer { padding: 14px 22px; border-top: 1px solid var(--nx-border, rgba(128,128,128,.18)); display: flex; justify-content: flex-end; gap: 8px; }
.tk-label { font-size: 11px; font-weight: 700; color: var(--nx-muted, #9ca3af); text-transform: uppercase; letter-spacing: .04em; display: block; margin-bottom: 5px; }
.tk-input { width: 100%; border: 1px solid var(--nx-border, rgba(128,128,128,.18)); border-radius: 8px; padding: 8px 11px; font-size: 13px; font-family: inherit; color: var(--nx-text, #0f172a); outline: none; background: var(--nx-surf2, rgba(128,128,128,.07)); box-sizing: border-box; transition: border-color .15s, background .15s; }
.tk-input:focus { border-color: #22c55e; background: var(--nx-surface, #fff); }
.tk-input::placeholder { color: var(--nx-muted, #9ca3af); }
.tk-textarea { resize: vertical; min-height: 72px; }
.tk-tab-row { display: flex; gap: 0; border: 1px solid var(--nx-border, rgba(128,128,128,.18)); border-radius: 8px; overflow: hidden; margin-bottom: 2px; }
.tk-tab-btn { flex: 1; padding: 7px 10px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; font-family: inherit; background: var(--nx-surf2, rgba(128,128,128,.07)); color: var(--nx-muted, #9ca3af); transition: background .12s, color .12s; }
.tk-tab-btn.active { background: #1e293b; color: #f8fafc; }
.tk-contact-list { border: 1px solid var(--nx-border, rgba(128,128,128,.18)); border-radius: 8px; overflow: hidden; margin-top: 4px; }
.tk-contact-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; cursor: pointer; transition: background .1s; border-bottom: 1px solid var(--nx-border, rgba(128,128,128,.18)); }
.tk-contact-item:last-child { border-bottom: none; }
.tk-contact-item:hover, .tk-contact-item.selected { background: var(--nx-surf2, rgba(128,128,128,.07)); }
.tk-btn-ghost { background: transparent; border: 1px solid var(--nx-border, rgba(128,128,128,.18)); border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; color: var(--nx-text, #0f172a); transition: background .12s; }
.tk-btn-ghost:hover { background: var(--nx-surf2, rgba(128,128,128,.07)); }
.tk-btn-primary { background: #1e293b; color: #f8fafc; border: 1px solid #1e293b; border-radius: 8px; padding: 8px 18px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; transition: background .12s; }
.tk-btn-primary:hover { background: #0f172a; }
.tk-btn-primary:disabled { background: #cbd5e1; border-color: #cbd5e1; cursor: default; }
.tk-avatar { width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0; }

/* ─── Responsive: móvil ─── */
@media (max-width: 768px) {
    .tk-root { flex-direction: column; height: auto; overflow: visible; min-height: 100dvh; }
    .tk-sidebar { width: 100%; max-width: 100%; border-right: none; border-bottom: 1px solid var(--nx-border, rgba(128,128,128,.18)); max-height: 40vh; }
    .tk-main { min-height: 60vh; }
    .tk-bubble { max-width: 85%; }
    .tk-chat-header-actions { flex-wrap: wrap; }
    .tk-filters { grid-template-columns: 1fr; }
}

/* ─── Additional mobile fixes ─── */
@media (max-width: 768px) {
    /* Stack sidebar + main vertically */
    .tk-root { flex-direction: column !important; height: auto !important; min-height: 100dvh; overflow: visible !important; }
    .tk-sidebar { width: 100% !important; max-width: 100% !important; border-right: none !important; border-bottom: 1px solid var(--nx-border, rgba(128,128,128,.18)) !important; max-height: 45vh; overflow-y: auto; }
    .tk-main { min-height: 55vh; }

    /* Chat header: stack on mobile */
    .tk-chat-header { padding: 10px 14px; }
    .tk-chat-header-top { flex-wrap: wrap; gap: 8px; }
    .tk-chat-header-sub { font-size: 11px; flex-wrap: wrap; }
    .tk-chat-header-name { font-size: 14px; }
    .tk-chat-header-actions { width: 100%; justify-content: flex-end; flex-wrap: wrap; gap: 4px; }

    /* Hide dept dropdown text overflow */
    .tk-chat-header-actions select { max-width: 130px; font-size: 11px; }

    /* Bubbles */
    .tk-bubble { max-width: 86% !important; }
    .tk-bubble-col { max-width: 86% !important; }

    /* Sidebar filters: 1 col on very small */
    .tk-filters { grid-template-columns: 1fr !important; }
    .tk-sidebar-header { padding: 10px 12px 8px; }
}
</style>

<div class="tk-root">

    {{-- ═══════════════════
         SIDEBAR — Lista
    ══════════════════════ --}}
    <aside class="tk-sidebar">
        <div class="tk-sidebar-header">
            <div class="tk-sidebar-title">
                <span class="tk-sidebar-title-text">🎫 Tickets</span>
                <button class="tk-btn-new" wire:click="openNewModal">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Nuevo
                </button>
            </div>

            {{-- Search --}}
            <div class="tk-search-wrap">
                <svg class="tk-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" class="tk-search" wire:model.live.debounce.300ms="search" placeholder="Buscar ticket, cliente, asunto…">
            </div>

            {{-- Filters --}}
            <div class="tk-filters">
                <select class="tk-select" wire:model.live="filterStatus">
                    <option value="all">Todos los estados</option>
                    <option value="human">Abiertos</option>
                    <option value="closed">Cerrados</option>
                    <option value="bot">Bot</option>
                </select>
                <select class="tk-select" wire:model.live="filterPriority">
                    <option value="all">Toda prioridad</option>
                    <option value="high">Alta</option>
                    <option value="medium">Media</option>
                    <option value="normal">Normal</option>
                    <option value="low">Baja</option>
                </select>
                @if($availableDepts->isNotEmpty())
                <select class="tk-select" wire:model.live="filterDepartment">
                    <option value="all">Todos los depart.</option>
                    <option value="none">Sin departamento</option>
                    @foreach($availableDepts as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>

        {{-- Ticket list --}}
        <div class="tk-list">
            @forelse($tickets as $ticket)
                @php
                    $color    = $palette[abs(crc32($ticket->client_name)) % count($palette)];
                    $priority = $ticket->priority ?? 'normal';
                    $active   = $selectedTicketId === $ticket->id;
                    $lastMsg  = $ticket->messages->first();
                    // Usar asunto como preview primario; si no hay, último mensaje (excluyendo system)
                    if ($ticket->ticket_subject) {
                        $preview = \Illuminate\Support\Str::limit($ticket->ticket_subject, 55);
                    } elseif ($lastMsg && !str_starts_with($lastMsg->content, '__')) {
                        $preview = \Illuminate\Support\Str::limit($lastMsg->content, 55);
                    } else {
                        $preview = '—';
                    }
                @endphp
                <div class="tk-list-item {{ $active ? 'active' : '' }}"
                     wire:click="selectTicket({{ $ticket->id }})"
                     wire:key="tl-{{ $ticket->id }}">
                    <div class="tk-li-avatar" style="background:{{ $color }}">
                        {{ strtoupper(substr($ticket->client_name ?? 'V', 0, 1)) }}
                    </div>
                    <div class="tk-li-body">
                        <div class="tk-li-top">
                            <span class="tk-li-name">{{ $ticket->client_name ?? 'Visitante' }}</span>
                            <span class="tk-li-time">{{ ($ticket->ticket_opened_at ?? $ticket->created_at)->setTimezone($orgTimezone)->diffForHumans(null, true, true) }}</span>
                        </div>
                        <div class="tk-li-num">{{ $ticket->ticket_number }}</div>
                        <div class="tk-li-subject">{{ $preview }}</div>
                        <div class="tk-li-foot">
                            <span class="tk-badge tk-badge-{{ $ticket->status }}">
                                @if($ticket->status==='human') Abierto
                                @elseif($ticket->status==='closed') Cerrado
                                @else Bot @endif
                            </span>
                            @if($priority !== 'normal')
                                <span class="tk-badge tk-badge-{{ $priority }}">{{ $priority }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="tk-list-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="32" height="32" style="opacity:.3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    <p>{{ $search || $filterStatus !== 'all' ? 'Sin resultados para los filtros aplicados.' : 'No hay tickets aún. Crea uno con el botón "Nuevo".' }}</p>
                </div>
            @endforelse
        </div>

        @if($tickets->hasPages())
        <div class="tk-pages">
            <span>{{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} de {{ $tickets->total() }}</span>
            <div>{{ $tickets->links() }}</div>
        </div>
        @endif
    </aside>

    {{-- ══════════════════════
         MAIN — Panel de chat
    ═══════════════════════════ --}}
    <main class="tk-main" @if($selectedTicketId) wire:poll.4s @endif>

        @if(! $selTicket)
            {{-- Empty state --}}
            <div class="tk-main-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48" style="opacity:.18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <p>Selecciona un ticket para ver la conversación y responder al cliente.</p>
            </div>

        @else
            @php
                $tc     = $palette[abs(crc32($selTicket->client_name)) % count($palette)];
                $rating = $selTicket->survey_rating;
            @endphp

            {{-- Header --}}
            <header class="tk-chat-header">
                <div class="tk-chat-header-top">
                    <div class="tk-li-avatar" style="background:{{ $tc }};width:38px;height:38px;font-size:14px">
                        {{ strtoupper(substr($selTicket->client_name ?? 'V', 0, 1)) }}
                    </div>
                    <div class="tk-chat-header-info">
                        <div class="tk-chat-header-name">{{ $selTicket->client_name }}</div>
                        <div class="tk-chat-header-sub">
                            <span style="font-family:ui-monospace,monospace;font-weight:700;color:#6366f1">{{ $selTicket->ticket_number }}</span>
                            <span>·</span>
                            <span class="tk-badge tk-badge-{{ $selTicket->status }}">
                                @if($selTicket->status==='human') Abierto
                                @elseif($selTicket->status==='closed') Cerrado
                                @else Bot @endif
                            </span>
                            @if($selTicket->client_email)
                                <span>· {{ $selTicket->client_email }}</span>
                            @endif
                            @if($selTicket->ticket_subject)
                                <span>· <em style="color:var(--nx-muted,#64748b)">{{ $selTicket->ticket_subject }}</em></span>
                            @endif
                            <span>· {{ ($selTicket->ticket_opened_at ?? $selTicket->created_at)->setTimezone($orgTimezone)->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        @if($rating)
                            <div class="tk-rating" style="margin-top:3px">
                                @for($i=1;$i<=5;$i++)
                                    <span class="{{ $i<=$rating ? 'tk-rating-star' : 'tk-rating-empty' }}">★</span>
                                @endfor
                                <span style="font-size:10px;color:#94a3b8;margin-left:3px">{{ $rating }}/5</span>
                                @if($selTicket->survey_comment)
                                    <span style="font-size:10.5px;color:#64748b;margin-left:5px">"{{ $selTicket->survey_comment }}"</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="tk-chat-header-actions">
                        @if($selTicket->status !== 'closed')
                            <button class="tk-hdr-btn danger"
                                    wire:click="closeTicket({{ $selTicket->id }})"
                                    wire:confirm="¿Marcar como resuelto? Se enviará la encuesta al cliente.">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Cerrar
                            </button>
                        @else
                            <button class="tk-hdr-btn green" wire:click="reopenTicket({{ $selTicket->id }})">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Reabrir
                            </button>
                        @endif
                        {{-- Dept selector --}}
                        <select x-data @change="$wire.setDepartment({{ $selTicket->id }}, $event.target.value)"
                                style="background:transparent;border:1px solid var(--nx-border,rgba(128,128,128,.18));border-radius:7px;font-size:12px;padding:5px 8px;color:var(--nx-text,#0f172a);outline:none;font-family:inherit;cursor:pointer">
                            <option value="">— Sin depart. —</option>
                            @foreach($availableDepts as $d)
                                <option value="{{ $d->id }}" {{ $selTicket->department_id === $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </header>

            {{-- Messages --}}
            <section class="tk-messages"
                     x-data="{}"
                     x-init="$el.scrollTop = $el.scrollHeight"
                     x-on:livewire:updated.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)">

                @php $lastDateKey = null; @endphp
                @forelse($chatMsgs as $msg)
                    @php
                        $isTicketOpened = str_starts_with($msg->content, '__TICKET_OPENED__:');
                        $ticketNum2     = $isTicketOpened ? substr($msg->content, strlen('__TICKET_OPENED__:')) : null;
                        $isUser         = $msg->sender_type === 'user';
                        $isAgent        = $msg->sender_type === 'agent';
                        $isSystem       = $msg->sender_type === 'system';

                        // Date separator logic
                        $msgDate    = $msg->created_at->setTimezone($orgTimezone);
                        $dateKey    = $msgDate->format('Y-m-d');
                        $showSep    = $dateKey !== $lastDateKey;
                        $lastDateKey = $dateKey;
                        $today      = now()->setTimezone($orgTimezone)->format('Y-m-d');
                        $yesterday  = now()->setTimezone($orgTimezone)->subDay()->format('Y-m-d');
                        $sepLabel   = match($dateKey) {
                            $today     => 'Hoy',
                            $yesterday => 'Ayer',
                            default    => $msgDate->translatedFormat('d M Y'),
                        };
                    @endphp

                    {{-- Date separator --}}
                    @if($showSep)
                        <div class="tk-date-sep">{{ $sepLabel }}</div>
                    @endif

                    @if($isSystem)
                        @if($isTicketOpened)
                            <div class="tk-ticket-card">
                                <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:8px">
                                    <svg fill="none" stroke="#6366f1" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    <span style="font-size:12px;font-weight:700;color:#4338ca">Chat convertido a ticket de soporte</span>
                                </div>
                                <div style="font-size:14px;font-weight:800;color:#312e81">#{{ $ticketNum2 }}</div>
                                <div style="font-size:11px;color:#64748b;margin-top:4px">El chat fue finalizado. Seguimiento por email.</div>
                            </div>
                        @else
                            <div class="tk-sys-msg">
                                {{ $msg->content === '__AGENT_CTA__' ? '📞 Cliente solicitó atención con un agente' : $msg->content }}
                            </div>
                        @endif
                        @continue
                    @endif

                    <div class="tk-bubble-wrap {{ $isAgent ? 'agent' : '' }}" wire:key="cm-{{ $msg->id }}">

                        {{-- Avatar --}}
                        @if($isUser)
                            <div class="tk-bubble-avatar" style="background:{{ $tc }}">
                                {{ strtoupper(substr($selTicket->client_name ?? 'V', 0, 1)) }}
                            </div>
                        @elseif($isAgent)
                            @if($agentAvatar)
                                <div class="tk-bubble-avatar" style="padding:0;overflow:hidden">
                                    <img src="{{ $agentAvatar }}" alt="A" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                                </div>
                            @else
                                <div class="tk-bubble-avatar" style="background:#4338ca">A</div>
                            @endif
                        @else
                            <div class="tk-bubble-avatar" style="background:#6366f1">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                            </div>
                        @endif

                        {{-- Bubble + time --}}
                        <div class="tk-bubble-col">
                            <div class="tk-bubble {{ $msg->sender_type }}">{{ trim($msg->content) }}</div>
                            <div class="tk-bubble-time">
                                @if($isAgent) Agente &middot;
                                @elseif($msg->sender_type==='bot') Nexova IA &middot;
                                @endif
                                {{ $msg->created_at->setTimezone($orgTimezone)->format('H:i') }}
                            </div>
                        </div>

                    </div>
                @empty
                    <div style="text-align:center;padding:40px 20px;color:var(--nx-muted,#94a3b8);font-size:13px">
                        Aún no hay mensajes en este ticket.
                    </div>
                @endforelse
            </section>

            {{-- Composer or closed notice --}}
            @if($selTicket->status !== 'closed')
                <footer class="tk-composer" x-data="{ reply: '' }">
                    <div class="tk-composer-row">
                        <textarea
                            class="tk-composer-input"
                            wire:model="replyContent"
                            wire:loading.attr="disabled"
                            wire:target="sendReply"
                            rows="1"
                            placeholder="Escribe tu respuesta… (se enviará por email al cliente)"
                            x-model="reply"
                            @keydown.ctrl.enter.prevent="if(reply.trim()) { $wire.sendReply(); reply = ''; }"
                            @input="$event.target.style.height='auto'; $event.target.style.height=Math.min($event.target.scrollHeight,120)+'px'"
                        ></textarea>
                        <button class="tk-composer-send"
                                wire:click="sendReply" wire:loading.attr="disabled" wire:target="sendReply"
                                title="Enviar respuesta (Ctrl+Enter)"
                                :disabled="!reply.trim()"
                                @click="$nextTick(() => reply = '')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                    <div style="font-size:10.5px;color:var(--nx-muted,#94a3b8);margin-top:5px">
                        Ctrl+Enter para enviar &middot; La respuesta llegará al correo del cliente
                    </div>
                </footer>
            @else
                <div class="tk-closed-notice">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" style="color:#94a3b8"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Ticket cerrado — Usa el botón <strong>Reabrir</strong> para responder de nuevo</span>
                </div>
            @endif
        @endif

    </main>

</div>

{{-- ══════════════════════════════════════
     MODAL — Nuevo ticket de soporte
══════════════════════════════════════════ --}}
@if($showNewModal)
<div class="tk-overlay" wire:click.self="$set('showNewModal', false)">
    <div class="tk-modal">
        <div class="tk-modal-header">
            <span class="tk-modal-title">Nuevo ticket de soporte</span>
            <button class="tk-modal-close" wire:click="$set('showNewModal', false)" aria-label="Cerrar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="tk-modal-body">
            <div>
                <label class="tk-label">Cliente</label>
                <div class="tk-tab-row">
                    <button type="button" class="tk-tab-btn {{ $newContactMode === 'existing' ? 'active' : '' }}" wire:click="$set('newContactMode','existing')">Buscar contacto</button>
                    <button type="button" class="tk-tab-btn {{ $newContactMode === 'new' ? 'active' : '' }}" wire:click="$set('newContactMode','new')">Nuevo contacto</button>
                </div>
            </div>

            @if($newContactMode === 'existing')
                <div>
                    <input type="text" class="tk-input" wire:model.live.debounce.250ms="newContactSearch" placeholder="Buscar por nombre, email o teléfono…">
                    @if($suggestions->isNotEmpty())
                        <div class="tk-contact-list">
                            @foreach($suggestions as $c)
                                @php $ci = strtoupper(substr($c->name ?? $c->email ?? '?', 0, 1)); $cc = $palette[abs(crc32($c->name ?? '')) % count($palette)]; @endphp
                                <div class="tk-contact-item {{ $newContactId === $c->id ? 'selected' : '' }}" wire:click="selectContact({{ $c->id }})">
                                    <div class="tk-avatar" style="background:{{ $cc }};width:26px;height:26px;font-size:10px">{{ $ci }}</div>
                                    <div>
                                        <div style="font-size:13px;font-weight:600">{{ $c->name ?? '—' }}</div>
                                        <div style="font-size:11px;color:#64748b">{{ $c->email }} {{ $c->phone ? '· '.$c->phone : '' }}</div>
                                    </div>
                                    @if($newContactId === $c->id)
                                        <svg style="margin-left:auto;flex-shrink:0;color:#16a34a" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif(strlen($newContactSearch) >= 2)
                        <div style="font-size:12px;color:#94a3b8;padding:8px 2px">Sin resultados. Puedes crear un contacto nuevo en la otra pestaña.</div>
                    @endif
                </div>
            @else
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div><label class="tk-label">Nombre</label><input type="text" class="tk-input" wire:model="newClientName" placeholder="Juan Pérez"></div>
                    <div><label class="tk-label">Teléfono</label><input type="text" class="tk-input" wire:model="newClientPhone" placeholder="+1 555 0000"></div>
                </div>
                <div>
                    <label class="tk-label">Email <span style="color:#ef4444">*</span></label>
                    <input type="email" class="tk-input" wire:model="newClientEmail" placeholder="cliente@email.com">
                    <div style="font-size:10.5px;color:#94a3b8;margin-top:4px">Se enviará la confirmación del ticket a este correo.</div>
                </div>
            @endif

            <div>
                <label class="tk-label">Asunto <span style="color:#ef4444">*</span></label>
                <input type="text" class="tk-input" wire:model="newSubject" placeholder="Describe brevemente el problema…">
            </div>
            <div>
                <label class="tk-label">Mensaje inicial (opcional)</label>
                <textarea class="tk-input tk-textarea" wire:model="newMessage" placeholder="Escribe el primer mensaje del agente al cliente…"></textarea>
            </div>
        </div>
        <div class="tk-modal-footer">
            <button class="tk-btn-ghost" wire:click="$set('showNewModal', false)">Cancelar</button>
            <button class="tk-btn-primary" wire:click="createTicket" @if(!trim($newSubject)) disabled @endif>
                Crear ticket y enviar email
            </button>
        </div>
    </div>
</div>
@endif

</x-filament-panels::page>
