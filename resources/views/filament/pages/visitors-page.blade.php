<x-filament-panels::page>

@php
$visitors    = $this->activeVisitors;
$banned      = $this->bannedIps;
$visitorIds  = $visitors->pluck('id')->values()->all();
@endphp

<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

/* ── Page shell ── */
.vp-page { display: flex; flex-direction: column; gap: 0; padding: 0 0 48px; }

/* ── Toolbar ── */
.vp-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid var(--nx-border);
    background: var(--nx-surface); position: sticky; top: 0; z-index: 10;
}
.vp-toolbar-left  { display: flex; align-items: center; gap: 10px; }
.vp-toolbar-right { display: flex; align-items: center; gap: 8px; }
.vp-title { font-size: 14px; font-weight: 800; color: var(--nx-text); }
.vp-live-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: #dcfce7; color: #15803d;
    border: 1px solid #bbf7d0; border-radius: 99px;
    font-size: 11px; font-weight: 700; padding: 2px 9px;
}
.vp-live-dot { width: 6px; height: 6px; border-radius: 50%; background: #22c55e; animation: vp-pulse 1.5s ease-in-out infinite; flex-shrink:0; }
@keyframes vp-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }

/* ── Sound toggle ── */
.vp-sound-btn {
    background: none; border: 1px solid var(--nx-border); border-radius: 7px;
    padding: 4px 10px; cursor: pointer; display: flex; align-items: center; gap: 4px;
    font-size: 11px; color: var(--nx-muted); font-family: inherit; transition: all .15s;
}
.vp-sound-btn.is-on { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }

/* ── Visitor list table ── */
.vp-list { width: 100%; border-collapse: collapse; }
.vp-list-head th {
    padding: 7px 14px; text-align: left;
    font-size: 10.5px; font-weight: 700; color: var(--nx-muted);
    text-transform: uppercase; letter-spacing: .06em;
    border-bottom: 1px solid var(--nx-border);
    background: var(--nx-surf2);
}
.vp-row {
    border-bottom: 1px solid var(--nx-border);
    transition: background .1s;
    animation: vp-row-in .25s ease-out both;
}
.vp-row:hover { background: var(--nx-surf2); }
@keyframes vp-row-in { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
.vp-row--new { animation: vp-row-in .25s ease-out both, vp-glow-row 2.5s ease-out both; }
@keyframes vp-glow-row { 0%{background:rgba(34,197,94,.12)} 100%{background:transparent} }

.vp-row td { padding: 9px 14px; vertical-align: middle; }

/* Visitor identity cell */
.vp-id-cell { display: flex; align-items: center; gap: 9px; }
.vp-avatar {
    width: 30px; height: 30px; border-radius: 7px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; color: #fff; letter-spacing: -.5px;
}
.vp-visitor-name { font-size: 12.5px; font-weight: 700; color: var(--nx-text); font-family: ui-monospace, monospace; }
.vp-new-badge {
    display: none; align-items: center;
    padding: 1px 5px; border-radius: 99px;
    font-size: 9px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
    background: #22c55e; color: #fff;
    animation: vp-badge-pop .2s cubic-bezier(.34,1.56,.64,1) both;
}
.vp-new-badge.visible { display: inline-flex; }
@keyframes vp-badge-pop { from{opacity:0;transform:scale(.5)} to{opacity:1;transform:scale(1)} }

/* Status pill */
.vp-status {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 99px;
    font-size: 10.5px; font-weight: 700;
}
.vp-status-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink:0; }
.vp-status--active { background: #dcfce7; color: #15803d; }
.vp-status--active .vp-status-dot { background: #22c55e; animation: vp-pulse 1.5s ease-in-out infinite; }
.vp-status--idle   { background: #fef3c7; color: #92400e; }
.vp-status--idle   .vp-status-dot { background: #f59e0b; }
.vp-status--hidden { background: #f1f5f9; color: #475569; }
.vp-status--hidden .vp-status-dot { background: #94a3b8; }

/* Page cell */
.vp-page-cell { max-width: 240px; }
.vp-page-title { font-size: 12px; font-weight: 600; color: var(--nx-text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.vp-page-url   { font-size: 10.5px; color: var(--nx-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

/* Location */
.vp-location { font-size: 11.5px; color: var(--nx-muted); white-space:nowrap; }

/* Time */
.vp-time-cell { font-size: 12px; font-weight: 700; color: var(--nx-text); white-space: nowrap; font-variant-numeric: tabular-nums; }
.vp-pages-count { font-size: 11px; color: var(--nx-muted); white-space:nowrap; }

/* Chat pill */
.vp-chat-pill {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 7px; border-radius: 99px; font-size: 10px; font-weight: 700;
    background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
}

/* Action buttons */
.vp-actions { display: flex; align-items: center; gap: 5px; white-space:nowrap; }
.vp-btn {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 4px 9px; border-radius: 6px; font-size: 11px; font-weight: 600;
    cursor: pointer; border: none; font-family: inherit; transition: background .12s;
    text-decoration: none;
}
.vp-btn--chat  { background: #22c55e; color: #fff; }
.vp-btn--chat:hover { background: #16a34a; }
.vp-btn--goto  { background: #1e293b; color: #f8fafc; }
.vp-btn--goto:hover { background: #0f172a; }
.vp-btn--ban   { background: transparent; border: 1px solid #fecaca; color: #dc2626; padding: 3px 7px; }
.vp-btn--ban:hover { background: #fef2f2; }

/* ── Skeleton loading ── */
.vp-skeleton-row td { padding: 10px 14px; }
.sk { border-radius: 5px; background: linear-gradient(90deg, var(--nx-border) 25%, var(--nx-surf2) 50%, var(--nx-border) 75%); background-size: 200% 100%; animation: sk-shimmer 1.4s ease-in-out infinite; }
@keyframes sk-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.sk-av   { width:30px; height:30px; border-radius:7px; flex-shrink:0; }
.sk-text { height:12px; border-radius:4px; }
.sk-pill { height:18px; border-radius:99px; }

/* ── Empty state ── */
.vp-empty {
    text-align: center; padding: 64px 24px;
    color: var(--nx-muted);
}
.vp-empty-icon { margin: 0 auto 14px; display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:14px; background:var(--nx-surf2); }
.vp-empty h3 { font-size: 14px; font-weight: 700; margin-bottom: 5px; color: var(--nx-text); }
.vp-empty p  { font-size: 12.5px; }

/* ── Section label ── */
.vp-section-label {
    padding: 8px 14px; font-size: 10.5px; font-weight: 700; color: var(--nx-muted);
    text-transform: uppercase; letter-spacing: .07em;
    background: var(--nx-surf2); border-bottom: 1px solid var(--nx-border);
    display: flex; align-items: center; gap: 6px;
}

/* ── Banned list ── */
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

/* ── Modals ── */
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

{{-- ══ Main wrapper ══ --}}
<div class="vp-page"
     wire:poll.10000ms="notifyCount"
     x-data="{
        soundEnabled: localStorage.getItem('nx_visitor_sound') !== 'false',
        knownIds: new Set({{ json_encode($visitorIds) }}),
        newIds: new Set(),
        loading: false,

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('nx_visitor_sound', this.soundEnabled ? 'true' : 'false');
        },

        playDing() {
            if (!this.soundEnabled) return;
            const el = document.getElementById('vp-ding');
            if (!el) return;
            el.currentTime = 0;
            el.play().catch(() => {});
        },

        onVisitorUpdate(ids) {
            const incoming = ids.filter(id => !this.knownIds.has(id));
            if (incoming.length > 0) {
                this.playDing();
                incoming.forEach(id => {
                    this.newIds.add(id);
                    setTimeout(() => {
                        this.newIds.delete(id);
                        const row = document.querySelector('[data-visitor-id=\'' + id + '\']');
                        if (row) {
                            row.classList.remove('vp-row--new');
                            const badge = row.querySelector('.vp-new-badge');
                            if (badge) badge.classList.remove('visible');
                        }
                    }, 4000);
                });
            }
            this.knownIds = new Set(ids);
            this.loading = false;
        }
     }"
     x-init="
        const _vp = $data;

        // Show skeleton briefly on first poll
        document.addEventListener('livewire:request', () => { _vp.loading = true; });
        document.addEventListener('livewire:response', () => { _vp.loading = false; });

        Livewire.on('visitor-count-updated', (data) => {
            _vp.onVisitorUpdate(data[0]?.ids ?? []);
        });

        document.addEventListener('livewire:updated', () => {
            _vp.newIds.forEach(id => {
                const row = document.querySelector('[data-visitor-id=\'' + id + '\']');
                if (row && !row.classList.contains('vp-row--new')) {
                    row.classList.add('vp-row--new');
                    const badge = row.querySelector('.vp-new-badge');
                    if (badge) {
                        badge.classList.add('visible');
                        setTimeout(() => badge.classList.remove('visible'), 3500);
                    }
                }
            });
        });
     ">

{{-- ── Toolbar ── --}}
<div class="vp-toolbar">
    <div class="vp-toolbar-left">
        <span class="vp-title">Visitantes en Vivo</span>
        <div class="vp-live-badge">
            <span class="vp-live-dot"></span>
            {{ $visitors->count() }} {{ $visitors->count() === 1 ? 'ahora' : 'ahora' }}
        </div>
    </div>
    <div class="vp-toolbar-right">
        <button @click="toggleSound()"
                :class="soundEnabled ? 'vp-sound-btn is-on' : 'vp-sound-btn'">
            <svg x-show="!soundEnabled" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
            </svg>
            <svg x-show="soundEnabled" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M12 6v12M9 9.341A4 4 0 004.929 12 4 4 0 009 14.659"/>
            </svg>
            <span x-text="soundEnabled ? 'Sonido ON' : 'Sonido OFF'"></span>
        </button>
        <audio id="vp-ding" preload="auto" style="display:none">
            <source src="/ding.wav" type="audio/wav">
        </audio>
    </div>
</div>

{{-- ── Visitor list ── --}}
<div style="background:var(--nx-surface);">
    <table class="vp-list">
        <thead class="vp-list-head">
            <tr>
                <th>Visitante</th>
                <th>Estado</th>
                <th>Página actual</th>
                <th>Ubicación</th>
                <th>Tiempo</th>
                <th>Págs</th>
                <th></th>
            </tr>
        </thead>
        <tbody>

        {{-- Skeleton rows shown while polling --}}
        <template x-if="loading && {{ $visitors->count() }} === 0">
            <template x-for="i in 3" :key="i">
                <tr class="vp-skeleton-row">
                    <td><div style="display:flex;align-items:center;gap:9px"><div class="sk sk-av"></div><div class="sk sk-text" style="width:110px"></div></div></td>
                    <td><div class="sk sk-pill" style="width:60px"></div></td>
                    <td><div class="sk sk-text" style="width:160px"></div></td>
                    <td><div class="sk sk-text" style="width:80px"></div></td>
                    <td><div class="sk sk-text" style="width:36px"></div></td>
                    <td><div class="sk sk-text" style="width:20px"></div></td>
                    <td></td>
                </tr>
            </template>
        </template>

        @if($visitors->isEmpty())
        <tr>
            <td colspan="7">
                <div class="vp-empty">
                    <div class="vp-empty-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22" style="opacity:.4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3>Nadie en el sitio ahora</h3>
                    <p>Cuando un visitante abra una página con el widget aparecerá aquí.</p>
                </div>
            </td>
        </tr>
        @else
        @foreach($visitors as $visitor)
        @php
            $status        = $visitor->status;
            $tabHidden     = !$visitor->tab_visible;
            $displayStatus = $tabHidden ? 'hidden' : $status;
            $palette       = ['#0ea5e9','#10b981','#f59e0b','#ef4444','#ec4899','#6366f1','#8b5cf6','#14b8a6'];
            $avatarColor   = $palette[abs(crc32($visitor->visitor_key)) % count($palette)];
            $pages         = $visitor->pages_visited ?? [];
            $timeOnSite    = $visitor->time_on_site;
            $timeLabel     = $timeOnSite < 60 ? $timeOnSite.'s' : floor($timeOnSite/60).'m '.($timeOnSite%60).'s';
            $initials      = '#'.substr($visitor->friendly_name, -3); // last 3 of ID
        @endphp
        <tr class="vp-row"
            wire:key="visitor-{{ $visitor->id }}"
            data-visitor-id="{{ $visitor->id }}">

            {{-- Identity --}}
            <td>
                <div class="vp-id-cell">
                    <div class="vp-avatar" style="background:{{ $avatarColor }}">
                        {{ strtoupper(substr($visitor->visitor_key, -2)) }}
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;gap:5px">
                            <span class="vp-visitor-name">{{ $visitor->friendly_name }}</span>
                            <span class="vp-new-badge">Nuevo</span>
                        </div>
                        @if($visitor->browser)
                        <div style="font-size:10px;color:var(--nx-muted);margin-top:1px">{{ $visitor->browser }}</div>
                        @endif
                    </div>
                </div>
            </td>

            {{-- Status --}}
            <td>
                <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-start">
                    <span class="vp-status vp-status--{{ $displayStatus }}">
                        <span class="vp-status-dot"></span>
                        @if($displayStatus==='active') En línea
                        @elseif($displayStatus==='idle') Inactivo
                        @else Tab oculta @endif
                    </span>
                    @if($visitor->session_id)
                    <span class="vp-chat-pill">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="8" height="8"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Chat activo
                    </span>
                    @endif
                </div>
            </td>

            {{-- Current page --}}
            <td class="vp-page-cell">
                @if($visitor->current_url)
                <div class="vp-page-title" title="{{ $visitor->page_title ?: $visitor->current_url }}">
                    {{ $visitor->page_title ?: parse_url($visitor->current_url, PHP_URL_PATH) }}
                </div>
                <div class="vp-page-url" title="{{ $visitor->current_url }}">
                    {{ parse_url($visitor->current_url, PHP_URL_HOST) }}{{ parse_url($visitor->current_url, PHP_URL_PATH) }}
                </div>
                @else
                <span style="font-size:11px;color:var(--nx-muted)">—</span>
                @endif
            </td>

            {{-- Location --}}
            <td>
                <span class="vp-location">
                    @if($visitor->country)
                        {{ $visitor->country }}{{ $visitor->city ? ', '.$visitor->city : '' }}
                    @else
                        <span style="color:var(--nx-muted)">—</span>
                    @endif
                </span>
            </td>

            {{-- Time on site --}}
            <td>
                <span class="vp-time-cell">{{ $timeLabel }}</span>
            </td>

            {{-- Pages visited --}}
            <td>
                <span class="vp-pages-count">{{ count($pages) }}</span>
            </td>

            {{-- Actions --}}
            <td>
                <div class="vp-actions">
                    @if(!$visitor->session_id)
                    <button class="vp-btn vp-btn--chat"
                            wire:click="openProactiveModal('{{ $visitor->visitor_key }}', '{{ $visitor->friendly_name }}')">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Iniciar chat
                    </button>
                    @else
                    <a href="{{ route('filament.admin.pages.live-inbox') }}" class="vp-btn vp-btn--goto">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Ver chat
                    </a>
                    @endif
                    @if($visitor->ip)
                    <button class="vp-btn vp-btn--ban"
                            wire:click="openBanModal('{{ $visitor->ip }}')">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </button>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
        @endif

        </tbody>
    </table>
</div>

{{-- ── Banned IPs ── --}}
@if($banned->isNotEmpty())
<div style="margin-top:24px;background:var(--nx-surface);border:1px solid var(--nx-border);border-radius:10px;overflow:hidden;">
    <div class="vp-section-label">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        IPs Bloqueadas ({{ $banned->count() }})
    </div>
    @foreach($banned as $ban)
    <div class="vp-banned-row">
        <span style="font-family:ui-monospace,monospace;font-size:12px;font-weight:700;color:#dc2626">{{ $ban->ip }}</span>
        @if($ban->reason)<span style="color:#64748b;font-size:12px">— {{ $ban->reason }}</span>@endif
        <span style="color:#94a3b8;font-size:11px">{{ $ban->created_at->format('d/m/Y') }}</span>
        <button class="vp-btn--unban" wire:click="unbanIp({{ $ban->id }})" wire:confirm="¿Desbloquear esta IP?">Desbloquear</button>
    </div>
    @endforeach
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
