<x-filament-panels::page>

@php
$visitors    = $this->activeVisitors;
$banned      = $this->bannedIps;
$visitorIds  = $visitors->pluck('id')->values()->all();

// Pass first_seen_at timestamps so JS can run the counter client-side
$visitorTimes = $visitors->pluck('first_seen_at', 'id')->map(fn($dt) => $dt?->timestamp)->toArray();
@endphp

<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

/* ── Page ── */
.vp-page { display:flex; flex-direction:column; padding:0 0 48px; }

/* ── Toolbar ── */
.vp-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    padding:11px 20px; border-bottom:1px solid var(--nx-border);
    background:var(--nx-surface); position:sticky; top:0; z-index:10;
}
.vp-live-badge {
    display:inline-flex; align-items:center; gap:5px;
    background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;
    border-radius:99px; font-size:11px; font-weight:600; padding:2px 9px;
}
.vp-live-dot { width:6px; height:6px; border-radius:50%; background:#22c55e; flex-shrink:0;
    animation:vp-pulse 1.5s ease-in-out infinite; }
@keyframes vp-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }

/* ── Sound toggle ── */
.vp-sound-btn {
    background:none; border:1px solid var(--nx-border); border-radius:6px;
    padding:4px 10px; cursor:pointer; display:flex; align-items:center; gap:4px;
    font-size:11px; color:var(--nx-muted); font-family:inherit; transition:all .15s;
}
.vp-sound-btn.is-on { background:#f0fdf4; border-color:#bbf7d0; color:#15803d; }

/* ── Table ── */
.vp-table-wrap { overflow-x:auto; }
.vp-table { width:100%; border-collapse:collapse; font-size:12.5px; }
.vp-table thead th {
    padding:7px 14px; text-align:left;
    font-size:10.5px; font-weight:600; color:var(--nx-muted);
    text-transform:uppercase; letter-spacing:.06em;
    border-bottom:1px solid var(--nx-border);
    background:var(--nx-surf2); white-space:nowrap;
}
.vp-table tbody tr {
    border-bottom:1px solid var(--nx-border);
    transition:background .1s;
}
.vp-table tbody tr:hover { background:var(--nx-surf2); }
.vp-table tbody td { padding:9px 14px; vertical-align:middle; }

/* Row entrance */
@keyframes vp-row-in { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }
.vp-row-new-enter { animation:vp-row-in .3s ease-out both; }
@keyframes vp-glow-row { 0%{background:rgba(34,197,94,.10)} 80%{background:rgba(34,197,94,.04)} 100%{background:transparent} }
.vp-row-new-glow { animation:vp-glow-row 3s ease-out both; }

/* ── Visitor identity ── */
.vp-id-cell { display:flex; align-items:center; gap:8px; }
.vp-avatar {
    width:28px; height:28px; border-radius:6px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:10px; font-weight:700; color:#fff; letter-spacing:-.3px;
}
.vp-visitor-name {
    font-size:12px; font-weight:500; color:var(--nx-text);
    font-family:ui-monospace,monospace; letter-spacing:-.2px;
}
.vp-new-badge {
    display:none; align-items:center; padding:1px 5px; border-radius:99px;
    font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
    background:#22c55e; color:#fff;
    animation:vp-badge-pop .25s cubic-bezier(.34,1.56,.64,1) both;
}
.vp-new-badge.visible { display:inline-flex; }
@keyframes vp-badge-pop { from{opacity:0;transform:scale(.5)} to{opacity:1;transform:scale(1)} }

/* ── Status pill ── */
.vp-status {
    display:inline-flex; align-items:center; gap:4px;
    padding:2px 8px; border-radius:99px; font-size:11px; font-weight:500;
}
.vp-sdot { width:5px; height:5px; border-radius:50%; flex-shrink:0; }
.vp-status--active { background:#dcfce7; color:#15803d; }
.vp-status--active .vp-sdot { background:#22c55e; animation:vp-pulse 1.5s ease-in-out infinite; }
.vp-status--idle   { background:#fef3c7; color:#92400e; }
.vp-status--idle   .vp-sdot { background:#f59e0b; }
.vp-status--hidden { background:#f1f5f9; color:#64748b; }
.vp-status--hidden .vp-sdot { background:#94a3b8; }
.vp-chat-pill {
    display:inline-flex; align-items:center; gap:3px; margin-top:3px;
    padding:1px 6px; border-radius:99px; font-size:10px; font-weight:500;
    background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;
}

/* ── Page cell ── */
.vp-page-title { font-size:12px; font-weight:500; color:var(--nx-text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:200px; }
.vp-page-url   { font-size:10.5px; color:var(--nx-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:200px; margin-top:1px; }

/* ── Device cell ── */
.vp-device { font-size:11.5px; color:var(--nx-text); white-space:nowrap; }
.vp-device-sub { font-size:10.5px; color:var(--nx-muted); margin-top:1px; }
.vp-device-icon { display:inline-flex; align-items:center; gap:4px; }

/* ── Referrer cell ── */
.vp-ref { font-size:11.5px; color:var(--nx-text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:160px; }
.vp-ref-direct { font-size:11px; color:var(--nx-muted); font-style:italic; }
.vp-ref-icon { display:inline-block; width:12px; height:12px; border-radius:2px; margin-right:3px; vertical-align:middle; }

/* ── Timer ── */
.vp-timer { font-size:12px; font-weight:500; color:var(--nx-text); font-variant-numeric:tabular-nums; white-space:nowrap; font-family:ui-monospace,monospace; letter-spacing:.02em; }

/* ── Location ── */
.vp-location { font-size:11.5px; color:var(--nx-muted); white-space:nowrap; }

/* ── Actions ── */
.vp-actions { display:flex; align-items:center; gap:5px; }
.vp-btn {
    display:inline-flex; align-items:center; gap:3px;
    padding:4px 9px; border-radius:6px; font-size:11px; font-weight:500;
    cursor:pointer; border:none; font-family:inherit; transition:background .12s;
    text-decoration:none; white-space:nowrap;
}
.vp-btn--chat  { background:#22c55e; color:#fff; }
.vp-btn--chat:hover { background:#16a34a; }
.vp-btn--goto  { background:#1e293b; color:#f8fafc; }
.vp-btn--goto:hover { background:#0f172a; }
.vp-btn--ban   { background:transparent; border:1px solid #fecaca; color:#dc2626; padding:4px 7px; }
.vp-btn--ban:hover { background:#fef2f2; }

/* ── Skeleton ── */
.vp-skeleton-row td { padding:10px 14px; }
.sk { border-radius:5px; background:linear-gradient(90deg,var(--nx-border) 25%,var(--nx-surf2) 50%,var(--nx-border) 75%); background-size:200% 100%; animation:sk-shimmer 1.4s ease-in-out infinite; }
@keyframes sk-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.sk-av   { width:28px; height:28px; border-radius:6px; flex-shrink:0; display:inline-block; }
.sk-text { height:11px; border-radius:4px; display:inline-block; }
.sk-pill { height:18px; border-radius:99px; display:inline-block; }

/* ── Empty ── */
.vp-empty { text-align:center; padding:64px 24px; color:var(--nx-muted); }
.vp-empty-icon { margin:0 auto 14px; display:flex; align-items:center; justify-content:center; width:48px; height:48px; border-radius:12px; background:var(--nx-surf2); }
.vp-empty h3 { font-size:13.5px; font-weight:600; margin-bottom:5px; color:var(--nx-text); }
.vp-empty p  { font-size:12px; }

/* ── Banned ── */
.vp-banned-wrap { margin:24px 20px 0; background:var(--nx-surface); border:1px solid var(--nx-border); border-radius:10px; overflow:hidden; }
.vp-section-label { padding:8px 14px; font-size:10.5px; font-weight:600; color:var(--nx-muted); text-transform:uppercase; letter-spacing:.07em; background:var(--nx-surf2); border-bottom:1px solid var(--nx-border); display:flex; align-items:center; gap:6px; }
.vp-banned-row { display:flex; align-items:center; gap:10px; padding:9px 14px; border-bottom:1px solid var(--nx-border); font-size:12px; color:var(--nx-text); }
.vp-banned-row:last-child { border-bottom:none; }
.vp-btn--unban { margin-left:auto; padding:3px 9px; border-radius:6px; font-size:11px; font-weight:500; cursor:pointer; background:var(--nx-surf2); border:1px solid var(--nx-border); color:var(--nx-text); font-family:inherit; }
.vp-btn--unban:hover { background:var(--nx-border); }

/* ── Modals ── */
.vp-overlay { position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px; }
.vp-modal { background:var(--nx-surface);border-radius:12px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;display:flex;flex-direction:column; }
.vp-modal__hd { padding:16px 20px;border-bottom:1px solid var(--nx-border);display:flex;align-items:center;justify-content:space-between; }
.vp-modal__title { font-size:14px;font-weight:600;color:var(--nx-text); }
.vp-modal__close { background:none;border:none;cursor:pointer;color:var(--nx-muted);display:flex;padding:2px; }
.vp-modal__close:hover { color:var(--nx-text); }
.vp-modal__body { padding:18px 20px;display:flex;flex-direction:column;gap:12px; }
.vp-modal__ft { padding:12px 20px;border-top:1px solid var(--nx-border);display:flex;justify-content:flex-end;gap:8px; }
.vp-label { font-size:11px;font-weight:600;color:var(--nx-muted);text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:5px; }
.vp-input,.vp-textarea { width:100%;border:1px solid var(--nx-border);border-radius:7px;padding:7px 11px;font-size:13px;font-family:inherit;color:var(--nx-text);outline:none;background:var(--nx-surf2);box-sizing:border-box;transition:border-color .15s; }
.vp-input:focus,.vp-textarea:focus { border-color:#22c55e;background:var(--nx-surface); }
.vp-textarea { resize:vertical;min-height:72px; }
.vp-btn-ghost { background:transparent;border:1px solid var(--nx-border);border-radius:7px;padding:7px 16px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;color:var(--nx-text); }
.vp-btn-ghost:hover { background:var(--nx-surf2); }
.vp-btn-primary { background:#22c55e;color:#fff;border:none;border-radius:7px;padding:7px 18px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit; }
.vp-btn-primary:hover { background:#16a34a; }
.vp-btn-danger  { background:#dc2626;color:#fff;border:none;border-radius:7px;padding:7px 18px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit; }
.vp-btn-danger:hover  { background:#b91c1c; }
</style>

{{-- ══ Visitor timestamps for client-side timer ══ --}}
<script>
window._vp_times = @json($visitorTimes);
</script>

<audio id="vp-ding" preload="auto" style="display:none">
    <source src="/ding.wav" type="audio/wav">
</audio>

{{-- ══ Main wrapper ══ --}}
<div class="vp-page"
     wire:poll.8000ms="notifyCount"
     x-data="{
        soundEnabled: localStorage.getItem('nx_visitor_sound') !== 'false',
        knownIds: new Set({{ json_encode($visitorIds) }}),
        newIds: new Set(),
        timerInterval: null,

        init() {
            this.startTimers();
            const _vp = this;

            Livewire.on('visitor-count-updated', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                const ids = payload?.ids ?? [];
                const incoming = ids.filter(id => !_vp.knownIds.has(id));
                if (incoming.length > 0) {
                    _vp.playDing();
                    incoming.forEach(id => {
                        _vp.newIds.add(id);
                        setTimeout(() => {
                            _vp.newIds.delete(id);
                            const row = document.querySelector('[data-vid=\'' + id + '\']');
                            if (row) {
                                row.classList.remove('vp-row-new-glow');
                                row.querySelector('.vp-new-badge')?.classList.remove('visible');
                            }
                        }, 4000);
                    });
                }
                _vp.knownIds = new Set(ids);
            });

            document.addEventListener('livewire:updated', () => {
                _vp.newIds.forEach(id => {
                    const row = document.querySelector('[data-vid=\'' + id + '\']');
                    if (row) {
                        row.classList.add('vp-row-new-enter', 'vp-row-new-glow');
                        const badge = row.querySelector('.vp-new-badge');
                        if (badge) {
                            badge.classList.add('visible');
                            setTimeout(() => badge.classList.remove('visible'), 3500);
                        }
                    }
                });
                _vp.startTimers();
            });
        },

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('nx_visitor_sound', this.soundEnabled ? 'true' : 'false');
            if (this.soundEnabled) this.playDing();
        },

        playDing() {
            if (!this.soundEnabled) return;
            const el = document.getElementById('vp-ding');
            if (!el) return;
            el.currentTime = 0;
            const playPromise = el.play();
            if (playPromise !== undefined) {
                playPromise.catch((e) => {
                    if (e.name === 'NotAllowedError' || e.name === 'NotSupportedError') {
                        console.warn('El navegador bloqueó el sonido automático.', e);
                        // Mostrar alerta visual si no suena
                        alert('¡Nuevo visitante! El navegador silenció el aviso. Haz click en la página para permitir sonidos.');
                    }
                });
            }
        },

        startTimers() {
            if (this.timerInterval) clearInterval(this.timerInterval);
            const times = window._vp_times || {};
            const now = Math.floor(Date.now() / 1000);

            function tick() {
                const nowTs = Math.floor(Date.now() / 1000);
                document.querySelectorAll('[data-timer]').forEach(el => {
                    const startTs = parseInt(el.dataset.timer, 10);
                    if (!startTs) return;
                    const secs = Math.max(0, nowTs - startTs);
                    const h = Math.floor(secs / 3600);
                    const m = Math.floor((secs % 3600) / 60);
                    const s = secs % 60;
                    el.textContent = (h > 0 ? String(h).padStart(2,'0') + ':' : '') +
                        String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
                });
            }
            tick();
            this.timerInterval = setInterval(tick, 1000);
        }
     }">

{{-- ── Toolbar ── --}}
<div class="vp-toolbar">
    <div style="display:flex;align-items:center;gap:10px">
        <span style="font-size:13.5px;font-weight:600;color:var(--nx-text)">Visitantes en Vivo</span>
        <div class="vp-live-badge">
            <span class="vp-live-dot"></span>
            {{ $visitors->count() }} {{ $visitors->count() === 1 ? 'ahora' : 'ahora' }}
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px">
        <button @click="toggleSound()"
                :class="soundEnabled ? 'vp-sound-btn is-on' : 'vp-sound-btn'">
            <template x-if="!soundEnabled">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                </svg>
            </template>
            <template x-if="soundEnabled">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M12 6v12M9 9.341A4 4 0 004.929 12 4 4 0 009 14.659"/>
                </svg>
            </template>
            <span x-text="soundEnabled ? 'Sonido ON' : 'Sonido OFF'"></span>
        </button>
    </div>
</div>

{{-- ── Table ── --}}
<div class="vp-table-wrap">
<table class="vp-table">
    <thead>
        <tr>
            <th>Visitante</th>
            <th>Estado</th>
            <th>Página actual</th>
            <th>Dispositivo</th>
            <th>Origen</th>
            <th>Ubicación</th>
            <th>Tiempo</th>
            <th></th>
        </tr>
    </thead>
    <tbody>

    @if($visitors->isEmpty())
    <tr>
        <td colspan="8">
            <div class="vp-empty">
                <div class="vp-empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22" style="opacity:.35">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
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
        $avatarInitials= strtoupper(substr($visitor->visitor_key, -2));

        // Device
        $deviceType = strtolower($visitor->device ?? '');
        $isMobile   = str_contains($deviceType, 'mobile') || str_contains($deviceType, 'phone') || str_contains($deviceType, 'tablet');
        $deviceLabel = $visitor->device ?? null;
        $osLabel     = $visitor->os ?? null;
        $browserLabel= $visitor->browser ?? null;

        // Referrer
        $ref = $visitor->referrer ?? null;
        $refHost = null;
        $refDirect = false;
        if ($ref && filter_var($ref, FILTER_VALIDATE_URL)) {
            $refHost = parse_url($ref, PHP_URL_HOST);
            $refHost = preg_replace('/^www\./', '', $refHost ?? '');
        } elseif (!$ref || $ref === '' || $ref === 'direct') {
            $refDirect = true;
        }

        // Search engines map
        $searchEngines = ['google' => 'Google', 'bing' => 'Bing', 'yahoo' => 'Yahoo', 'duckduckgo' => 'DuckDuckGo', 'baidu' => 'Baidu'];
        $refLabel = null;
        $isSearch = false;
        if ($refHost) {
            foreach ($searchEngines as $key => $name) {
                if (str_contains($refHost, $key)) { $refLabel = $name; $isSearch = true; break; }
            }
            if (!$refLabel) $refLabel = $refHost;
        }

        $firstSeenTs = $visitor->first_seen_at?->timestamp ?? 0;
    @endphp
    <tr wire:key="v-{{ $visitor->id }}" data-vid="{{ $visitor->id }}">

        {{-- Visitante --}}
        <td>
            <div class="vp-id-cell">
                <div class="vp-avatar" style="background:{{ $avatarColor }}">{{ $avatarInitials }}</div>
                <div>
                    <div style="display:flex;align-items:center;gap:5px">
                        <span class="vp-visitor-name">{{ $visitor->friendly_name }}</span>
                        <span class="vp-new-badge">Nuevo</span>
                    </div>
                    <div style="font-size:10px;color:var(--nx-muted);margin-top:1px;font-family:ui-monospace,monospace">
                        {{ $visitor->ip ?? '' }}
                    </div>
                </div>
            </div>
        </td>

        {{-- Estado --}}
        <td>
            <div style="display:flex;flex-direction:column;align-items:flex-start;gap:3px">
                <span class="vp-status vp-status--{{ $displayStatus }}">
                    <span class="vp-sdot"></span>
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

        {{-- Página actual --}}
        <td>
            @if($visitor->current_url)
            <div class="vp-page-title" title="{{ $visitor->page_title ?: $visitor->current_url }}">
                {{ $visitor->page_title ?: parse_url($visitor->current_url, PHP_URL_PATH) }}
            </div>
            <div class="vp-page-url">
                {{ parse_url($visitor->current_url, PHP_URL_HOST) }}{{ parse_url($visitor->current_url, PHP_URL_PATH) }}
            </div>
            @else
            <span style="color:var(--nx-muted);font-size:11px">—</span>
            @endif
        </td>

        {{-- Dispositivo --}}
        <td>
            <div class="vp-device-icon">
                @if($isMobile)
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" style="color:var(--nx-muted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" style="color:var(--nx-muted)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                @endif
                <span class="vp-device" style="font-weight:400">{{ $browserLabel ?? '—' }}</span>
            </div>
            @if($osLabel)
            <div class="vp-device-sub">{{ $osLabel }}</div>
            @endif
        </td>

        {{-- Origen / Referrer --}}
        <td>
            @if($refDirect || !$ref)
            <span class="vp-ref-direct">Directo</span>
            @elseif($isSearch)
            <div style="display:flex;align-items:center;gap:4px">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11" style="color:#f59e0b;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span class="vp-ref">{{ $refLabel }}</span>
            </div>
            @else
            <div style="display:flex;align-items:center;gap:4px" title="{{ $ref }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11" style="color:var(--nx-muted);flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span class="vp-ref">{{ $refLabel }}</span>
            </div>
            @endif
        </td>

        {{-- Ubicación --}}
        <td>
            <span class="vp-location">
                @if($visitor->country)
                    {{ $visitor->country }}{{ $visitor->city ? ', '.$visitor->city : '' }}
                @else
                    <span style="color:var(--nx-muted)">—</span>
                @endif
            </span>
        </td>

        {{-- Tiempo HH:MM:SS contador en vivo --}}
        <td>
            <span class="vp-timer" data-timer="{{ $firstSeenTs }}">00:00</span>
        </td>

        {{-- Acciones --}}
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
                <button class="vp-btn vp-btn--ban" wire:click="openBanModal('{{ $visitor->ip }}')">
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
<div class="vp-banned-wrap">
    <div class="vp-section-label">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        IPs Bloqueadas ({{ $banned->count() }})
    </div>
    @foreach($banned as $ban)
    <div class="vp-banned-row">
        <span style="font-family:ui-monospace,monospace;font-size:12px;font-weight:600;color:#dc2626">{{ $ban->ip }}</span>
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
