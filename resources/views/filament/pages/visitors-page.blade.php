<x-filament-panels::page>

@php
$visitors = $this->activeVisitors;
$banned   = $this->bannedIps;
$visitorIds = $visitors->pluck('id')->values()->all();
@endphp

<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

.vp-page { display: flex; flex-direction: column; gap: 20px; padding: 20px 24px 48px; }

/* ── Header ── */
.vp-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
.vp-title  { font-size: 17px; font-weight: 800; color: var(--nx-text); display: flex; align-items: center; gap: 8px; }
.vp-count-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: #dcfce7; color: #15803d;
    border: 1px solid #bbf7d0; border-radius: 99px;
    font-size: 11.5px; font-weight: 700; padding: 3px 10px;
}
.vp-dot { width: 6px; height: 6px; border-radius: 50%; background: #22c55e; animation: vp-pulse 1.5s ease-in-out infinite; }
@keyframes vp-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }

/* ── Sound toggle ── */
.vp-sound-btn {
    background: none; border: 1px solid var(--nx-border); border-radius: 7px;
    padding: 5px 11px; cursor: pointer; display: flex; align-items: center; gap: 5px;
    font-size: 11.5px; color: var(--nx-muted); font-family: inherit;
    transition: background .15s, border-color .15s, color .15s;
}
.vp-sound-btn.is-on { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }

/* ── Grid ── */
.vp-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 10px; }

/* ── Card ── */
.vp-card {
    background: var(--nx-surface); border: 1px solid var(--nx-border); border-radius: 10px;
    padding: 12px 14px; display: flex; flex-direction: column; gap: 9px;
    position: relative; transition: box-shadow .15s, transform .15s;
}
.vp-card:hover { box-shadow: 0 2px 14px rgba(0,0,0,.07); transform: translateY(-1px); }
.vp-card--active { border-left: 3px solid #22c55e; }
.vp-card--idle   { border-left: 3px solid #f59e0b; }
.vp-card--hidden { border-left: 3px solid #94a3b8; }

/* New visitor animation */
@keyframes vp-new-in   { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
@keyframes vp-glow-out { 0%{box-shadow:0 0 0 0 rgba(34,197,94,.3)} 60%{box-shadow:0 0 0 5px rgba(34,197,94,.15)} 100%{box-shadow:0 0 0 0 rgba(34,197,94,0)} }
.vp-card--new { animation: vp-new-in .3s ease-out both, vp-glow-out 2.5s ease-out both; }
@keyframes vp-badge-pop { from{opacity:0;transform:scale(.6)} to{opacity:1;transform:scale(1)} }
.vp-new-badge {
    display: none; align-items: center;
    padding: 1px 6px; border-radius: 99px;
    font-size: 9px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
    background: #22c55e; color: #fff;
    animation: vp-badge-pop .2s cubic-bezier(.34,1.56,.64,1) both;
}
.vp-new-badge.visible { display: inline-flex; }

/* Card top */
.vp-card__top  { display: flex; align-items: flex-start; gap: 9px; }
.vp-card__av   {
    width: 34px; height: 34px; border-radius: 8px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; color: #fff;
}
.vp-card__meta { flex: 1; min-width: 0; }
.vp-card__name { font-size: 12.5px; font-weight: 700; color: var(--nx-text); display: flex; align-items: center; gap: 5px; flex-wrap: wrap; line-height: 1.3; }
.vp-card__sub  { font-size: 11px; color: var(--nx-muted); margin-top: 3px; display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }

/* Pill badges */
.vp-pill {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 1px 7px; border-radius: 99px; font-size: 10px; font-weight: 700;
}
.vp-pill--active { background:#dcfce7; color:#15803d; }
.vp-pill--idle   { background:#fef3c7; color:#92400e; }
.vp-pill--hidden { background:#f1f5f9; color:#475569; }
.vp-pill--chat   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.vp-pill--open   { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.vp-pill--min    { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }

/* Page history */
.vp-history { border: 1px solid var(--nx-border); border-radius: 7px; overflow: hidden; background: var(--nx-surf2); }
.vp-history-row {
    display: flex; align-items: center; gap: 6px; padding: 4px 8px;
    border-bottom: 1px solid var(--nx-border); font-size: 10.5px; color: var(--nx-muted);
}
.vp-history-row:last-child { border-bottom: none; }
.vp-history-row--now { background: #f0fdf4; }
.vp-hdot { width: 5px; height: 5px; border-radius: 50%; background: #cbd5e1; flex-shrink: 0; }
.vp-hdot--live { background: #22c55e; animation: vp-pulse 1.5s ease-in-out infinite; }

/* Stats row */
.vp-stats { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.vp-stat  { display: flex; align-items: center; gap: 3px; font-size: 11px; color: var(--nx-muted); }
.vp-stat strong { color: var(--nx-text); font-weight: 700; }

/* Action buttons */
.vp-card__actions { display: flex; gap: 5px; flex-wrap: wrap; }
.vp-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 600;
    cursor: pointer; border: none; font-family: inherit; transition: background .12s;
    text-decoration: none;
}
.vp-btn--chat  { background: #22c55e; color: #fff; }
.vp-btn--chat:hover { background: #16a34a; }
.vp-btn--goto  { background: #1e293b; color: #f8fafc; }
.vp-btn--goto:hover { background: #0f172a; }
.vp-btn--ban   { background: transparent; border: 1px solid #fecaca; color: #dc2626; }
.vp-btn--ban:hover { background: #fef2f2; }

/* Empty state */
.vp-empty {
    text-align: center; padding: 60px 24px;
    background: var(--nx-surface); border: 1px solid var(--nx-border); border-radius: 12px; color: var(--nx-muted);
}
.vp-empty svg { margin: 0 auto 12px; display: block; opacity: .3; }
.vp-empty h3 { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
.vp-empty p  { font-size: 13px; }

/* Section title */
.vp-section-title {
    font-size: 11.5px; font-weight: 700; color: var(--nx-muted);
    text-transform: uppercase; letter-spacing: .06em;
    margin-bottom: 8px; display: flex; align-items: center; gap: 7px;
}

/* Banned list */
.vp-banned-list { background: var(--nx-surface); border: 1px solid var(--nx-border); border-radius: 8px; overflow: hidden; }
.vp-banned-row {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 14px; border-bottom: 1px solid var(--nx-border); font-size: 12.5px; color: var(--nx-text);
}
.vp-banned-row:last-child { border-bottom: none; }
.vp-btn--unban {
    margin-left: auto; padding: 3px 9px; border-radius: 6px;
    font-size: 11px; font-weight: 600; cursor: pointer;
    background: var(--nx-surf2); border: 1px solid var(--nx-border); color: var(--nx-text); font-family: inherit;
}
.vp-btn--unban:hover { background: var(--nx-border); }

/* Modals */
.vp-overlay { position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px; }
.vp-modal { background:var(--nx-surface);border-radius:12px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;display:flex;flex-direction:column; }
.vp-modal__hd { padding:16px 20px;border-bottom:1px solid var(--nx-border);display:flex;align-items:center;justify-content:space-between; }
.vp-modal__title { font-size:14px;font-weight:700;color:var(--nx-text); }
.vp-modal__close { background:none;border:none;cursor:pointer;color:var(--nx-muted);display:flex;padding:2px; }
.vp-modal__close:hover { color:var(--nx-text); }
.vp-modal__body { padding:18px 20px;display:flex;flex-direction:column;gap:12px; }
.vp-modal__ft { padding:12px 20px;border-top:1px solid var(--nx-border);display:flex;justify-content:flex-end;gap:8px; }
.vp-label { font-size:11px;font-weight:700;color:var(--nx-muted);text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:5px; }
.vp-input,.vp-textarea { width:100%;border:1px solid var(--nx-border);border-radius:8px;padding:8px 11px;font-size:13px;font-family:inherit;color:var(--nx-text);outline:none;background:var(--nx-surf2);box-sizing:border-box;transition:border-color .15s; }
.vp-input:focus,.vp-textarea:focus { border-color:#22c55e;background:var(--nx-surface); }
.vp-textarea { resize:vertical;min-height:72px; }
.vp-btn-ghost { background:transparent;border:1px solid var(--nx-border);border-radius:8px;padding:7px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;color:var(--nx-text); }
.vp-btn-ghost:hover { background:var(--nx-surf2); }
.vp-btn-primary { background:#22c55e;color:#fff;border:none;border-radius:8px;padding:7px 18px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit; }
.vp-btn-primary:hover { background:#16a34a; }
.vp-btn-danger  { background:#dc2626;color:#fff;border:none;border-radius:8px;padding:7px 18px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit; }
.vp-btn-danger:hover  { background:#b91c1c; }
</style>

{{-- ══ Main wrapper — x-data aquí para que Alpine lo inicialice correctamente ══ --}}
<div class="vp-page"
     wire:poll.10000ms="notifyCount"
     x-data="{
        audioCtx: null,
        soundEnabled: localStorage.getItem('nx_visitor_sound') !== 'false',
        knownIds: new Set({{ json_encode($visitorIds) }}),
        newIds: new Set(),

        toggleSound() {
            if (!this.soundEnabled && !this.audioCtx) {
                try { this.audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) {}
            }
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('nx_visitor_sound', this.soundEnabled ? 'true' : 'false');
        },

        playDing() {
            if (!this.soundEnabled) return;
            if (!this.audioCtx) {
                try { this.audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) { return; }
            }
            if (this.audioCtx.state === 'suspended') {
                this.audioCtx.resume().then(() => this.playDing()).catch(() => {});
                return;
            }
            try {
                [820, 1050].forEach((freq, i) => {
                    const o = this.audioCtx.createOscillator();
                    const g = this.audioCtx.createGain();
                    o.connect(g); g.connect(this.audioCtx.destination);
                    o.type = 'sine'; o.frequency.value = freq;
                    const t = this.audioCtx.currentTime + i * 0.13;
                    g.gain.setValueAtTime(0, t);
                    g.gain.linearRampToValueAtTime(0.1, t + 0.02);
                    g.gain.exponentialRampToValueAtTime(0.001, t + 0.5);
                    o.start(t); o.stop(t + 0.5);
                });
            } catch(e) {}
        },

        onVisitorUpdate(ids) {
            const incoming = ids.filter(id => !this.knownIds.has(id));
            if (incoming.length > 0) {
                this.playDing();
                incoming.forEach(id => {
                    this.newIds.add(id);
                    setTimeout(() => {
                        this.newIds.delete(id);
                        const card = document.querySelector('[data-visitor-id=\'' + id + '\']');
                        if (card) {
                            card.classList.remove('vp-card--new');
                            const badge = card.querySelector('.vp-new-badge');
                            if (badge) badge.classList.remove('visible');
                        }
                    }, 3500);
                });
            }
            this.knownIds = new Set(ids);
        }
     }"
     x-init="
        const _vp = $data;
        Livewire.on('visitor-count-updated', (data) => {
            _vp.onVisitorUpdate(data[0]?.ids ?? []);
        });
        document.addEventListener('livewire:updated', () => {
            _vp.newIds.forEach(id => {
                const card = document.querySelector('[data-visitor-id=\'' + id + '\']');
                if (card && !card.classList.contains('vp-card--new')) {
                    card.classList.add('vp-card--new');
                    const badge = card.querySelector('.vp-new-badge');
                    if (badge) {
                        badge.classList.add('visible');
                        setTimeout(() => badge.classList.remove('visible'), 3000);
                    }
                }
            });
        });
     ">

{{-- ── Header ── --}}
<div class="vp-header">
    <div class="vp-title">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="19" height="19"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        Visitantes en Vivo
    </div>
    <div style="display:flex;align-items:center;gap:8px">
        {{-- Sound toggle --}}
        <button @click="toggleSound()"
                :class="soundEnabled ? 'vp-sound-btn is-on' : 'vp-sound-btn'"
                :title="soundEnabled ? 'Click para silenciar' : 'Click para activar sonido'">
            <svg x-show="!soundEnabled" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
            </svg>
            <svg x-show="soundEnabled" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M12 6v12m-3-9.659A8 8 0 014 12a8 8 0 015 7.659M3 12h1"/>
            </svg>
            <span x-text="soundEnabled ? 'Sonido ON' : 'Sonido OFF'" style="font-weight:600"></span>
        </button>
        <div class="vp-count-badge">
            <span class="vp-dot"></span>
            {{ $visitors->count() }} {{ $visitors->count() === 1 ? 'visitante' : 'visitantes' }} ahora
        </div>
    </div>
</div>

{{-- ── Visitor cards ── --}}
@if($visitors->isEmpty())
<div class="vp-empty">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="40" height="40"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
    <h3>Nadie en el sitio ahora</h3>
    <p>Cuando un visitante abra una página con el widget aparecerá aquí automáticamente.</p>
</div>
@else
<div class="vp-grid">
@foreach($visitors as $visitor)
@php
    $status        = $visitor->status;
    $tabHidden     = !$visitor->tab_visible;
    $displayStatus = $tabHidden ? 'hidden' : $status;
    $palette       = ['#0ea5e9','#10b981','#f59e0b','#ef4444','#ec4899','#6366f1','#8b5cf6','#14b8a6'];
    $avatarColor   = $palette[abs(crc32($visitor->visitor_key)) % count($palette)];
    $pages         = $visitor->pages_visited ?? [];
    $prevPages     = array_slice($pages, 0, -1);
    $timeOnSite    = $visitor->time_on_site;
    $timeLabel     = $timeOnSite < 60 ? $timeOnSite.'s' : floor($timeOnSite/60).'m '.($timeOnSite%60).'s';
@endphp
<div class="vp-card vp-card--{{ $displayStatus }}"
     wire:key="visitor-{{ $visitor->id }}"
     data-visitor-id="{{ $visitor->id }}">

    {{-- Top row: avatar + info + status --}}
    <div class="vp-card__top">
        <div class="vp-card__av" style="background:{{ $avatarColor }}">
            {{ strtoupper(substr($visitor->friendly_name, 0, 1)) }}
        </div>
        <div class="vp-card__meta">
            <div class="vp-card__name">
                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:120px" title="{{ $visitor->friendly_name }}">{{ $visitor->friendly_name }}</span>
                <span class="vp-new-badge">Nuevo</span>
                @if($visitor->session_id)
                <span class="vp-pill vp-pill--chat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="8" height="8"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Chat
                </span>
                @endif
            </div>
            <div class="vp-card__sub">
                <span class="vp-pill vp-pill--{{ $displayStatus }}">
                    <span style="width:4px;height:4px;border-radius:50%;background:{{ $displayStatus==='active'?'#22c55e':($displayStatus==='idle'?'#f59e0b':'#94a3b8') }}"></span>
                    @if($displayStatus==='active') En línea @elseif($displayStatus==='idle') Inactivo @else Pestaña oculta @endif
                </span>
                @if($visitor->country)
                <span style="font-size:10.5px">{{ $visitor->country }}{{ $visitor->city ? ', '.$visitor->city : '' }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Current page --}}
    @if($visitor->current_url)
    <div class="vp-history">
        <div class="vp-history-row vp-history-row--now">
            <span class="vp-hdot vp-hdot--live"></span>
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;color:#0f172a;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:10.5px">
                    {{ $visitor->page_title ?: parse_url($visitor->current_url, PHP_URL_PATH) }}
                </div>
            </div>
            <span style="font-size:9px;color:#22c55e;font-weight:700;flex-shrink:0;margin-left:6px">AHORA</span>
        </div>
        @foreach(array_slice(array_reverse($prevPages), 0, 3) as $pg)
        <div class="vp-history-row">
            <span class="vp-hdot"></span>
            <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:10.5px">{{ $pg['title'] ?: $pg['url'] }}</span>
            <span style="color:#cbd5e1;font-size:9.5px;flex-shrink:0;margin-left:6px">{{ \Carbon\Carbon::parse($pg['at'])->diffForHumans(null, true, true) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Stats --}}
    <div class="vp-stats">
        <div class="vp-stat">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <strong>{{ $timeLabel }}</strong>
        </div>
        <div class="vp-stat">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <strong>{{ count($pages) }}</strong> <span>págs</span>
        </div>
        @if($visitor->browser)
        <div class="vp-stat">{{ $visitor->browser }}</div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="vp-card__actions">
        @if(!$visitor->session_id)
        <button class="vp-btn vp-btn--chat"
                wire:click="openProactiveModal('{{ $visitor->visitor_key }}', '{{ $visitor->friendly_name }}')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Iniciar chat
        </button>
        @else
        <a href="{{ route('filament.admin.pages.live-inbox') }}" class="vp-btn vp-btn--goto">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Ver chat
        </a>
        @endif
        @if($visitor->ip)
        <button class="vp-btn vp-btn--ban"
                wire:click="openBanModal('{{ $visitor->ip }}')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            Banear IP
        </button>
        @endif
    </div>
</div>
@endforeach
</div>
@endif

{{-- ── Banned IPs ── --}}
@if($banned->isNotEmpty())
<div>
    <div class="vp-section-title">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        IPs Bloqueadas ({{ $banned->count() }})
    </div>
    <div class="vp-banned-list">
        @foreach($banned as $ban)
        <div class="vp-banned-row">
            <span style="font-family:ui-monospace,monospace;font-size:12px;font-weight:700;color:#dc2626">{{ $ban->ip }}</span>
            @if($ban->reason)<span style="color:#64748b;font-size:12px">— {{ $ban->reason }}</span>@endif
            <span style="color:#94a3b8;font-size:11px">{{ $ban->created_at->format('d/m/Y') }}</span>
            <button class="vp-btn--unban" wire:click="unbanIp({{ $ban->id }})" wire:confirm="¿Desbloquear esta IP?">Desbloquear</button>
        </div>
        @endforeach
    </div>
</div>
@endif

</div>{{-- /.vp-page --}}

{{-- ═══ MODAL Proactive ═══ --}}
@if($showProactiveModal)
<div class="vp-overlay" wire:click.self="$set('showProactiveModal', false)">
    <div class="vp-modal">
        <div class="vp-modal__hd">
            <span class="vp-modal__title">Iniciar chat con {{ $proactiveVisitorName }}</span>
            <button wire:click="$set('showProactiveModal', false)" class="vp-modal__close">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="vp-modal__body">
            <p style="font-size:13px;color:#64748b">Se abrirá el widget en el navegador del visitante automáticamente en el próximo ciclo (~15s).</p>
            <div>
                <label class="vp-label">Mensaje de saludo (opcional)</label>
                <textarea wire:model="proactiveMessage" class="vp-textarea" placeholder="¡Hola! ¿En qué te puedo ayudar?"></textarea>
            </div>
        </div>
        <div class="vp-modal__ft">
            <button wire:click="$set('showProactiveModal', false)" class="vp-btn-ghost">Cancelar</button>
            <button wire:click="triggerProactiveChat" class="vp-btn-primary">Abrir chat</button>
        </div>
    </div>
</div>
@endif

{{-- ═══ MODAL Ban ═══ --}}
@if($showBanModal)
<div class="vp-overlay" wire:click.self="$set('showBanModal', false)">
    <div class="vp-modal">
        <div class="vp-modal__hd">
            <span class="vp-modal__title">Bloquear IP {{ $banIp }}</span>
            <button wire:click="$set('showBanModal', false)" class="vp-modal__close">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="vp-modal__body">
            <p style="font-size:13px;color:#64748b">El visitante con esta IP no podrá abrir el chat ni enviar mensajes.</p>
            <div>
                <label class="vp-label">Motivo (opcional)</label>
                <input type="text" wire:model="banReason" class="vp-input" placeholder="Ej: Spam, comportamiento inapropiado…">
            </div>
        </div>
        <div class="vp-modal__ft">
            <button wire:click="$set('showBanModal', false)" class="vp-btn-ghost">Cancelar</button>
            <button wire:click="banIp" class="vp-btn-danger">Bloquear IP</button>
        </div>
    </div>
</div>
@endif

</x-filament-panels::page>
