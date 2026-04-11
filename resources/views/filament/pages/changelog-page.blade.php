<x-filament-panels::page>

@php
$releases      = $this->getViewData()['releases'];
$currentVersion = $this->getViewData()['currentVersion'];
$hasReleases   = count($releases) > 0;
@endphp

<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

/* ── Layout ── */
.cl-page { max-width: 780px; margin: 0 auto; padding: 40px 24px 80px; }

/* ── Header ── */
.cl-head { margin-bottom: 48px; }
.cl-head__eyebrow {
    font-size: 11.5px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
    color: #22c55e; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
}
.cl-head__eyebrow::before { content:''; width:18px; height:2px; background:#22c55e; border-radius:2px; display:inline-block; }
.cl-head__title { font-size: 28px; font-weight: 800; color: var(--nx-text); line-height: 1.2; margin-bottom: 10px; }
.cl-head__sub   { font-size: 14px; color: var(--nx-muted); line-height: 1.6; }

/* ── Empty state ── */
.cl-empty {
    text-align: center; padding: 80px 24px;
    border: 1px dashed var(--nx-border); border-radius: 16px;
    color: var(--nx-muted);
}
.cl-empty__icon { margin: 0 auto 16px; opacity: .25; display: block; }
.cl-empty__title { font-size: 16px; font-weight: 700; color: var(--nx-muted); margin-bottom: 6px; }
.cl-empty__sub   { font-size: 13.5px; line-height: 1.6; }

/* ── Timeline ── */
.cl-timeline { display: flex; flex-direction: column; }

.cl-entry {
    display: grid;
    grid-template-columns: 100px 1fr;
    gap: 0 32px;
    padding-bottom: 48px;
    position: relative;
}
.cl-entry:last-child { padding-bottom: 0; }

/* Left column — date + version */
.cl-entry__left {
    text-align: right;
    padding-top: 4px;
    position: relative;
}
.cl-entry__date { font-size: 11px; color: var(--nx-muted); font-weight: 600; line-height: 1; }
.cl-entry__version {
    font-size: 11px; font-weight: 800; color: var(--nx-muted);
    letter-spacing: .04em; margin-top: 4px;
    font-family: ui-monospace, monospace;
}

/* Vertical line */
.cl-entry::after {
    content: '';
    position: absolute;
    left: calc(100px + 16px - 1px); /* center of gap */
    top: 10px; bottom: -10px;
    width: 1px;
    background: var(--nx-border);
}
.cl-entry:last-child::after { display: none; }

/* Dot on the line */
.cl-dot {
    position: absolute;
    left: calc(100px + 32px / 2 - 6px);
    top: 4px;
    width: 13px; height: 13px;
    border-radius: 50%;
    background: var(--nx-surface);
    border: 2px solid var(--nx-border);
    z-index: 1;
}
.cl-dot--latest {
    background: #22c55e;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.15);
}
.cl-dot--pre {
    background: var(--nx-surface);
    border-color: #f59e0b;
    border-style: dashed;
}

/* Right column — card */
.cl-card {
    background: var(--nx-surface);
    border: 1px solid var(--nx-border);
    border-radius: 12px;
    padding: 20px 24px;
    transition: box-shadow .15s;
}
.cl-card:hover { box-shadow: 0 2px 16px rgba(0,0,0,.06); }
.cl-card--latest { border-color: rgba(34,197,94,.35); }

/* Card header */
.cl-card__top { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
.cl-card__title { font-size: 15px; font-weight: 700; color: var(--nx-text); flex: 1; line-height: 1.35; }
.cl-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 700;
    flex-shrink: 0; margin-top: 2px;
}
.cl-badge--latest { background: #dcfce7; color: #15803d; }
.cl-badge--pre    { background: #fef3c7; color: #92400e; }

/* Markdown-like body rendered as notes */
.cl-notes { display: flex; flex-direction: column; gap: 5px; }
.cl-note {
    display: flex; align-items: baseline; gap: 8px;
    font-size: 13px; color: var(--nx-muted); line-height: 1.5;
}
.cl-note__bullet {
    width: 5px; height: 5px; border-radius: 50%;
    background: #22c55e; flex-shrink: 0; margin-top: 7px;
}
.cl-note--improve .cl-note__bullet { background: #3b82f6; }
.cl-note--fix     .cl-note__bullet { background: #f59e0b; }
.cl-note--remove  .cl-note__bullet { background: #ef4444; }

/* Empty body placeholder */
.cl-no-notes { font-size: 12.5px; color: var(--nx-muted); font-style: italic; opacity: .6; }

/* ── "Up to date" banner when current = latest ── */
.cl-uptodate {
    display: flex; align-items: center; gap: 10px;
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
    padding: 12px 16px; margin-bottom: 32px; font-size: 13px; color: #15803d; font-weight: 600;
}
.cl-uptodate svg { flex-shrink: 0; }
</style>

<div class="cl-page">

    {{-- Header --}}
    <div class="cl-head">
        <div class="cl-head__eyebrow">Nexova Desk</div>
        <h1 class="cl-head__title">Novedades de la plataforma</h1>
        <p class="cl-head__sub">Aquí encontrarás todo lo nuevo que va llegando — funciones, mejoras y correcciones.</p>
    </div>

    @if(! $hasReleases)
    {{-- Empty / no repo yet --}}
    <div class="cl-empty">
        <svg class="cl-empty__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        <p class="cl-empty__title">Todo al día</p>
        <p class="cl-empty__sub">Cuando haya nuevas funciones o mejoras aparecerán aquí.<br>Por ahora la plataforma está completamente actualizada.</p>
    </div>

    @else

    {{-- Up to date notice if version matches --}}
    @if($currentVersion && isset($releases[0]) && ltrim($releases[0]['tag'], 'v') === ltrim($currentVersion, 'v'))
    <div class="cl-uptodate">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Estás usando la versión más reciente — {{ $releases[0]['tag'] }}
    </div>
    @endif

    {{-- Timeline --}}
    <div class="cl-timeline">
    @foreach($releases as $i => $release)
    @php
        $lines = array_filter(array_map('trim', explode("\n", $release['body'] ?? '')));
        // Parse markdown bullets: "- text" or "* text"
        $notes = [];
        foreach ($lines as $line) {
            if (preg_match('/^[-*]\s+(.+)/', $line, $m)) {
                $text = $m[1];
                // Classify by keywords
                $type = 'new';
                if (preg_match('/fix|correc|arregl|bug/i', $text))    $type = 'fix';
                elseif (preg_match('/mejor|optim|rendim|velocidad/i', $text)) $type = 'improve';
                elseif (preg_match('/remov|elimin|deprec/i', $text))  $type = 'remove';
                $notes[] = ['text' => $text, 'type' => $type];
            }
        }
    @endphp
    <div class="cl-entry">
        {{-- Left: date + version --}}
        <div class="cl-entry__left">
            <div class="cl-entry__date">{{ $release['date'] }}</div>
            <div class="cl-entry__version">{{ $release['tag'] }}</div>
        </div>

        {{-- Dot --}}
        <span class="cl-dot {{ $release['latest'] ? 'cl-dot--latest' : ($release['prerelease'] ? 'cl-dot--pre' : '') }}"></span>

        {{-- Card --}}
        <div class="cl-card {{ $release['latest'] ? 'cl-card--latest' : '' }}">
            <div class="cl-card__top">
                <div class="cl-card__title">{{ $release['title'] }}</div>
                @if($release['latest'])
                <span class="cl-badge cl-badge--latest">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="9" height="9"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Última versión
                </span>
                @elseif($release['prerelease'])
                <span class="cl-badge cl-badge--pre">Beta</span>
                @endif
            </div>

            @if(count($notes) > 0)
            <div class="cl-notes">
                @foreach($notes as $note)
                <div class="cl-note cl-note--{{ $note['type'] }}">
                    <span class="cl-note__bullet"></span>
                    <span>{{ $note['text'] }}</span>
                </div>
                @endforeach
            </div>
            @elseif(!empty(trim($release['body'] ?? '')))
            <p class="cl-no-notes">{{ Str::limit(strip_tags($release['body']), 200) }}</p>
            @else
            <p class="cl-no-notes">Sin notas de versión.</p>
            @endif
        </div>
    </div>
    @endforeach
    </div>

    @endif

</div>

</x-filament-panels::page>
