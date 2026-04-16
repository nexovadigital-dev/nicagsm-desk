<x-filament-panels::page>

{{-- ══ Alerta de llamada a agente ══ --}}
@php 
    $incomingCalls = $this->incomingAgentCalls(); 
    \Carbon\Carbon::setLocale('es');
    // Timezone de la organización (configurado en Perfil → Datos de la empresa)
    $orgTimezone = auth()->user()?->organization?->timezone ?? config('app.timezone', 'UTC');
@endphp

<div x-data="{
    ringing: $wire.entangle('hasIncomingCall'),
    dismissed: false,
    audioCtx: null,
    ringInterval: null,

    startRing() {
        if (this.dismissed || !this.ringing) return;
        try {
            this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            this.ringInterval = setInterval(() => {
                if (!this.ringing || this.dismissed) { clearInterval(this.ringInterval); return; }
                this.playBeep();
            }, 1800);
            this.playBeep();
        } catch(e) {}
    },
    playBeep() {
        if (!this.audioCtx) return;
        const o1 = this.audioCtx.createOscillator();
        const o2 = this.audioCtx.createOscillator();
        const g  = this.audioCtx.createGain();
        o1.connect(g); o2.connect(g); g.connect(this.audioCtx.destination);
        o1.type = 'sine'; o1.frequency.value = 880;
        o2.type = 'sine'; o2.frequency.value = 1100;
        g.gain.setValueAtTime(0, this.audioCtx.currentTime);
        g.gain.linearRampToValueAtTime(0.18, this.audioCtx.currentTime + 0.05);
        g.gain.linearRampToValueAtTime(0, this.audioCtx.currentTime + 0.35);
        o1.start(); o2.start();
        o1.stop(this.audioCtx.currentTime + 0.4);
        o2.stop(this.audioCtx.currentTime + 0.4);
    },
    dismiss() {
        this.dismissed = true; this.ringing = false;
        clearInterval(this.ringInterval);
    }
}"
x-init="
    if (ringing) startRing();
    $watch('ringing', v => {
        if (v && !dismissed) startRing();
        if (!v) { dismissed = false; clearInterval(ringInterval); }
    });
">

@if($hasIncomingCall)
<div x-show="ringing && !dismissed" x-transition.opacity.duration.300ms
     style="position:fixed; top:20px; left:50%; transform:translateX(-50%); z-index:9999; display:flex; align-items:center; gap:12px; background:#fef3c7; border:1px solid #fcd34d; border-radius:16px; padding:10px 16px; box-shadow:0 10px 25px rgba(245,158,11,0.25); animation:nx-ring-pulse 2s ease infinite alternate">
    <div style="width:34px;height:34px;border-radius:50%;background:#f59e0b22;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" width="18" height="18">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
        </svg>
    </div>
    <div style="flex:1;min-width:0;line-height:1.2">
        <div style="font-size:13px;font-weight:700;color:#92400e">
            {{ $incomingCalls->count() }} usuario(s) esperando
        </div>
        <div style="font-size:11.5px;color:#b45309;margin-top:2px">
            @foreach($incomingCalls->take(2) as $call)
                <span>{{ $call->client_name ?: 'Visitante' }}</span>{{ !$loop->last ? ' · ' : '' }}
            @endforeach
        </div>
    </div>
    {{-- Acción rápida: ir al primer ticket que solicita agente --}}
    @if($incomingCalls->first())
        <button wire:click="selectTicket({{ $incomingCalls->first()->id }})" @click="dismiss()"
                style="background:var(--nx-accent,#22c55e);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:5px;box-shadow:0 2px 8px rgba(34,197,94,0.3)">
            Atender
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" width="12" height="12"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
    @endif
    <button @click="dismiss()"
            style="background:none;border:none;padding:6px;font-size:16px;color:#92400e;opacity:.6;cursor:pointer">
        ✕
    </button>
</div>
@endif

<style>
@keyframes nx-ring-pulse {
    from { box-shadow: 0 0 0 0 rgba(245,158,11,.4); transform: translateX(-50%) translateY(0); }
    to   { box-shadow: 0 0 0 8px rgba(245,158,11,0); transform: translateX(-50%) translateY(-2px); }
}
</style>

</div>

<div x-data="{
    soundEnabled: localStorage.getItem('nx_inbox_sound') !== 'false',
    prevTicketCount: {{ $this->tickets()->count() }},
    audioCtx: null,

    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        localStorage.setItem('nx_inbox_sound', this.soundEnabled ? 'true' : 'false');
    },

    getAudioCtx() {
        if (!this.audioCtx) {
            try { this.audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) {}
        }
        return this.audioCtx;
    },

    playNewTicket() {
        if (!this.soundEnabled) return;
        const ctx = this.getAudioCtx();
        if (!ctx) return;
        // Dos tonos suaves tipo 'pop' — amigable, no molesto
        [880, 1100].forEach((freq, i) => {
            const o = ctx.createOscillator();
            const g = ctx.createGain();
            o.connect(g); g.connect(ctx.destination);
            o.type = 'sine'; o.frequency.value = freq;
            const t = ctx.currentTime + i * 0.12;
            g.gain.setValueAtTime(0, t);
            g.gain.linearRampToValueAtTime(0.12, t + 0.04);
            g.gain.linearRampToValueAtTime(0, t + 0.22);
            o.start(t); o.stop(t + 0.25);
        });
    },

    checkNewTickets(currentCount) {
        if (currentCount > this.prevTicketCount) {
            this.playNewTicket();
        }
        this.prevTicketCount = currentCount;
    }
}"
@nexova-new-message.window="$wire.$refresh().then(() => { const el = document.querySelector('[data-inbox-count]'); if (typeof $data !== 'undefined' && $data.checkNewTickets) $data.checkNewTickets(el ? parseInt(el.dataset.inboxCount) : 0); })"
class="nx-inbox" wire:poll.3s>

    {{-- ═══════════════════════════
         SIDEBAR — Lista de tickets
    ════════════════════════════════ --}}
    <aside class="nx-sidebar">

        @php $liveTickets = $this->tickets(); $activeCount = $liveTickets->count(); @endphp
        <div class="nx-sidebar__header" data-inbox-count="{{ $activeCount }}">
            <span class="nx-sidebar__title">Conversaciones</span>
            <div style="display:flex;align-items:center;gap:6px;margin-left:auto">
                @if($activeCount > 0)
                    <span style="font-size:11px;font-weight:700;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;padding:2px 8px;border-radius:99px">{{ $activeCount }}</span>
                @endif
                {{-- Toggle sonido nuevo visitante --}}
                <button @click="toggleSound()" :title="soundEnabled ? 'Silenciar notificaciones' : 'Activar notificaciones de sonido'"
                    style="width:28px;height:28px;border-radius:7px;border:1px solid var(--nx-bd,#e2e8f0);background:var(--nx-bg,#fff);cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s"
                    :style="soundEnabled ? 'border-color:var(--nx-accent,#22c55e);color:var(--nx-accent,#22c55e)' : 'color:#9ca3af'">
                    <svg x-show="soundEnabled" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="14" height="14">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/>
                    </svg>
                    <svg x-show="!soundEnabled" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="14" height="14">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                        <line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="nx-sidebar__tabs">
            <button wire:click="switchView('active')"
                class="nx-tab {{ $inboxView === 'active' ? 'nx-tab--active' : '' }}">
                En Vivo
                @if($activeCount > 0)
                    <span class="nx-tab-count">{{ $activeCount }}</span>
                @endif
            </button>
            <button wire:click="switchView('history')"
                class="nx-tab {{ $inboxView === 'history' ? 'nx-tab--active' : '' }}">
                Historial
            </button>
        </div>

        {{-- Filtro departamento --}}
        @php $sidebarDepts = $this->availableDepartments; @endphp
        @if($sidebarDepts->isNotEmpty())
        <div style="padding: 4px 12px 0;">
            <select wire:model.live="filterDept"
                    style="width:100%;padding:6px 8px;border-radius:7px;border:1px solid var(--nx-border,rgba(128,128,128,.2));background:var(--nx-bg2,rgba(128,128,128,.07));font-size:11.5px;color:inherit;outline:none;font-family:inherit;box-sizing:border-box">
                <option value="all">Todos los departamentos</option>
                <option value="none">Sin departamento</option>
                @foreach($sidebarDepts as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Buscador --}}
        <div style="padding: 8px 12px 4px;">
            <div style="position:relative">
                <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);pointer-events:none;opacity:.4" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nombre, email, mensaje…"
                    style="width:100%;padding:7px 10px 7px 30px;border-radius:8px;border:1px solid var(--nx-border, rgba(128,128,128,.2));background:var(--nx-bg2, rgba(128,128,128,.07));font-size:12px;color:inherit;outline:none;font-family:inherit;box-sizing:border-box;transition:border-color .15s"
                    onfocus="this.style.borderColor='#22c55e'"
                    onblur="this.style.borderColor=''"
                >
                @if($search)
                <button wire:click="$set('search','')" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;opacity:.5;padding:2px;display:flex;color:inherit" title="Limpiar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                @endif
            </div>
        </div>

        <nav class="nx-ticket-list">

            {{-- ═══ BARRA DE ACCIÓN — aparece cuando hay conversaciones seleccionadas ═══ --}}
            @php $selCount = count($selectedTicketIds); @endphp
            @if($selCount > 0)
            <div class="nx-bulk-bar">
                <div class="nx-bulk-bar__info" style="gap:6px">
                    <div class="nx-bulk-bar__check">
                        <svg fill="none" stroke="#fff" viewBox="0 0 24 24" width="13" height="13" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="nx-bulk-bar__count">{{ $selCount }} <span style="font-weight:400; opacity:.7">(sel)</span></span>
                </div>
                <div class="nx-bulk-bar__actions">
                    <button wire:click="selectAllTickets" class="nx-bulk-bar__btn nx-bulk-bar__btn--ghost" title="Seleccionar todas" style="padding:4px 8px">Todas</button>
                    <button wire:click="deleteSelectedTickets"
                            wire:confirm="¿Eliminar {{ $selCount }} {{ $selCount === 1 ? 'conversación' : 'conversaciones' }}? Esta acción es permanente."
                            class="nx-bulk-bar__btn nx-bulk-bar__btn--danger" title="Eliminar" style="padding:4px 10px">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Eliminar
                    </button>
                    <button wire:click="clearTicketSelection" class="nx-bulk-bar__cancel" title="Cancelar" style="width:24px;height:24px">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            @endif

            @forelse ($liveTickets as $ticket)
                @php
                    $active     = $selectedTicketId === $ticket->id;
                    $isSelected = in_array($ticket->id, $selectedTicketIds);
                    $lastMsg    = $ticket->messages->first();
                    $preview    = $lastMsg ? \Illuminate\Support\Str::limit($lastMsg->content, 55) : 'Sin mensajes aún';
                    $palette    = ['#0ea5e9','#10b981','#f59e0b','#ef4444','#ec4899','#6366f1'];
                    $color      = $palette[abs(crc32($ticket->client_name)) % count($palette)];
                    $ticketDept = $ticket->department;
                @endphp

                {{-- Tarjeta de conversación — hover sobre avatar → checkbox --}}
                <div wire:key="ticket-{{ $ticket->id }}"
                     class="nx-ticket {{ $active && !$isSelected ? 'nx-ticket--active' : '' }} {{ $isSelected ? 'nx-ticket--selected' : '' }}"
                     x-data="{ hov: false }"
                     @mouseenter="hov = true" @mouseleave="hov = false">

                    {{-- Avatar / Checkbox area --}}
                    <div class="nx-avatar-wrap"
                         wire:click.stop="toggleTicketSelection({{ $ticket->id }})"
                         title="{{ $isSelected ? 'Deseleccionar' : 'Seleccionar' }}">
                        <div class="nx-avatar" style="background:{{ $color }}; opacity:1; transform:scale(1);">
                            {{ strtoupper(substr($ticket->client_name ?? 'V', 0, 1)) }}
                        </div>
                        <div class="nx-avatar-cb {{ $isSelected ? 'nx-avatar-cb--on' : '' }}"
                             :style="(hov || {{ $isSelected ? 'true' : 'false' }}) ? 'opacity:1;transform:scale(1)' : 'opacity:0;transform:scale(.85)'">
                            @if($isSelected)
                            <svg fill="none" stroke="#fff" viewBox="0 0 24 24" width="12" height="12" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </div>
                    </div>

                    {{-- Body: abre ticket o toggle selección si hay seleccionadas --}}
                    <div class="nx-ticket__body"
                         wire:click="selectTicket({{ $ticket->id }})"
                         style="flex:1;min-width:0;cursor:pointer">
                        <div class="nx-ticket__top">
                            <span class="nx-ticket__name">{{ $ticket->conversation_name ?? $ticket->client_name }}@if($ticket->is_support_ticket) <span style="font-weight:400;opacity:.6;font-size:.9em">(Ticket #{{ $ticket->ticket_number }})</span>@elseif($ticket->platform === 'telegram') <span style="font-weight:400;opacity:.6;font-size:.9em">(Telegram)</span>@endif</span>
                            <span class="nx-ticket__time">{{ $ticket->updated_at->setTimezone($orgTimezone)->diffForHumans(null, true, true) }}</span>
                        </div>
                        <div class="nx-ticket__bottom">
                            <span class="nx-ticket__preview">{{ $preview }}</span>
                            <span class="nx-pill nx-pill--{{ $ticket->status }}">
                                @if($ticket->status === 'bot') Bot
                                @elseif($ticket->status === 'human') Agente
                                @else Cerrado
                                @endif
                            </span>
                        </div>
                        @if($ticketDept)
                        <div style="margin-top:3px;display:flex;align-items:center;gap:4px">
                            <span style="width:6px;height:6px;border-radius:50%;background:{{ $ticketDept->color }};flex-shrink:0"></span>
                            <span style="font-size:10.5px;color:var(--nx-muted,#6b7280);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $ticketDept->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="nx-empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                    </svg>
                    @if($search)
                        <p>Sin resultados para "{{ Str::limit($search, 20) }}"</p>
                    @else
                        <p>{{ $inboxView === 'history' ? 'Sin conversaciones en el historial' : 'Sin conversaciones activas' }}</p>
                    @endif
                </div>
            @endforelse
        </nav>
    </aside>

    {{-- ═══════════════════════════
         PANEL PRINCIPAL — Chat
    ════════════════════════════════ --}}
    <main class="nx-chat">

        @php $ticket = $this->selectedTicket(); @endphp

        @if ($ticket)

            {{-- Cabecera --}}
            <header class="nx-chat__header">
                @php
                    $palette = ['#0ea5e9','#10b981','#f59e0b','#ef4444','#ec4899','#6366f1'];
                    $color   = $palette[abs(crc32($ticket->client_name)) % count($palette)];
                @endphp
                <div class="nx-chat__header-top">
                    <div class="nx-avatar nx-avatar--lg" style="background:{{ $color }}">
                        {{ strtoupper(substr($ticket->client_name, 0, 1)) }}
                    </div>
                    <div class="nx-chat__info">
                        <strong>{{ $ticket->conversation_name ?? $ticket->client_name }}</strong>
                        <span>
                            #{{ $ticket->id }}
                            @if($ticket->platform === 'telegram' && $ticket->telegram_id)
                                &middot; {{ $ticket->telegram_username ? '@' . $ticket->telegram_username : 'Chat ID: ' . $ticket->telegram_id }}
                            @endif
                            &middot; {{ $ticket->created_at->setTimezone($orgTimezone)->translatedFormat('d M, H:i') }}
                            @if($ticket->status === 'human')
                                &middot; <span class="nx-status-label nx-status-label--human">Agente activo</span>
                            @elseif($ticket->status === 'closed')
                                &middot; <span class="nx-status-label nx-status-label--closed">Cerrado</span>
                            @else
                                &middot; <span class="nx-status-label nx-status-label--bot">Bot activo</span>
                            @endif
                            @if($ticket->widget)
                                &middot; <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11" style="display:inline;vertical-align:middle;margin-right:2px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>{{ $ticket->widget->name }}
                            @endif
                        </span>
                        {{-- Visitor URL --}}
                        @if($ticket->visitor_page)
                        <span style="font-size:10.5px;color:var(--c-sub,#6b7280);display:flex;align-items:center;gap:4px;margin-top:2px">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <a href="{{ $ticket->visitor_page }}" target="_blank"
                               style="color:var(--c-sub,#6b7280);text-decoration:none;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                               title="{{ $ticket->visitor_page }}">
                                {{ parse_url($ticket->visitor_page, PHP_URL_HOST) }}{{ parse_url($ticket->visitor_page, PHP_URL_PATH) }}
                            </a>
                        </span>
                        @endif
                        {{-- Visitor info row --}}
                        @if($ticket->visitor_country || $ticket->visitor_device || $ticket->visitor_browser)
                        <span style="font-size:10.5px;color:var(--c-sub,#9ca3af);margin-top:1px">
                            @if($ticket->visitor_country) {{ $ticket->visitor_country }}{{ $ticket->visitor_city ? ', '.$ticket->visitor_city : '' }} &middot; @endif
                            @if($ticket->visitor_device) {{ $ticket->visitor_device }} @endif
                            @if($ticket->visitor_browser) · {{ $ticket->visitor_browser }} @endif
                        </span>
                        @endif
                    </div>
                </div>
                <div class="nx-chat__actions">
                    @if ($ticket->status === 'bot')
                        {{-- Bot manejando — opción de tomar el chat --}}
                        <button wire:click="assignToMe" class="nx-btn nx-btn--assign">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Tomar chat
                        </button>
                    @endif

                    @if ($ticket->status === 'human' && ! $ticket->assigned_agent)
                        {{-- Visitante solicitó agente — pendiente de aceptar o rechazar --}}
                        <span style="font-size:11px;font-weight:600;color:#d97706;display:flex;align-items:center;gap:4px;animation:nx-ring-alert .8s ease infinite alternate">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Solicita agente
                        </span>
                        <button wire:click="assignToMe" class="nx-btn nx-btn--assign">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Aceptar
                        </button>
                        <button wire:click="rejectAgentRequest"
                                wire:confirm="¿Rechazar la solicitud? El bot retomará el chat."
                                class="nx-btn nx-btn--ghost" style="color:#dc2626;border-color:#fca5a5">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Rechazar
                        </button>
                    @endif

                    @if ($ticket->status === 'human' && $ticket->assigned_agent)
                        {{-- Agente asignado y activo --}}
                        <span class="nx-agent-tag">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $ticket->assigned_agent }}
                        </span>
                        {{-- Pasar al bot: NO para tickets de soporte ni Telegram --}}
                        @if(! $ticket->is_support_ticket && $ticket->platform !== 'telegram')
                        <button wire:click="handBackToBot"
                                wire:confirm="¿Devolver al bot? El asistente retomará la conversación."
                                class="nx-btn nx-btn--ghost">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Pasar al bot
                        </button>
                        @endif
                    @endif
                    @if ($ticket->status !== 'closed' && ! $ticket->is_support_ticket && $ticket->platform !== 'telegram')
                        <button wire:click="openTicketModal" class="nx-btn nx-btn--ticket">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Crear ticket
                        </button>
                    @elseif($ticket->is_support_ticket)
                        <span class="nx-ticket-badge-tag">#{{ $ticket->ticket_number }}</span>
                    @elseif($ticket->platform === 'telegram' && $ticket->status !== 'closed')
                        {{-- Telegram: no se crea ticket de soporte por email --}}
                        <span style="font-size:11px;color:#64748b;display:flex;align-items:center;gap:4px">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 12l9-5-9-5-9 5 9 5z"/></svg>
                            Telegram
                        </span>
                    @endif
                    @if ($ticket->status !== 'closed')
                        <button wire:click="closeTicket"
                                wire:confirm="¿Cerrar este ticket? El cliente no podrá enviar más mensajes."
                                class="nx-btn nx-btn--danger">
                            Cerrar
                        </button>
                    @endif
                </div>
            </header>

            {{-- Mensajes --}}
            @php
                $allMsgs     = $this->chatMessages();
                $lastUserMsg = $allMsgs->where('sender_type', 'user')->last();
                $lastUserMsgId = $lastUserMsg?->id ?? 0;
                $ticket = $this->selectedTicket();
                $isAssignedToMe = $ticket && $ticket->status === 'human' && auth()->id();
            @endphp
            <section class="nx-messages"
                     data-last-user-msg="{{ $lastUserMsgId }}"
                     x-data="{
                         loading: !sessionStorage.getItem('nx_inbox_ready'),
                         prevUserMsgId: {{ $lastUserMsgId }},
                         snap() { this.$el.scrollTop = this.$el.scrollHeight },
                         checkNewUserMsg() {
                             const newId = parseInt(this.$el.dataset.lastUserMsg || '0');
                             if (newId && newId !== this.prevUserMsgId) {
                                 this.prevUserMsgId = newId;
                                 // Solo sonar si el ticket está asignado (modo human) y el sonido está activo
                                 const soundBtn = document.querySelector('[data-inbox-sound]');
                                 const soundOn = !soundBtn || soundBtn.dataset.inboxSound !== 'false';
                                 if (soundOn) {
                                     const ctx = window._nxAudioCtx || (window._nxAudioCtx = new (window.AudioContext || window.webkitAudioContext)());
                                     if (ctx.state === 'suspended') ctx.resume();
                                     [880, 1100].forEach((freq, i) => {
                                         const o = ctx.createOscillator(), g = ctx.createGain();
                                         o.connect(g); g.connect(ctx.destination);
                                         o.type = 'sine'; o.frequency.value = freq;
                                         const t = ctx.currentTime + i * 0.13;
                                         g.gain.setValueAtTime(0, t);
                                         g.gain.linearRampToValueAtTime(0.13, t + 0.04);
                                         g.gain.linearRampToValueAtTime(0, t + 0.25);
                                         o.start(t); o.stop(t + 0.28);
                                     });
                                 }
                             }
                         }
                     }"
                     x-init="
                         $nextTick(() => {
                             loading = false;
                             sessionStorage.setItem('nx_inbox_ready', '1');
                             $nextTick(() => snap());
                         });
                         setTimeout(() => { loading = false; }, 800);
                     "
                     x-on:livewire:updated.window="$nextTick(() => { snap(); checkNewUserMsg(); })">

                {{-- Skeleton loading --}}
                <template x-if="loading">
                    <div class="nx-skeleton-wrap">
                        <div class="nx-skeleton-msg nx-skeleton-msg--bot">
                            <div class="nx-skeleton-avatar"></div>
                            <div class="nx-skeleton-lines">
                                <div class="nx-skeleton-line" style="width:72%"></div>
                                <div class="nx-skeleton-line" style="width:55%"></div>
                            </div>
                        </div>
                        <div class="nx-skeleton-msg nx-skeleton-msg--user">
                            <div class="nx-skeleton-lines" style="align-items:flex-end">
                                <div class="nx-skeleton-line" style="width:38%"></div>
                            </div>
                        </div>
                        <div class="nx-skeleton-msg nx-skeleton-msg--bot">
                            <div class="nx-skeleton-avatar"></div>
                            <div class="nx-skeleton-lines">
                                <div class="nx-skeleton-line" style="width:80%"></div>
                                <div class="nx-skeleton-line" style="width:65%"></div>
                                <div class="nx-skeleton-line" style="width:45%"></div>
                            </div>
                        </div>
                        <div class="nx-skeleton-msg nx-skeleton-msg--user">
                            <div class="nx-skeleton-lines" style="align-items:flex-end">
                                <div class="nx-skeleton-line" style="width:50%"></div>
                                <div class="nx-skeleton-line" style="width:40%"></div>
                            </div>
                        </div>
                    </div>
                </template>




                @php $lastDateKey = null; @endphp
                @forelse ($allMsgs as $msg)
                    @php
                        $isUser = $msg->sender_type === 'user';
                        $vcPalette = ['#0ea5e9','#10b981','#f59e0b','#ef4444','#ec4899','#6366f1'];
                        $vcColor   = $vcPalette[abs(crc32($ticket->client_name ?? 'V')) % 6];

                        // Date separator
                        $msgDate2    = $msg->created_at->setTimezone($orgTimezone);
                        $dateKey2    = $msgDate2->format('Y-m-d');
                        $showSep2    = $dateKey2 !== $lastDateKey;
                        $lastDateKey = $dateKey2;
                        $todayKey    = now()->setTimezone($orgTimezone)->format('Y-m-d');
                        $yesterKey   = now()->setTimezone($orgTimezone)->subDay()->format('Y-m-d');
                        $sepLabel2   = match($dateKey2) {
                            $todayKey  => 'Hoy',
                            $yesterKey => 'Ayer',
                            default    => $msgDate2->translatedFormat('d M Y'),
                        };
                    @endphp

                    @if($showSep2)
                        <div class="nx-msg--system" wire:key="datesep-{{ $dateKey2 }}-{{ $ticket->id }}">
                            <span>{{ $sepLabel2 }}</span>
                        </div>
                    @endif

                    @if ($msg->sender_type === 'system')
                        @php
                            $isTicketOpened = str_starts_with($msg->content, '__TICKET_OPENED__:');
                            $ticketNum      = $isTicketOpened ? substr($msg->content, strlen('__TICKET_OPENED__:')) : null;
                            $sysLabel = match(true) {
                                $isTicketOpened => null, // rendered separately below
                                $msg->content === '__AGENT_CTA__' => '📞 Cliente solicitó atención con un agente',
                                default => $msg->content,
                            };
                        @endphp
                        @if($isTicketOpened)
                            {{-- Tarjeta especial: chat finalizado, ticket de soporte creado --}}
                            <div wire:key="msg-{{ $msg->id }}"
                                 style="margin:12px auto;max-width:420px;background:linear-gradient(135deg,#eff6ff,#f0fdf4);border:1.5px solid #bfdbfe;border-radius:14px;padding:14px 18px;text-align:center;box-shadow:0 2px 8px rgba(99,102,241,.08)">
                                <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:8px">
                                    <svg fill="none" stroke="#6366f1" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    <span style="font-size:13px;font-weight:700;color:#4338ca">Ticket de Soporte Creado</span>
                                </div>
                                <div style="font-size:15px;font-weight:800;color:#312e81;letter-spacing:.5px;margin-bottom:6px">#{{ $ticketNum }}</div>
                                <div style="font-size:11.5px;color:#64748b;line-height:1.5">
                                    Esta conversación ha sido finalizada y trasladada a soporte por correo electrónico.<br>
                                    El cliente recibirá seguimiento en su bandeja.
                                </div>
                                <div style="margin-top:10px;display:inline-flex;align-items:center;gap:5px;background:#e0e7ff;border-radius:99px;padding:4px 12px;font-size:11px;font-weight:600;color:#4338ca">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Chat finalizado — Ver en sección Tickets
                                </div>
                            </div>
                        @else
                            <div class="nx-msg--system" wire:key="msg-{{ $msg->id }}">
                                <span>{{ $sysLabel }}</span>
                            </div>
                        @endif
                        @continue
                    @endif

                    @if ($msg->sender_type === 'note')
                        <div class="nx-msg--note" wire:key="msg-{{ $msg->id }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" style="flex-shrink:0;color:#d97706">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>{{ $msg->content }}</span>
                            <time style="font-size:10px;color:#92400e;opacity:.6;margin-left:auto;white-space:nowrap">{{ $msg->created_at->setTimezone($orgTimezone)->format('H:i') }}</time>
                        </div>
                        @continue
                    @endif

                    {{-- Mensaje normal --}}
                    <div class="nx-msg-wrap"
                         wire:key="msg-{{ $msg->id }}"
                         x-data="{ hovered: false }"
                         @mouseenter="hovered = true"
                         @mouseleave="hovered = false">

                    <div class="nx-msg {{ $isUser ? 'nx-msg--user' : 'nx-msg--' . $msg->sender_type }}">

                        @if ($isUser)
                            {{-- Visitor avatar — LEFT --}}
                            <div class="nx-msg__avatar" style="background:{{ $vcColor }}">
                                {{ strtoupper(substr($ticket->client_name ?? 'V', 0, 1)) }}
                            </div>
                        @elseif ($msg->sender_type === 'agent')
                            {{-- Agent avatar — RIGHT --}}
                            @if($currentAgentAvatar)
                                <div class="nx-msg__avatar" style="padding:0;overflow:hidden">
                                    <img src="{{ $currentAgentAvatar }}" alt="A" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                                </div>
                            @else
                                <div class="nx-msg__avatar" style="background:#334155">A</div>
                            @endif
                        @else
                            {{-- Bot avatar — LEFT --}}
                            @php
                                $rawBotAvatar = $ticket->widget?->bot_avatar ?? null;
                                $botAvatarUrl = $rawBotAvatar
                                    ? (str_starts_with($rawBotAvatar, 'http') ? $rawBotAvatar : \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim(str_replace('/storage/', '', $rawBotAvatar), '/')))
                                    : null;
                            @endphp
                            @if($botAvatarUrl)
                                <div class="nx-msg__avatar" style="padding:0;overflow:hidden">
                                    <img src="{{ $botAvatarUrl }}" alt="Bot" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                                </div>
                            @else
                                <div class="nx-msg__avatar" style="background:#64748b">N</div>
                            @endif
                        @endif

                        <div class="nx-msg__content">
                            <div class="nx-bubble">@if($msg->attachment_path)
@php $attUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($msg->attachment_path); @endphp
@if(str_starts_with($msg->attachment_type ?? '', 'image/'))<a href="{{ $attUrl }}" target="_blank" style="display:block;margin-bottom:{{ $msg->content ? '6px' : '0' }}"><img src="{{ $attUrl }}" alt="{{ $msg->attachment_name }}" style="max-width:220px;max-height:180px;border-radius:8px;display:block;object-fit:cover"></a>@else<a href="{{ $attUrl }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:5px 10px;background:rgba(255,255,255,.1);border-radius:6px;text-decoration:none;color:inherit;margin-bottom:{{ $msg->content ? '5px' : '0' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="14 2 14 8 20 8"/></svg>{{ $msg->attachment_name ?? 'Archivo' }}</a>@endif
@endif
@php
    $fmtContent = e(trim($msg->content));
    $fmtContent = preg_replace('/\[([^\]]+)\]\((https?:\/\/[^\)]+)\)/', '<a href="$2" target="_blank" style="color:var(--nx-accent);text-decoration:underline">$1</a>', $fmtContent);
@endphp
{!! nl2br($fmtContent) !!}</div>
                            <time class="nx-msg__time">
                                @if($msg->sender_type === 'agent') Agente &middot;
                                @elseif($msg->sender_type === 'bot') Nexova IA &middot;
                                @endif
                                {{ $msg->created_at->setTimezone($orgTimezone)->format('H:i') }}
                            </time>
                        </div>

                    </div>{{-- end .nx-msg --}}
                    </div>{{-- end .nx-msg-wrap --}}
                @empty
                    <div class="nx-messages__empty">
                        <p>Aún no hay mensajes en esta conversación.</p>
                    </div>
                @endforelse

                {{-- Sneak-peek: texto que el visitante está escribiendo --}}
                @php $sneakText = $this->typingPreview; @endphp
                @if($sneakText)
                <div style="display:flex;align-items:flex-end;gap:8px;padding:4px 0 2px;opacity:.75">
                    <div style="width:28px;height:28px;border-radius:50%;background:#e2e8f0;flex-shrink:0;display:flex;align-items:center;justify-content:center">
                        <svg fill="none" stroke="#64748b" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div style="max-width:68%;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:14px 14px 14px 2px;padding:8px 12px;position:relative">
                        <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:3px;display:flex;align-items:center;gap:5px">
                            <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#94a3b8;animation:nx-sneak-pulse 1.2s ease-in-out infinite"></span>
                            escribiendo…
                        </div>
                        <div style="font-size:13px;color:#475569;word-break:break-word;font-style:italic">{{ $sneakText }}</div>
                    </div>
                </div>
                <style>
                @keyframes nx-sneak-pulse {
                    0%, 100% { opacity:.4; transform:scale(1); }
                    50%       { opacity:1; transform:scale(1.3); }
                }
                </style>
                @endif
            </section>


            {{-- Compositor --}}
            @if ($ticket->status !== 'closed')
                <footer class="nx-composer"
                    x-data="{
                        open: false,
                        noteMode: false,
                        enterSend: JSON.parse(localStorage.getItem('nx_enter_send') || 'false'),
                        canned: @js(\App\Models\CannedResponse::orderBy('shortcut')->get(['shortcut','content'])->toArray()),
                        filtered: [],
                        query: '',
                        selected: 0,
                        toggleEnterSend() {
                            this.enterSend = !this.enterSend;
                            localStorage.setItem('nx_enter_send', JSON.stringify(this.enterSend));
                        },
                        onInput(val) {
                            const m = val.match(/\/(\w*)$/);
                            if (m) {
                                this.query = m[1].toLowerCase();
                                this.filtered = this.canned.filter(r => r.shortcut.startsWith(this.query));
                                this.open = this.filtered.length > 0;
                                this.selected = 0;
                            } else {
                                this.open = false;
                                this.filtered = [];
                            }
                        },
                        pick(item) {
                            const ta = this.$refs.ta;
                            const val = ta.value;
                            const newVal = val.replace(/\/\w*$/, item.content);
                            ta.value = newVal;
                            ta.dispatchEvent(new Event('input'));
                            @this.set('replyContent', newVal);
                            this.open = false;
                        },
                        onKeydown(e) {
                            if (this.open) {
                                if (e.key === 'ArrowDown') { e.preventDefault(); this.selected = Math.min(this.selected+1, this.filtered.length-1); return; }
                                if (e.key === 'ArrowUp')   { e.preventDefault(); this.selected = Math.max(this.selected-1, 0); return; }
                                if (e.key === 'Enter')  { e.preventDefault(); this.pick(this.filtered[this.selected]); return; }
                                if (e.key === 'Escape') { this.open = false; return; }
                            }
                            // Ctrl+Enter: enviar/nota según modo
                            if (e.key === 'Enter' && e.ctrlKey) {
                                e.preventDefault();
                                this.noteMode ? $wire.sendNote() : $wire.sendReply();
                                return;
                            }
                            // Enter solo (sin Shift) cuando enterSend está activo
                            if (e.key === 'Enter' && !e.shiftKey && this.enterSend && !this.open) {
                                e.preventDefault();
                                this.noteMode ? $wire.sendNote() : $wire.sendReply();
                            }
                        }
                    }">
                    @if ($ticket->status === 'bot')
                        @if ($ticket->platform === 'telegram')
                            {{-- TELEGRAM + BOT: bloquear compositor — el agente debe tomar el chat primero --}}
                            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;padding:18px 20px;background:var(--nx-bg2,rgba(128,128,128,.05));border-top:1px solid var(--nx-bd,rgba(128,128,128,.15));text-align:center">
                                <div style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#64748b">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    El bot está atendiendo esta conversación
                                </div>
                                <p style="font-size:12px;color:#94a3b8;margin:0">Asigna el chat para poder responder como agente.</p>
                                <button wire:click="assignToMe"
                                        style="display:inline-flex;align-items:center;gap:6px;background:var(--nx-accent,#22c55e);color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 8px rgba(34,197,94,.25)">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Tomar el chat
                                </button>
                            </div>
                        @else
                            {{-- Chat web + bot: aviso suave, se puede enviar para tomar el control --}}
                            <div class="nx-composer__notice">
                                El bot está respondiendo. Al enviar un mensaje tomarás el control de la conversación.
                            </div>
                        @endif
                    @endif

                    {{-- Canned response picker --}}
                    <div x-show="open" x-cloak class="nx-canned-picker">
                        <div class="nx-canned-header">Respuestas rápidas — ↑↓ navegar · Enter seleccionar · Esc cerrar</div>
                        <template x-for="(item, i) in filtered" :key="item.shortcut">
                            <button type="button" class="nx-canned-item"
                                :class="i === selected ? 'nx-canned-item--active' : ''"
                                @click="pick(item)"
                                @mouseenter="selected = i">
                                <span class="nx-canned-shortcut" x-text="'/' + item.shortcut"></span>
                                <span class="nx-canned-preview" x-text="item.content.substring(0,80) + (item.content.length>80?'…':'')"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Preview adjunto --}}
                    @if ($replyAttachment)
                        <div class="nx-composer__attachment-preview">
                            @php $mime = $replyAttachment->getMimeType(); @endphp
                            @if (str_starts_with($mime, 'image/'))
                                <img src="{{ $replyAttachment->temporaryUrl() }}" alt="preview" class="nx-attachment-thumb" />
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            @endif
                            <span class="nx-attachment-name">{{ $replyAttachment->getClientOriginalName() }}</span>
                            <button wire:click="removeReplyAttachment" class="nx-attachment-remove" title="Quitar adjunto">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    @endif

                    <div class="nx-composer__row">
                        {{-- Adjuntar archivo --}}
                        <label class="nx-composer__attach-btn" title="Adjuntar imagen o PDF (máx. 8 MB)">
                            <input type="file"
                                wire:model="replyAttachment"
                                accept="image/jpeg,image/png,image/gif,image/webp,application/pdf"
                                style="display:none" />
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="16" height="16">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                            </svg>
                        </label>
                        <textarea
                            x-ref="ta"
                            wire:model="replyContent"
                            wire:loading.attr="disabled"
                            wire:target="sendReply,sendNote"
                            rows="1"
                            :placeholder="noteMode ? '\uD83D\uDD12 Nota interna (solo agentes)…' : 'Escribe una respuesta… (/ para respuestas rápidas)'"
                            :class="noteMode ? 'nx-composer__input nx-composer__input--note' : 'nx-composer__input'"
                            @input="onInput($event.target.value); $event.target.style.height='auto'; $event.target.style.height=Math.min($event.target.scrollHeight,120)+'px'"
                            @keydown="onKeydown($event)"
                            @paste="
                                const items = $event.clipboardData?.items;
                                if (items) {
                                    for (const item of items) {
                                        if (item.type.startsWith('image/')) {
                                            $event.preventDefault();
                                            const file = item.getAsFile();
                                            if (!file) break;
                                            const dt = new DataTransfer();
                                            dt.items.add(file);
                                            $el.closest('.nx-composer').querySelector('input[type=file]').files = dt.files;
                                            $el.closest('.nx-composer').querySelector('input[type=file]').dispatchEvent(new Event('change'));
                                            break;
                                        }
                                    }
                                }
                            "
                        ></textarea>
                        <button
                            x-on:click="noteMode ? $wire.sendNote() : $wire.sendReply()"
                            wire:loading.attr="disabled"
                            :class="noteMode ? 'nx-btn nx-btn--note' : 'nx-btn nx-btn--primary'"
                            :title="noteMode ? 'Guardar nota (Ctrl+Enter)' : 'Enviar (Ctrl+Enter)'">
                            <span wire:loading.remove wire:target="sendReply,sendNote">
                                <template x-if="!noteMode">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="15" height="15">
                                        <path d="M3.105 2.289a.75.75 0 00-.826.95l1.903 6.557H13.5a.75.75 0 010 1.5H4.182l-1.903 6.557a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z"/>
                                    </svg>
                                </template>
                                <template x-if="noteMode">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </template>
                            </span>
                            <span wire:loading wire:target="sendReply,sendNote">
                                <div class="nx-send-spinner"></div>
                            </span>
                        </button>
                    </div>
                    <div class="nx-composer__footer">
                        <button type="button" class="nx-note-toggle" :class="noteMode ? 'nx-note-toggle--on' : ''" @click="noteMode = !noteMode" title="Modo nota interna">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span x-text="noteMode ? 'Nota interna activa' : 'Nota interna'"></span>
                        </button>
                        <span class="nx-composer__hint" x-text="enterSend ? 'Enter para enviar · Shift+Enter nueva línea' : 'Ctrl+Enter para enviar'"></span>
                        <label class="nx-enter-toggle">
                            <span class="nx-enter-toggle__label">Enter envía</span>
                            <span class="nx-enter-toggle__track" :class="enterSend ? 'on' : ''" @click="toggleEnterSend()">
                                <span class="nx-enter-toggle__thumb"></span>
                            </span>
                        </label>
                    </div>
                </footer>
            @else
                <footer class="nx-composer nx-composer--closed">
                    Ticket cerrado &mdash; el cliente no puede enviar más mensajes
                </footer>
            @endif

        @else
            <div class="nx-chat__empty">
                <div class="nx-chat__empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5" width="28" height="28">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                    </svg>
                </div>
                <h3>Selecciona una conversación</h3>
                <p>Los mensajes nuevos aparecen automáticamente</p>
            </div>
        @endif

    </main>

    {{-- ═══════════════════════════
         PANEL DERECHO — Visitante
    ════════════════════════════════ --}}
    {{-- ═══════════════════════════
         MODAL — Editar visitante
    ════════════════════════════════ --}}
    @if($showVisitorModal)
    <div class="nx-modal-overlay" wire:click.self="$set('showVisitorModal', false)">
        <div class="nx-modal">
            <div class="nx-modal__header">
                <span class="nx-modal__title">Editar datos del visitante</span>
                <button wire:click="$set('showVisitorModal', false)" class="nx-modal__close" aria-label="Cerrar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="nx-modal__body">
                <div class="nx-modal__field">
                    <label class="nx-modal__label">Nombre</label>
                    <input wire:model="visitorName" type="text" placeholder="Nombre del cliente" class="nx-modal__input">
                </div>
                <div class="nx-modal__field">
                    <label class="nx-modal__label">Email</label>
                    <input wire:model="visitorEmail" type="email" placeholder="correo@ejemplo.com" class="nx-modal__input">
                </div>
                <div class="nx-modal__field">
                    <label class="nx-modal__label">Teléfono / Celular</label>
                    <input wire:model="visitorPhone" type="tel" placeholder="+52 55 1234 5678" class="nx-modal__input">
                </div>
            </div>
            <div class="nx-modal__footer">
                <button wire:click="$set('showVisitorModal', false)" class="nx-btn nx-btn--ghost">Cancelar</button>
                <button wire:click="saveVisitor" class="nx-btn nx-btn--primary">Guardar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════
         MODAL — Crear ticket de soporte
    ════════════════════════════════ --}}
    @if($showTicketModal)
    <div class="nx-modal-overlay" wire:click.self="$set('showTicketModal', false)">
        <div class="nx-modal">
            <div class="nx-modal__header">
                <span class="nx-modal__title">Crear ticket de soporte</span>
                <button wire:click="$set('showTicketModal', false)" class="nx-modal__close" aria-label="Cerrar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="nx-modal__body">
                <p class="nx-modal__desc">Se generará un número de ticket y se enviará confirmación por correo al cliente.</p>

                <div class="nx-modal__field">
                    <label class="nx-modal__label">Asunto / Motivo <span style="color:#dc2626">*</span></label>
                    <input wire:model="ticketSubject"
                           type="text"
                           placeholder="Ej: Problema con el pedido #1234"
                           class="nx-modal__input"
                           autofocus>
                </div>

                <div class="nx-modal__field">
                    <label class="nx-modal__label">Email del cliente</label>
                    <input wire:model="ticketEmailForTicket"
                           type="email"
                           placeholder="cliente@ejemplo.com"
                           class="nx-modal__input">
                    <span class="nx-modal__hint">Si está vacío no se enviará correo de confirmación.</span>
                </div>
            </div>
            <div class="nx-modal__footer">
                <button wire:click="$set('showTicketModal', false)" class="nx-btn nx-btn--ghost">Cancelar</button>
                <button wire:click="createSupportTicket" class="nx-btn nx-btn--primary">Abrir ticket</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════
         MODAL — Contacto duplicado
    ════════════════════════════════ --}}
    @if($showDuplicateModal)
    <div class="nx-modal-overlay">
        <div class="nx-modal" style="max-width:420px">
            <div class="nx-modal__header">
                <span class="nx-modal__title">Contacto existente encontrado</span>
            </div>
            <div class="nx-modal__body" style="gap:14px">
                <p style="font-size:13px;color:var(--nx-fg);line-height:1.5;margin:0">
                    Ya existe un contacto con el email
                    <strong>{{ $duplicateContact['email'] ?? '' }}</strong>:
                </p>
                <div style="background:var(--nx-surface-2);border:1px solid var(--nx-border);border-radius:10px;padding:12px 14px;display:flex;align-items:center;gap:12px">
                    @php
                        $dupInitial = strtoupper(substr($duplicateContact['name'] ?? '?', 0, 1));
                        $dupPalette = ['#0ea5e9','#10b981','#f59e0b','#ef4444','#ec4899','#6366f1'];
                        $dupColor   = $dupPalette[abs(crc32($duplicateContact['name'] ?? '')) % count($dupPalette)];
                    @endphp
                    <div class="nx-avatar" style="background:{{ $dupColor }};flex-shrink:0">{{ $dupInitial }}</div>
                    <div>
                        <div style="font-weight:600;font-size:13px">{{ $duplicateContact['name'] ?? '—' }}</div>
                        <div style="font-size:11.5px;color:var(--nx-muted)">{{ $duplicateContact['email'] ?? '' }}</div>
                    </div>
                </div>
                <p style="font-size:12.5px;color:var(--nx-muted);margin:0;line-height:1.5">
                    ¿Es la misma persona? Puedes vincular este ticket con el contacto existente o crear uno nuevo separado.
                </p>
            </div>
            <div class="nx-modal__footer" style="justify-content:space-between">
                <button wire:click="createNewContact" class="nx-btn nx-btn--ghost" style="font-size:12px">
                    Crear contacto nuevo
                </button>
                <button wire:click="linkWithDuplicate" class="nx-btn nx-btn--primary" style="font-size:12px">
                    Vincular con este contacto
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($ticket)
    <aside class="nx-detail">

        {{-- Header del panel derecho --}}
        <div class="nx-detail__panel-header">
            <span>Detalles</span>
            @if($ticket->platform !== 'telegram')
            <button wire:click="openVisitorModal" class="nx-detail__edit-btn" title="Editar datos del visitante">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </button>
            @endif
        </div>

        {{-- Info del cliente --}}
        <div class="nx-detail__section">
            <div class="nx-detail__heading">Cliente</div>

            <div class="nx-detail__row">
                <span class="nx-detail__key">Nombre</span>
                <span class="nx-detail__val">{{ $ticket->client_name }}</span>
            </div>
            @if($ticket->platform === 'telegram')
            <div class="nx-detail__row">
                <span class="nx-detail__key">Telegram</span>
                <span class="nx-detail__val">
                    {{ $ticket->telegram_username ? '@' . $ticket->telegram_username : 'Username oculto' }}
                </span>
            </div>
            <div class="nx-detail__row">
                <span class="nx-detail__key">Chat ID</span>
                <span class="nx-detail__val nx-detail__val--mono">{{ $ticket->telegram_id }}</span>
            </div>
            @endif
            @if($ticket->client_email)
            <div class="nx-detail__row">
                <span class="nx-detail__key">Email</span>
                <span class="nx-detail__val nx-detail__val--mono">{{ $ticket->client_email }}</span>
            </div>
            @endif
            @if($ticket->client_phone)
            <div class="nx-detail__row">
                <span class="nx-detail__key">Teléfono</span>
                <span class="nx-detail__val nx-detail__val--mono">{{ $ticket->client_phone }}</span>
            </div>
            @endif
        </div>

        {{-- Info del visitante --}}
        <div class="nx-detail__section">
            <div class="nx-detail__heading">Visitante</div>

            @if($ticket->visitor_country)
            <div class="nx-detail__row">
                <span class="nx-detail__key">Ubicación</span>
                <span class="nx-detail__val">{{ $ticket->visitor_city }}, {{ $ticket->visitor_country }}</span>
            </div>
            @endif
            @if($ticket->visitor_ip)
            @php $isBanned = $this->isVisitorBanned(); @endphp
            <div class="nx-detail__row">
                <span class="nx-detail__key">IP</span>
                <span class="nx-detail__val nx-detail__val--mono">{{ $ticket->visitor_ip }}</span>
            </div>
            <div class="nx-detail__row" style="align-items:center">
                <span class="nx-detail__key">Acceso</span>
                @if($isBanned)
                    <button wire:click="unbanVisitor" class="nx-ban-btn nx-ban-btn--unban" title="Quitar baneo">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        IP baneada · Desbanear
                    </button>
                @else
                    <button wire:click="banVisitor" class="nx-ban-btn nx-ban-btn--ban" title="Banear IP">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Banear IP
                    </button>
                @endif
            </div>
            @endif
            @if($ticket->visitor_device)
            <div class="nx-detail__row">
                <span class="nx-detail__key">Dispositivo</span>
                <span class="nx-detail__val">{{ $ticket->visitor_device }}</span>
            </div>
            @endif
            @if($ticket->visitor_os)
            <div class="nx-detail__row">
                <span class="nx-detail__key">Sistema</span>
                <span class="nx-detail__val">{{ $ticket->visitor_os }}</span>
            </div>
            @endif
            @if($ticket->visitor_browser)
            <div class="nx-detail__row">
                <span class="nx-detail__key">Navegador</span>
                <span class="nx-detail__val">{{ $ticket->visitor_browser }}</span>
            </div>
            @endif
            @if($ticket->visitor_page)
            <div class="nx-detail__row">
                <span class="nx-detail__key">Página</span>
                <span class="nx-detail__val nx-detail__val--mono" title="{{ $ticket->visitor_page }}">
                    {{ \Illuminate\Support\Str::limit($ticket->visitor_page, 30) }}
                </span>
            </div>
            @endif
            @if($ticket->visitor_referrer)
            <div class="nx-detail__row">
                <span class="nx-detail__key">Referrer</span>
                <span class="nx-detail__val nx-detail__val--mono" title="{{ $ticket->visitor_referrer }}">
                    {{ \Illuminate\Support\Str::limit($ticket->visitor_referrer, 30) }}
                </span>
            </div>
            @endif
        </div>

        {{-- Calificación --}}
        @if($ticket->status === 'closed')
        <div class="nx-detail__section">
            <div class="nx-detail__heading">Calificación</div>
            @if($ticket->rating)
                <div class="nx-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $ticket->rating ? 'nx-star nx-star--on' : 'nx-star' }}">★</span>
                    @endfor
                    <span class="nx-detail__key" style="margin-left:4px">{{ $ticket->rating }}/5</span>
                </div>
                @if($ticket->rating_comment)
                    <div class="nx-rating-comment">"{{ $ticket->rating_comment }}"</div>
                @endif
            @else
                <div class="nx-detail__key" style="font-size:11px">Sin calificación</div>
            @endif
        </div>
        @endif

        {{-- Conversación / Ticket info --}}
        <div class="nx-detail__section">
            @if($ticket->is_support_ticket && $ticket->ticket_number)
                <div class="nx-detail__heading" style="display:flex;align-items:center;gap:6px">
                    Ticket
                    <span style="font-size:11px;font-weight:700;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;padding:1px 7px;border-radius:5px">{{ $ticket->ticket_number }}</span>
                </div>
                @if($ticket->ticket_subject)
                <div class="nx-detail__row">
                    <span class="nx-detail__key">Asunto</span>
                    <span class="nx-detail__val">{{ $ticket->ticket_subject }}</span>
                </div>
                @endif
            @else
                <div class="nx-detail__heading">Conversación</div>
            @endif
            <div class="nx-detail__row">
                <span class="nx-detail__key">Canal</span>
                <span class="nx-detail__val">{{ ucfirst($ticket->platform) }}</span>
            </div>
            <div class="nx-detail__row">
                <span class="nx-detail__key">Inicio</span>
                <span class="nx-detail__val">{{ $ticket->created_at->setTimezone($orgTimezone)->translatedFormat('d M, H:i') }}</span>
            </div>
        </div>

        {{-- Gestión del ticket (prioridad, categoría, departamento, notas) --}}
        @php $availableDepts = $this->availableDepartments; @endphp
        @if($ticket->platform !== 'telegram')
        @if($ticket->status !== 'closed')
        <div class="nx-detail__section">
            <div class="nx-detail__heading">Gestión</div>

            <div class="nx-detail__field">
                <label class="nx-detail__key">Departamento</label>
                <select wire:model.live="ticketDepartmentId" wire:change="saveTicketMeta" class="nx-detail__select">
                    <option value="">Sin departamento</option>
                    @foreach($availableDepts as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="nx-detail__field">
                <label class="nx-detail__key">Prioridad</label>
                <select wire:model.live="ticketPriority" wire:change="saveTicketMeta" class="nx-detail__select">
                    <option value="low">Baja</option>
                    <option value="normal">Normal</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                </select>
            </div>

            <div class="nx-detail__field">
                <label class="nx-detail__key">Categoría</label>
                <select wire:model.live="ticketCategory" wire:change="saveTicketMeta" class="nx-detail__select">
                    <option value="general">General</option>
                    <option value="sales">Ventas</option>
                    <option value="support">Soporte técnico</option>
                    <option value="billing">Facturación</option>
                    <option value="other">Otro</option>
                </select>
            </div>

            {{-- Tags --}}
            @php $availableTags = $this->availableTags; $ticketTags = $ticket->tags ?? collect(); @endphp
            @if($availableTags->isNotEmpty())
            <div class="nx-detail__field"
                 x-data="{
                     open: false,
                     selected: {{ json_encode($ticketTags->pluck('id')->toArray()) }},
                     toggle(id) {
                         const idx = this.selected.indexOf(id);
                         if (idx === -1) this.selected.push(id);
                         else this.selected.splice(idx, 1);
                         $wire.syncTags(this.selected);
                     }
                 }">
                <label class="nx-detail__key">Tags</label>
                {{-- Active tags as pills --}}
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:{{ $ticketTags->isNotEmpty() ? '6px' : '0' }}">
                    @foreach($ticketTags as $tag)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;background:{{ $tag->color }}22;color:{{ $tag->color }};border:1px solid {{ $tag->color }}44">
                        <span style="width:5px;height:5px;border-radius:50%;background:{{ $tag->color }};flex-shrink:0"></span>
                        {{ $tag->name }}
                        <button wire:click="removeTag({{ $tag->id }})"
                                style="background:none;border:none;cursor:pointer;padding:0;margin-left:1px;opacity:.6;display:flex;color:inherit;line-height:1"
                                title="Quitar tag">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                    @endforeach
                </div>
                {{-- Dropdown to add --}}
                <div style="position:relative" @click.outside="open=false">
                    <button type="button"
                            @click="open=!open"
                            style="width:100%;text-align:left;padding:5px 9px;border:1px dashed var(--nx-border,#e3e6ea);border-radius:7px;background:transparent;font-size:12px;color:var(--nx-muted,#6b7280);cursor:pointer;display:flex;align-items:center;gap:5px;font-family:inherit">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Agregar tag
                    </button>
                    <div x-show="open" x-transition
                         style="position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:50;background:var(--nx-surface,#fff);border:1px solid var(--nx-border,#e3e6ea);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);overflow:hidden;max-height:180px;overflow-y:auto">
                        @foreach($availableTags as $tag)
                        <button type="button"
                                @click="toggle({{ $tag->id }})"
                                style="width:100%;text-align:left;padding:7px 11px;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:7px;font-family:inherit;font-size:12.5px;color:var(--nx-text,#111827);transition:background .1s"
                                :style="selected.includes({{ $tag->id }}) ? 'background:var(--nx-bg,#f5f6f8)' : ''"
                                onmouseenter="this.style.background='var(--nx-bg,#f5f6f8)'"
                                onmouseleave="this.style.background=selected.includes({{ $tag->id }}) ? 'var(--nx-bg,#f5f6f8)' : ''">
                            <span style="width:8px;height:8px;border-radius:50%;background:{{ $tag->color }};flex-shrink:0"></span>
                            {{ $tag->name }}
                            <svg x-show="selected.includes({{ $tag->id }})" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" style="margin-left:auto;color:#22c55e" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="nx-detail__field">
                <label class="nx-detail__key">
                    Notas internas
                    <span style="font-weight:400;opacity:.6;text-transform:none;letter-spacing:0"> · privado</span>
                </label>
                <textarea wire:model="internalNotes"
                          wire:blur="saveTicketMeta"
                          rows="2"
                          placeholder="Contexto privado del cliente…"
                          class="nx-detail__textarea"></textarea>
            </div>
        </div>
        @else
        <div class="nx-detail__section">
            <div class="nx-detail__heading">Gestión</div>
            @if($ticket->department_id)
            <div class="nx-detail__row">
                <span class="nx-detail__key">Departamento</span>
                @php $dept = $availableDepts->firstWhere('id', $ticket->department_id); @endphp
                @if($dept)
                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:{{ $dept->color }}">
                        <span style="width:7px;height:7px;border-radius:50%;background:{{ $dept->color }};flex-shrink:0"></span>
                        {{ $dept->name }}
                    </span>
                @else
                    <span class="nx-detail__val">—</span>
                @endif
            </div>
            @endif
            <div class="nx-detail__row">
                <span class="nx-detail__key">Prioridad</span>
                <span class="nx-pill-sm nx-pill-sm--{{ $ticket->priority ?? 'normal' }}">
                    {{ ucfirst($ticket->priority ?? 'Normal') }}
                </span>
            </div>
            <div class="nx-detail__row">
                <span class="nx-detail__key">Categoría</span>
                <span class="nx-detail__val">{{ ucfirst($ticket->category ?? 'General') }}</span>
            </div>
            @php $closedTags = $ticket->tags ?? collect(); @endphp
            @if($closedTags->isNotEmpty())
            <div class="nx-detail__row" style="align-items:flex-start">
                <span class="nx-detail__key" style="margin-top:3px">Tags</span>
                <div style="display:flex;flex-wrap:wrap;gap:4px">
                    @foreach($closedTags as $tag)
                    <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 7px;border-radius:99px;font-size:10.5px;font-weight:600;background:{{ $tag->color }}22;color:{{ $tag->color }};border:1px solid {{ $tag->color }}44">
                        <span style="width:5px;height:5px;border-radius:50%;background:{{ $tag->color }};flex-shrink:0"></span>
                        {{ $tag->name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
        @endif

    </aside>
    @endif
</div>

<style>
/* ── Reset completo de Filament — inbox ocupa TODO el espacio ── */
.fi-page-header, .fi-breadcrumbs { display: none !important; }

/* Toda la cadena de wrappers Filament sin scroll ni padding */
.fi-main, .fi-page, .fi-page-header-main-ctn,
.fi-page-main, .fi-page-content {
    padding: 0 !important;
    margin: 0 !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    min-height: 0 !important;
}

/* ── Variables: hereda la paleta global --c-* del theme.css ── */
.nx-inbox {
    --nx-bg:       var(--c-bg,       #f5f6f8);
    --nx-surface:  var(--c-surface,  #ffffff);
    --nx-surface-2:var(--c-surf2,    #f0f2f5);
    --nx-border:   var(--c-border,   #e3e6ea);
    --nx-text:     var(--c-text,     #111827);
    --nx-muted:    var(--c-sub,      #6b7280);
    --nx-accent:   #22c55e;
    --nx-accent-h: #16a34a;
    --nx-accent-bg:rgba(34,197,94,.08);
    --nx-accent-bd:rgba(34,197,94,.2);
    --nx-font:     'Inter', ui-sans-serif, system-ui, sans-serif;
}

/* ── Layout raíz — flush, sin border, sin border-radius ── */
.nx-inbox {
    display: flex;
    flex: 1;
    height: calc(100vh - 64px);
    max-height: calc(100vh - 64px);
    min-height: 0;
    background: var(--nx-bg);
    overflow: hidden;
    font-family: var(--nx-font);
}

/* ─── SIDEBAR ─── */
.nx-sidebar {
    width: 280px;
    min-width: 280px;
    display: flex;
    flex-direction: column;
    background: var(--nx-surface);
    border-right: 1px solid var(--nx-border);
}

.nx-sidebar__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    height: 56px;
    flex-shrink: 0;
    border-bottom: 1px solid var(--nx-border);
    background: var(--nx-surface);
}

.nx-sidebar__tabs {
    display: flex;
    border-bottom: 1px solid var(--nx-border);
    background: var(--nx-surface);
    flex-shrink: 0;
    padding: 0 8px;
    gap: 2px;
}
.nx-tab {
    flex: 1;
    padding: 10px 8px;
    font-size: 12px;
    font-weight: 500;
    color: var(--nx-muted);
    background: none;
    border: none;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: color .12s, border-color .12s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-family: var(--nx-font);
    letter-spacing: -.01em;
}
.nx-tab:hover { color: var(--nx-text); }
.nx-tab--active {
    color: var(--nx-accent);
    border-bottom-color: var(--nx-accent);
    font-weight: 600;
}
/* Tab count — usa el accent color del sistema */
.nx-tab-count {
    font-size: 10px;
    font-weight: 700;
    background: var(--nx-accent-bg);
    color: var(--nx-accent);
    border: 1px solid var(--nx-accent-bd);
    padding: 1px 5px;
    border-radius: 99px;
}

.nx-sidebar__title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 700;
    color: var(--nx-text);
    letter-spacing: -.02em;
}

.nx-status-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 0 2px rgba(34,197,94,.2);
    animation: nx-pulse 2.5s ease-in-out infinite;
}
@keyframes nx-pulse {
    0%,100% { box-shadow: 0 0 0 2px rgba(34,197,94,.2); }
    50%      { box-shadow: 0 0 0 5px rgba(34,197,94,.04); }
}

.nx-badge {
    font-size: 10px;
    font-weight: 700;
    color: #1e293b;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 1px 7px;
    border-radius: 99px;
}

/* Lista de tickets */
.nx-ticket-list {
    flex: 1;
    overflow-y: auto;
    padding: 6px 8px;
    scrollbar-width: thin;
    scrollbar-color: var(--nx-border) transparent;
}

.nx-ticket {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    width: 100%;
    padding: 10px 10px;
    border-radius: 9px;
    border: 1px solid transparent;
    background: none;
    cursor: pointer;
    text-align: left;
    transition: background .12s, border-color .12s;
    margin-bottom: 2px;
    color: inherit;
}
.nx-ticket:hover {
    background: var(--nx-surface-2);
    border-color: var(--nx-border);
}
/* Active — usa el accent del sistema (verde) */
.nx-ticket--active {
    background: var(--nx-accent-bg);
    border-color: var(--nx-accent-bd);
}
:is(html.dark, [data-theme="dark"]) .nx-ticket--active {
    background: rgba(34,197,94,.1);
    border-color: rgba(34,197,94,.25);
}
.nx-ticket--selected {
    background: rgba(239,68,68,.06);
    border-color: rgba(239,68,68,.25);
}
.nx-ticket--selected:hover {
    background: rgba(239,68,68,.1);
}

/* ── Avatar / Checkbox overlay (WhatsApp-style) ── */
.nx-avatar-wrap {
    position: relative; width: 36px; height: 36px;
    flex-shrink: 0; cursor: pointer;
}
.nx-avatar-wrap .nx-avatar {
    position: absolute; inset: 0; width: 100%; height: 100%;
    transition: opacity .18s ease, transform .18s ease;
    margin: 0 !important;  /* cancel any margin from .nx-avatar base class */
}
.nx-avatar-cb {
    position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px;
    border-radius: 4px; border: 2px solid var(--nx-surface, #fff);
    background: #d1d5db;
    display: flex; align-items: center; justify-content: center;
    transition: opacity .18s ease, transform .18s ease, border-color .15s, background .15s;
    /* Hidden by default — Alpine shows it on hover / selected */
    opacity: 0;
    transform: scale(.85);
}
.nx-avatar-cb--on { border-color: var(--nx-surface, #fff); background: #ef4444; }

/* ── Bulk action bar ── */
.nx-bulk-bar {
    display: flex; align-items: center; justify-content: space-between;
    gap: 8px; padding: 9px 12px;
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-bottom: 1px solid rgba(239,68,68,.25);
    animation: nx-bulk-slide .2s ease;
}
@keyframes nx-bulk-slide {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.nx-bulk-bar__info { display: flex; align-items: center; gap: 8px; }
.nx-bulk-bar__check {
    width: 22px; height: 22px; border-radius: 50%;
    background: #ef4444;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.nx-bulk-bar__count { font-size: 12px; font-weight: 600; color: #f1f5f9; white-space: nowrap; }
.nx-bulk-bar__actions { display: flex; align-items: center; gap: 6px; }
.nx-bulk-bar__btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 11px; border-radius: 8px; border: none;
    font-size: 11.5px; font-weight: 600; cursor: pointer;
    font-family: inherit; transition: all .15s; white-space: nowrap;
}
.nx-bulk-bar__btn--ghost {
    background: rgba(255,255,255,.08); color: #94a3b8;
    border: 1px solid rgba(255,255,255,.12);
}
.nx-bulk-bar__btn--ghost:hover { background: rgba(255,255,255,.14); color: #e2e8f0; }
.nx-bulk-bar__btn--danger {
    background: #ef4444; color: #fff;
    box-shadow: 0 2px 10px rgba(239,68,68,.35);
}
.nx-bulk-bar__btn--danger:hover {
    background: #dc2626;
    box-shadow: 0 3px 14px rgba(239,68,68,.5);
    transform: translateY(-1px);
}
.nx-bulk-bar__cancel {
    width: 28px; height: 28px; border-radius: 50%; border: none;
    background: rgba(255,255,255,.08); color: #94a3b8;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .15s; flex-shrink: 0;
}
.nx-bulk-bar__cancel:hover { background: rgba(255,255,255,.18); color: #f1f5f9; }

.nx-ticket__body { flex: 1; min-width: 0; }
.nx-ticket__top  {
    display: flex; justify-content: space-between;
    align-items: baseline; gap: 4px; margin-bottom: 2px;
}
.nx-ticket__name {
    font-size: 13px; font-weight: 600;
    color: var(--nx-text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    letter-spacing: -.01em;
}
.nx-ticket__time {
    font-size: 10px; color: var(--nx-muted); white-space: nowrap; flex-shrink: 0;
    font-variant-numeric: tabular-nums;
}
.nx-ticket__bottom {
    display: flex; align-items: center;
    justify-content: space-between; gap: 6px;
}
.nx-ticket__preview {
    font-size: 11.5px; color: var(--nx-muted);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;
    opacity: .85;
}

/* Pills — status badges con identidad visual clara */
.nx-pill {
    font-size: 9.5px; font-weight: 700; letter-spacing: .04em;
    padding: 2px 7px; border-radius: 99px;
    flex-shrink: 0; text-transform: uppercase;
}
.nx-pill--bot    { background: var(--nx-surface-2); color: var(--nx-muted); border: 1px solid var(--nx-border); }
.nx-pill--human  { background: var(--nx-accent-bg); color: var(--nx-accent-h); border: 1px solid var(--nx-accent-bd); }
.nx-pill--closed { background: var(--nx-surface-2); color: var(--nx-muted); border: 1px solid var(--nx-border); opacity: .7; }

:is(html.dark, [data-theme="dark"]) .nx-pill--bot   { color: var(--nx-muted); }
:is(html.dark, [data-theme="dark"]) .nx-pill--human { color: var(--nx-accent); }

/* Empty state (sidebar) */
.nx-empty-state {
    padding: 48px 20px; text-align: center;
    display: flex; flex-direction: column; align-items: center; gap: 10px;
    color: var(--nx-muted);
}
.nx-empty-state p { font-size: 12px; margin: 0; }

/* ─── AVATARS ─── */
.nx-avatar {
    width: 32px; height: 32px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
    letter-spacing: -.02em;
}
.nx-avatar--lg { width: 36px; height: 36px; font-size: 14px; border-radius: 11px; }

/* ─── CHAT PANEL ─── */
.nx-chat { flex: 1; display: flex; flex-direction: column; min-width: 0; background: var(--nx-bg); }

.nx-chat__header {
    display: flex;
    flex-direction: column;
    gap: 0;
    padding: 0 16px;
    min-height: 56px;
    justify-content: center;
    border-bottom: 1px solid var(--nx-border);
    background: var(--nx-surface);
    flex-shrink: 0;
}
.nx-chat__header-top {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    padding: 10px 0 6px;
}
.nx-chat__info { flex: 1; min-width: 0; }
.nx-chat__info strong {
    display: block; font-size: 14px; font-weight: 700; color: var(--nx-text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 1px; letter-spacing: -.02em;
}
.nx-chat__info span {
    font-size: 11px; color: var(--nx-muted); display: block;
    overflow: hidden; text-overflow: ellipsis; margin-top: 1px;
    line-height: 1.4;
}
.nx-chat__actions {
    display: flex; gap: 6px; align-items: center;
    padding-bottom: 8px; flex-wrap: wrap;
}

.nx-status-label { font-size: 11px; font-weight: 500; }
.nx-status-label--human  { color: #2563eb; }
.nx-status-label--bot    { color: #64748b; }
.nx-status-label--closed { color: var(--nx-muted); }
:is(html.dark, [data-theme="dark"]) .nx-status-label--human { color: #93c5fd; }
:is(html.dark, [data-theme="dark"]) .nx-status-label--bot   { color: #94a3b8; }

/* ─── MENSAJES ─── */
.nx-messages {
    flex: 1; overflow-y: auto;
    padding: 20px 20px 16px;
    display: flex; flex-direction: column; gap: 4px;
    scrollbar-width: thin;
    scrollbar-color: var(--nx-border) transparent;
}
/* Agrupación de mensajes por sender */
.nx-msg + .nx-msg { margin-top: 2px; }
.nx-msg--user + .nx-msg--bot,
.nx-msg--bot + .nx-msg--user,
.nx-msg--agent + .nx-msg--user,
.nx-msg--user + .nx-msg--agent { margin-top: 12px; }

/* ─── WRAPPER CON HOVER ─── */
.nx-msg-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    border-radius: 10px;
    transition: background .12s;
    padding: 2px 4px;
    margin: 0 -4px;
}
.nx-msg-wrap:hover { background: rgba(0,0,0,.03); }
:is(html.dark, [data-theme="dark"]) .nx-msg-wrap:hover { background: rgba(255,255,255,.04); }
.nx-msg-wrap--selected {
    background: var(--nx-accent-bg) !important;
    outline: 1px solid var(--nx-accent-bd);
    border-radius: 10px;
}
.nx-msg-wrap .nx-msg { flex: 1; min-width: 0; }

/* ─── CHECKBOX DE SELECCIÓN ─── */
.nx-msg__check {
    flex-shrink: 0;
    width: 24px; height: 24px;
    border: none; background: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    border-radius: 50%;
    color: var(--nx-muted);
    transition: color .12s, background .12s;
    padding: 0;
}
.nx-msg__check:hover { background: var(--nx-surface-2); color: var(--nx-accent); }
.nx-msg__check--on { color: var(--nx-accent); }

/* ─── KEBAB MENU (⋮) ─── */
.nx-msg__kebab {
    flex-shrink: 0;
}
.nx-msg__kebab-btn {
    width: 26px; height: 26px;
    border: none; background: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    border-radius: 6px;
    color: var(--nx-muted);
    transition: background .1s, color .1s;
    padding: 0;
}
.nx-msg__kebab-btn:hover { background: var(--nx-surface-2); color: var(--nx-text); }
.nx-msg__kebab-menu {
    display: none;
    position: absolute;
    right: 0; top: calc(100% + 4px); z-index: 80;
    background: var(--nx-surface);
    border: 1px solid var(--nx-border);
    border-radius: 9px;
    box-shadow: 0 4px 20px rgba(0,0,0,.12);
    min-width: 160px;
    overflow: hidden;
    animation: nx-fadein .1s ease;
}
.nx-msg__kebab-menu.open { display: block; }
.nx-msg__kebab-item {
    width: 100%; padding: 8px 12px;
    background: none; border: none; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    font-size: 12.5px; font-family: var(--nx-font);
    color: var(--nx-text); text-align: left;
    transition: background .1s;
}
.nx-msg__kebab-item:hover { background: var(--nx-surface-2); }
.nx-msg__kebab-item--danger { color: #dc2626; }
.nx-msg__kebab-item--danger:hover { background: #fef2f2; }
:is(html.dark, [data-theme="dark"]) .nx-msg__kebab-item--danger:hover { background: rgba(220,38,38,.12); }

/* ─── BOTÓN ELIMINAR NOTA ─── */
.nx-msg__delete-btn {
    background: none; border: none; cursor: pointer;
    padding: 3px; border-radius: 5px;
    color: #dc2626; opacity: 0; transition: opacity .15s;
    display: flex; align-items: center;
}
.nx-msg--note:hover .nx-msg__delete-btn { opacity: .7; }
.nx-msg__delete-btn:hover { opacity: 1 !important; }

/* ─── BOTÓN SELECT EN HEADER ─── */
.nx-btn--select {
    font-size: 11.5px;
    color: var(--nx-muted);
    border-color: var(--nx-border);
}
.nx-btn--select--active {
    color: var(--nx-accent);
    border-color: var(--nx-accent-bd);
    background: var(--nx-accent-bg);
}

/* ─── ANIMACIÓN FADE-IN ─── */
@keyframes nx-fadein {
    from { opacity: 0; transform: translateY(-4px); }
    to   { opacity: 1; transform: translateY(0); }
}

.nx-messages__empty {
    flex: 1; display: flex; align-items: center; justify-content: center;
}
.nx-messages__empty p { font-size: 13px; color: var(--nx-muted); }

/* ─── SKELETON LOADING ─── */
@keyframes nx-shimmer {
    0%   { background-position: -400px 0 }
    100% { background-position: 400px 0 }
}
.nx-skeleton-wrap { display: flex; flex-direction: column; gap: 14px; padding: 4px 0; }
.nx-skeleton-msg { display: flex; align-items: flex-end; gap: 8px; }
.nx-skeleton-msg--user { justify-content: flex-end; }
.nx-skeleton-avatar {
    width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
    background: var(--nx-surface-2);
    background: linear-gradient(90deg, var(--nx-surface-2) 25%, var(--nx-border) 50%, var(--nx-surface-2) 75%);
    background-size: 800px 100%;
    animation: nx-shimmer 1.4s infinite linear;
}
.nx-skeleton-lines { display: flex; flex-direction: column; gap: 6px; flex: 1; }
.nx-skeleton-line {
    height: 14px; border-radius: 99px;
    background: var(--nx-surface-2);
    background: linear-gradient(90deg, var(--nx-surface-2) 25%, var(--nx-border) 50%, var(--nx-surface-2) 75%);
    background-size: 800px 100%;
    animation: nx-shimmer 1.4s infinite linear;
}
.nx-skeleton-line:nth-child(2) { animation-delay: .1s; }
.nx-skeleton-line:nth-child(3) { animation-delay: .2s; }

.nx-msg { display: flex; align-items: flex-end; gap: 8px; }

.nx-msg__avatar {
    width: 26px; height: 26px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 700; color: #fff; flex-shrink: 0;
    margin-bottom: 2px; letter-spacing: -.01em;
}

.nx-msg__content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
    max-width: 68%;
    min-width: 0;
}
/* Visitor (user) messages — LEFT */
.nx-msg--user .nx-msg__content {
    align-items: flex-start;
}
/* Agent messages — RIGHT (row-reverse flips avatar & content) */
.nx-msg--agent {
    flex-direction: row-reverse;
}
.nx-msg--agent .nx-msg__content {
    align-items: flex-end;
}

.nx-bubble {
    padding: 9px 13px;
    border-radius: 16px;
    font-size: 13.5px; line-height: 1.58;
    white-space: normal;
    word-break: break-word;
    overflow-wrap: break-word;
    box-sizing: border-box;
    max-width: 100%;
}
/* Visitor: neutral, left tail */
.nx-msg--user  .nx-bubble {
    background: var(--nx-surface-2);
    color: var(--nx-text);
    border: 1px solid var(--nx-border);
    border-bottom-left-radius: 3px;
}
/* Bot: white surface, left tail */
.nx-msg--bot   .nx-bubble {
    background: var(--nx-surface);
    color: var(--nx-text);
    border: 1px solid var(--nx-border);
    border-bottom-left-radius: 3px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
/* Agent: usa accent del sistema, right tail */
.nx-msg--agent .nx-bubble {
    background: var(--nx-accent);
    color: #fff;
    border: none;
    border-radius: 16px;
    border-bottom-right-radius: 3px;
    box-shadow: 0 1px 4px rgba(34,197,94,.3);
}

/* Burbujas — modo oscuro */
:is(html.dark, [data-theme="dark"]) .nx-msg--user  .nx-bubble { background: var(--nx-surface-2); color: var(--nx-text); border-color: var(--nx-border); }
:is(html.dark, [data-theme="dark"]) .nx-msg--bot   .nx-bubble { background: var(--nx-surface); border-color: var(--nx-border); }
:is(html.dark, [data-theme="dark"]) .nx-msg--agent .nx-bubble { background: var(--nx-accent-h); box-shadow: 0 1px 4px rgba(34,197,94,.2); }

.nx-msg__time { display: block; font-size: 10px; color: var(--nx-muted); padding: 2px 4px 0; }
.nx-msg--agent .nx-msg__time { text-align: right; }

/* Mensaje de sistema */
.nx-msg--system {
    display: flex; align-items: center; gap: 10px;
    text-align: center; font-size: 11px; color: var(--nx-muted);
    padding: 2px 0;
}
.nx-msg--system::before,
.nx-msg--system::after {
    content: ''; flex: 1; height: 1px; background: var(--nx-border);
}
.nx-msg--system span { white-space: nowrap; flex-shrink: 0; }

/* ─── Date separator (same style as system msg) ─── */
.nx-date-sep {
    display: flex; align-items: center; gap: 10px;
    font-size: 10.5px; font-weight: 600; letter-spacing: .04em;
    text-transform: uppercase; color: var(--nx-muted);
    margin: 10px 0 6px;
}
.nx-date-sep::before, .nx-date-sep::after {
    content: ''; flex: 1; height: 1px; background: var(--nx-border);
}

/* ─── NOTA INTERNA ─── */
.nx-msg--note {
    display: flex; align-items: flex-start; gap: 7px;
    background: rgba(251,191,36,.12);
    border: 1px solid rgba(217,119,6,.2);
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 12.5px;
    color: #92400e;
    line-height: 1.5;
    margin: 2px 0;
}
.nx-msg--note span { flex: 1; white-space: pre-wrap; word-break: break-word; }

/* Toggle modo nota */
.nx-note-toggle {
    display: inline-flex; align-items: center; gap: 5px;
    background: none; border: 1px solid var(--nx-border);
    border-radius: 6px; padding: 3px 9px;
    font-size: 10.5px; color: var(--nx-muted);
    cursor: pointer; transition: all .12s;
}
.nx-note-toggle:hover { border-color: #d97706; color: #d97706; }
.nx-note-toggle--on {
    background: rgba(251,191,36,.12);
    border-color: rgba(217,119,6,.3);
    color: #d97706;
}

/* Input en modo nota */
.nx-composer__input--note {
    border-color: rgba(217,119,6,.3) !important;
    background: rgba(251,191,36,.05) !important;
}
.nx-composer__input--note:focus {
    border-color: #d97706 !important;
    box-shadow: 0 0 0 3px rgba(217,119,6,.08) !important;
}

/* Botón enviar en modo nota */
.nx-btn--note {
    flex-shrink: 0;
    background: #d97706;
    color: #fff;
    border: none; border-radius: 8px;
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background .12s;
}
.nx-btn--note:hover { background: #b45309; }

/* Ban/Unban buttons */
.nx-ban-btn {
    display: inline-flex; align-items: center; gap: 5px;
    border-radius: 6px; padding: 3px 9px;
    font-size: 10.5px; font-weight: 500;
    cursor: pointer; border: 1px solid;
    transition: all .12s;
}
.nx-ban-btn--ban {
    background: rgba(239,68,68,.06);
    border-color: rgba(239,68,68,.2);
    color: #dc2626;
}
.nx-ban-btn--ban:hover { background: rgba(239,68,68,.12); }
.nx-ban-btn--unban {
    background: rgba(34,197,94,.06);
    border-color: rgba(34,197,94,.2);
    color: #16a34a;
}
.nx-ban-btn--unban:hover { background: rgba(34,197,94,.12); }

/* ─── PANEL DE METADATOS ─── */
.nx-meta-panel {
    padding: 8px 18px;
    border-top: 1px solid var(--nx-border);
    background: var(--nx-surface);
    flex-shrink: 0;
}
.nx-meta-row {
    display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;
}
.nx-meta-field { display: flex; flex-direction: column; gap: 3px; }
.nx-meta-field--wide { flex: 1; min-width: 200px; }
.nx-meta-label {
    font-size: 10px; font-weight: 600; color: var(--nx-muted);
    text-transform: uppercase; letter-spacing: .05em;
    display: flex; align-items: center; gap: 6px;
}
.nx-meta-private { font-size: 10px; font-weight: 400; color: var(--nx-muted); text-transform: none; letter-spacing: 0; opacity: .7; }
.nx-meta-select,
.nx-meta-textarea {
    background: var(--nx-bg);
    color: var(--nx-text);
    border: 1px solid var(--nx-border);
    border-radius: 7px;
    padding: 5px 9px;
    font-size: 12px;
    outline: none;
    font-family: var(--nx-font);
    transition: border-color .15s;
}
.nx-meta-select { cursor: pointer; }
.nx-meta-select:focus,
.nx-meta-textarea:focus { border-color: var(--nx-accent); }
.nx-meta-textarea { resize: none; width: 100%; line-height: 1.5; }

/* ─── COMPOSITOR ─── */
.nx-composer {
    padding: 12px 16px;
    border-top: 1px solid var(--nx-border);
    background: var(--nx-surface);
    flex-shrink: 0;
}
.nx-composer--closed {
    text-align: center; font-size: 12px; color: var(--nx-muted);
}

.nx-composer__notice {
    font-size: 11px; color: #2563eb;
    background: rgba(59,130,246,.06);
    border: 1px solid rgba(59,130,246,.15);
    border-radius: 7px;
    padding: 6px 12px;
    margin-bottom: 9px;
}
:is(html.dark, [data-theme="dark"]) .nx-composer__notice { color: #93c5fd; }

.nx-composer__row { display: flex; gap: 8px; align-items: flex-end; }
.nx-composer__input {
    flex: 1;
    background: var(--nx-bg);
    color: var(--nx-text);
    border: 1px solid var(--nx-border);
    border-radius: 12px;
    padding: 9px 13px;
    font-size: 13px;
    font-family: var(--nx-font);
    line-height: 1.5; resize: none; outline: none;
    min-height: 40px; max-height: 120px;
    transition: border-color .15s, box-shadow .15s;
}
.nx-composer__input:focus {
    border-color: var(--nx-accent);
    box-shadow: 0 0 0 3px var(--nx-accent-bg);
}
.nx-composer__input::placeholder { color: var(--nx-muted); }
.nx-composer__footer { display: flex; align-items: center; justify-content: space-between; margin-top: 5px; }
.nx-composer__hint { font-size: 10px; color: var(--nx-muted); }
.nx-enter-toggle { display: flex; align-items: center; gap: 6px; cursor: pointer; user-select: none; }
.nx-enter-toggle__label { font-size: 10px; color: var(--nx-muted); }
.nx-enter-toggle__track { width: 28px; height: 16px; border-radius: 99px; background: var(--nx-border, #e3e6ea); position: relative; cursor: pointer; transition: background .15s; flex-shrink: 0; }
.nx-enter-toggle__track.on { background: #22c55e; }
.nx-enter-toggle__thumb { position: absolute; top: 2px; left: 2px; width: 12px; height: 12px; border-radius: 50%; background: #fff; transition: transform .15s; }
.nx-enter-toggle__track.on .nx-enter-toggle__thumb { transform: translateX(12px); }

/* ── Send button loading ── */
@keyframes nx-spin { to { transform: rotate(360deg); } }
.nx-send-spinner {
    width: 15px; height: 15px; border-radius: 50%;
    border: 2px solid rgba(255,255,255,.3);
    border-top-color: #fff;
    animation: nx-spin .65s linear infinite;
    display: flex;
}
.nx-composer__input:disabled { opacity: .5; cursor: not-allowed; }

/* ── Adjuntar archivo en compositor ── */
.nx-composer__attach-btn {
    display: flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 7px; flex-shrink: 0;
    color: var(--nx-muted); cursor: pointer; transition: color .15s, background .15s;
    margin-bottom: 1px;
}
.nx-composer__attach-btn:hover { color: var(--nx-accent); background: color-mix(in srgb, var(--nx-accent) 10%, transparent); }

.nx-composer__attachment-preview {
    display: flex; align-items: center; gap: 8px;
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
    padding: 6px 10px; margin-bottom: 8px; font-size: 12px; color: #15803d;
}
:is(html.dark, [data-theme="dark"]) .nx-composer__attachment-preview {
    background: #052e16; border-color: #166534; color: #86efac;
}
.nx-attachment-thumb { width: 36px; height: 36px; object-fit: cover; border-radius: 5px; flex-shrink: 0; }
.nx-attachment-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.nx-attachment-remove {
    background: none; border: none; cursor: pointer; color: #6b7280; padding: 2px;
    display: flex; align-items: center; flex-shrink: 0; border-radius: 4px;
}
.nx-attachment-remove:hover { background: #fef2f2; color: #dc2626; }

/* ── Canned responses picker ── */
.nx-canned-picker {
    background: var(--c-surface, #fff);
    border: 1px solid var(--c-border, #e3e6ea);
    border-radius: 10px 10px 0 0;
    box-shadow: 0 -4px 16px rgba(0,0,0,.10);
    overflow: hidden;
    max-height: 220px;
    overflow-y: auto;
    border-bottom: none;
}
.nx-canned-header {
    font-size: 10px;
    color: var(--nx-muted, #9ca3af);
    padding: 6px 12px;
    background: var(--c-bg, #f5f6f8);
    border-bottom: 1px solid var(--c-border, #e3e6ea);
    letter-spacing: .03em;
}
.nx-canned-item {
    display: flex;
    align-items: baseline;
    gap: 10px;
    width: 100%;
    padding: 9px 12px;
    border: none;
    background: transparent;
    cursor: pointer;
    text-align: left;
    transition: background .1s;
    font-family: inherit;
}
.nx-canned-item:hover,
.nx-canned-item--active {
    background: var(--nx-accent-bg);
}
.nx-canned-shortcut {
    font-size: 12px;
    font-weight: 700;
    color: var(--nx-accent-h);
    font-family: monospace;
    flex-shrink: 0;
    min-width: 80px;
}
.nx-canned-preview {
    font-size: 12px;
    color: var(--nx-muted, #6b7280);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ─── EMPTY CHAT STATE ─── */
.nx-chat__empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 10px; text-align: center; color: var(--nx-muted);
}
.nx-chat__empty-icon {
    width: 56px; height: 56px; border-radius: 14px;
    background: var(--nx-surface-2);
    border: 1px solid var(--nx-border);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 4px;
}
.nx-chat__empty h3 { font-size: 14px; font-weight: 600; color: var(--nx-text); margin: 0; }
.nx-chat__empty p  { font-size: 12px; color: var(--nx-muted); margin: 0; }

/* ─── BOTONES ─── */
.nx-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 8px;
    font-size: 12px; font-weight: 500;
    border: 1px solid transparent;
    cursor: pointer;
    transition: background .12s, color .12s, border-color .12s;
    font-family: var(--nx-font);
    line-height: 1;
    letter-spacing: -.01em;
}

/* Primary usa el verde accent */
.nx-btn--primary {
    background: var(--nx-accent); color: #fff;
    border-color: var(--nx-accent);
    padding: 8px 14px; border-radius: 9px;
    font-weight: 600;
}
.nx-btn--primary:hover { background: var(--nx-accent-h); border-color: var(--nx-accent-h); }

.nx-btn--ghost {
    background: transparent;
    color: var(--nx-muted);
    border-color: var(--nx-border);
}
.nx-btn--ghost:hover { background: var(--nx-surface-2); color: var(--nx-text); }

.nx-btn--danger {
    background: transparent; color: #dc2626;
    border-color: rgba(220,38,38,.2);
}
.nx-btn--danger:hover { background: rgba(220,38,38,.07); border-color: rgba(220,38,38,.35); }
:is(html.dark, [data-theme="dark"]) .nx-btn--danger { color: #f87171; }

/* Assign — usa accent del sistema */
.nx-btn--assign {
    background: var(--nx-accent-bg);
    color: var(--nx-accent-h);
    border-color: var(--nx-accent-bd);
    font-weight: 600;
}
.nx-btn--assign:hover { background: rgba(34,197,94,.14); }
:is(html.dark, [data-theme="dark"]) .nx-btn--assign { color: var(--nx-accent); }

.nx-agent-tag {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 500; color: var(--nx-muted);
    background: var(--nx-surface-2);
    border: 1px solid var(--nx-border);
    padding: 4px 10px; border-radius: 99px;
}

/* ─── PANEL DERECHO — Detalle del visitante ─── */
.nx-detail {
    width: 240px;
    min-width: 240px;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    background: var(--nx-surface);
    border-left: 1px solid var(--nx-border);
    scrollbar-width: thin;
    scrollbar-color: var(--nx-border) transparent;
}

.nx-detail__panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 56px;
    padding: 0 16px;
    font-size: 13px;
    font-weight: 600;
    color: var(--nx-text);
    border-bottom: 1px solid var(--nx-border);
    flex-shrink: 0;
    background: var(--nx-surface);
}
.nx-detail__edit-btn {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 500; color: var(--nx-muted);
    background: none; border: 1px solid var(--nx-border); border-radius: 6px;
    padding: 4px 9px; cursor: pointer; font-family: var(--nx-font);
    transition: background .1s, color .1s;
}
.nx-detail__edit-btn:hover { background: var(--nx-surface-2); color: var(--nx-text); }

.nx-detail__section {
    padding: 14px 16px 12px;
    border-bottom: 1px solid var(--nx-border);
}
.nx-detail__section:last-child { border-bottom: none; }

.nx-detail__heading {
    font-size: 10px;
    font-weight: 700;
    color: var(--nx-muted);
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 10px;
    opacity: .7;
}

.nx-detail__row {
    display: flex;
    flex-direction: column;
    gap: 2px;
    margin-bottom: 10px;
}
.nx-detail__key {
    font-size: 10.5px;
    color: var(--nx-muted);
    font-weight: 500;
}
.nx-detail__val {
    font-size: 12.5px;
    color: var(--nx-text);
    font-weight: 400;
    word-break: break-all;
    line-height: 1.4;
}
.nx-detail__val--mono { font-family: ui-monospace, monospace; font-size: 11px; }

.nx-detail__field { display: flex; flex-direction: column; gap: 4px; margin-bottom: 10px; }
.nx-detail__field:last-child { margin-bottom: 0; }
.nx-detail__select,
.nx-detail__textarea {
    background: var(--nx-bg);
    color: var(--nx-text);
    border: 1px solid var(--nx-border);
    border-radius: 6px;
    padding: 5px 8px;
    font-size: 12px;
    outline: none;
    font-family: var(--nx-font);
    transition: border-color .15s;
    width: 100%;
    box-sizing: border-box;
}
.nx-detail__select { cursor: pointer; }
.nx-detail__select:focus,
.nx-detail__textarea:focus { border-color: var(--nx-accent); }
.nx-detail__textarea { resize: none; line-height: 1.5; }

/* Estrellas de calificación */
.nx-stars { display: flex; align-items: center; gap: 2px; margin-bottom: 6px; }
.nx-star { font-size: 16px; color: var(--nx-border); }
.nx-star--on { color: #f59e0b; }
.nx-rating-comment {
    font-size: 11.5px;
    color: var(--nx-muted);
    font-style: italic;
    line-height: 1.5;
    margin-top: 4px;
}

/* ─── BOTÓN TICKET ─── */
.nx-btn--ticket {
    background: #f8fafc;
    color: #475569;
    border-color: #e2e8f0;
}
.nx-btn--ticket:hover { background: #f1f5f9; color: #1e293b; }

.nx-ticket-badge-tag {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 4px 10px; border-radius: 99px;
    font-family: ui-monospace, monospace;
}

/* ─── MODAL CREAR TICKET ─── */
.nx-modal-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.35);
    display: flex; align-items: center; justify-content: center;
}
.nx-modal {
    background: var(--nx-surface);
    border: 1px solid var(--nx-border);
    border-radius: 14px;
    width: 100%; max-width: 420px;
    margin: 16px;
    display: flex; flex-direction: column;
    overflow: hidden;
}
.nx-modal__header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--nx-border);
}
.nx-modal__title {
    font-size: 14px; font-weight: 600; color: var(--nx-text);
}
.nx-modal__close {
    background: none; border: none; cursor: pointer;
    color: var(--nx-muted); padding: 2px;
    display: flex; align-items: center;
    border-radius: 5px;
    transition: background .1s, color .1s;
}
.nx-modal__close:hover { background: var(--nx-surface-2); color: var(--nx-text); }
.nx-modal__body { padding: 18px 20px; display: flex; flex-direction: column; gap: 14px; }
.nx-modal__desc { font-size: 12px; color: var(--nx-muted); margin: 0; line-height: 1.55; }
.nx-modal__field { display: flex; flex-direction: column; gap: 5px; }
.nx-modal__label {
    font-size: 10px; font-weight: 700; color: var(--nx-muted);
    text-transform: uppercase; letter-spacing: .05em;
}
.nx-modal__input {
    background: var(--nx-bg);
    color: var(--nx-text);
    border: 1px solid var(--nx-border);
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    font-family: var(--nx-font);
    outline: none;
    transition: border-color .15s;
    width: 100%; box-sizing: border-box;
}
.nx-modal__input:focus { border-color: var(--nx-accent); }
.nx-modal__input::placeholder { color: var(--nx-muted); }
.nx-modal__hint { font-size: 10.5px; color: var(--nx-muted); }
.nx-modal__footer {
    display: flex; justify-content: flex-end; gap: 8px;
    padding: 14px 20px;
    border-top: 1px solid var(--nx-border);
    background: var(--nx-surface-2);
}

/* Pills pequeñas para prioridad */
.nx-pill-sm {
    font-size: 9px; font-weight: 700; letter-spacing:.04em;
    padding: 2px 7px; border-radius: 99px; text-transform: uppercase;
    display: inline-block;
}
.nx-pill-sm--low    { background: rgba(5,150,105,.08); color:#059669; border:1px solid rgba(5,150,105,.15); }
.nx-pill-sm--normal { background: rgba(14,165,233,.08); color:#0284c7; border:1px solid rgba(14,165,233,.15); }
.nx-pill-sm--high   { background: rgba(245,158,11,.08); color:#d97706; border:1px solid rgba(245,158,11,.15); }
.nx-pill-sm--urgent { background: rgba(220,38,38,.08);  color:#dc2626; border:1px solid rgba(220,38,38,.15); }

/* ─── RESPONSIVE ─── */
/* <1100px: ocultar panel derecho de detalles */
@media (max-width: 1100px) {
    .nx-detail { display: none; }
}
/* <700px: sidebar reducida */
@media (max-width: 700px) {
    .nx-sidebar { width: 220px; min-width: 220px; }
}
/* <560px: sidebar ultra-compacta (solo avatares) */
@media (max-width: 560px) {
    .nx-sidebar { width: 60px; min-width: 60px; }
    .nx-sidebar__title,
    .nx-sidebar__tabs,
    .nx-ticket__body,
    .nx-ticket-list > div { display: none; }
    .nx-ticket { justify-content: center; padding: 8px 0; }
    .nx-ticket-list { padding: 4px 8px; }
    .nx-sidebar__header { justify-content: center; padding: 0 8px; }
    /* Buscador oculto en móvil */
    .nx-sidebar > div:nth-child(3) { display: none; }
}
/* <420px: ocultar sidebar completamente si hay ticket activo */
@media (max-width: 420px) {
    .nx-sidebar { display: none; }
    .nx-messages { padding: 12px 12px 8px; }
    .nx-chat__header { padding: 8px 12px; }
    .nx-composer { padding: 8px 12px; }
}

</style>

</x-filament-panels::page>
