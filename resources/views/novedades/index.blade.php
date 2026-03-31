<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novedades — Nexova Desk</title>
    <meta name="description" content="Noticias, eventos y actualizaciones de Nexova Desk.">
    <link rel="canonical" href="{{ url('/novedades') }}">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url('/novedades') }}">
    <meta property="og:title"       content="Novedades — Nexova Desk">
    <meta property="og:description" content="Noticias, eventos y actualizaciones de Nexova Desk.">
    <meta property="og:image"       content="{{ asset('images/nexovadeskicon.png') }}">
    <meta name="twitter:card"       content="summary_large_image">
    <meta name="twitter:title"      content="Novedades — Nexova Desk">
    <meta name="twitter:description" content="Noticias, eventos y actualizaciones de Nexova Desk.">
    <meta name="twitter:image"      content="{{ asset('images/nexovadeskicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/nexovadeskicon.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg:#0f1117; --surface:#161b27; --surf2:#1e2636; --border:rgba(255,255,255,.075); --green:#22c55e; --text:#f1f5f9; --sub:#64748b; --muted:rgba(255,255,255,.45); }
        html { scroll-behavior: smooth; }
        body { font-family:'Inter',system-ui,sans-serif; background:var(--bg); color:var(--text); line-height:1.6; overflow-x:hidden; }
        a { color:inherit; text-decoration:none; }
        .nav { position:sticky; top:0; z-index:100; height:56px; background:rgba(22,27,39,.9); backdrop-filter:blur(12px); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 5%; }
        .nav-brand { display:flex; align-items:center; gap:10px; }
        .nav-brand img { height:32px; width:32px; border-radius:6px; }
        .nav-brand-name { font-size:15px; font-weight:700; }
        .btn { display:inline-flex; align-items:center; gap:7px; font-family:inherit; cursor:pointer; border:none; font-weight:600; transition:.15s; text-decoration:none; }
        .btn-sm { padding:7px 14px; border-radius:7px; font-size:13px; }
        .btn-primary { background:var(--green); color:#0d1117; }
        .btn-primary:hover { background:#16a34a; }
        .btn-ghost { background:transparent; border:1px solid var(--border); color:var(--muted); }
        .btn-ghost:hover { border-color:rgba(255,255,255,.18); color:var(--text); }

        .page-wrap { max-width:1100px; margin:0 auto; padding:60px 5%; }
        .page-eyebrow { display:flex; align-items:center; gap:8px; margin-bottom:10px; }
        .page-eyebrow span { width:18px; height:1px; background:var(--green); }
        .page-eyebrow-text { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--green); }
        .page-h1 { font-size:clamp(1.8rem,4vw,2.6rem); font-weight:800; letter-spacing:-.03em; margin-bottom:12px; }
        .page-sub { font-size:14px; color:var(--muted); max-width:480px; margin-bottom:48px; line-height:1.7; }

        .cat-filter { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:40px; }
        .cat-btn { padding:6px 14px; border-radius:99px; font-size:12px; font-weight:700; cursor:pointer; border:1px solid var(--border); background:transparent; color:var(--muted); font-family:inherit; transition:.15s; text-decoration:none; }
        .cat-btn:hover, .cat-btn.active { background:var(--green); color:#0d1117; border-color:var(--green); }

        .posts-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:24px; }
        .post-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; transition:border-color .2s,transform .2s; display:flex; flex-direction:column; }
        .post-card:hover { border-color:rgba(34,197,94,.22); transform:translateY(-2px); }
        .post-cover { width:100%; height:180px; object-fit:cover; background:var(--surf2); display:block; }
        .post-cover-placeholder { width:100%; height:180px; background:var(--surf2); display:flex; align-items:center; justify-content:center; }
        .post-body { padding:20px; display:flex; flex-direction:column; gap:10px; flex:1; }
        .post-badge { display:inline-flex; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:700; width:fit-content; }
        .post-title { font-size:16px; font-weight:700; line-height:1.35; }
        .post-excerpt { font-size:13px; color:var(--muted); line-height:1.65; flex:1; }
        .post-meta { font-size:11px; color:var(--sub); }
        .post-read { color:var(--green); font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:5px; margin-top:auto; }
        .post-read:hover { color:#16a34a; }

        .empty-state { text-align:center; padding:80px 20px; color:var(--sub); }
        .empty-state h3 { font-size:16px; margin-bottom:8px; color:var(--muted); }

        .pagination-wrap { display:flex; justify-content:center; gap:8px; margin-top:48px; flex-wrap:wrap; }
        .pag-btn { padding:7px 14px; border-radius:7px; font-size:13px; font-weight:600; border:1px solid var(--border); background:transparent; color:var(--muted); font-family:inherit; cursor:pointer; text-decoration:none; transition:.15s; }
        .pag-btn:hover { background:var(--surface); color:var(--text); }
        .pag-btn.active { background:var(--green); color:#0d1117; border-color:var(--green); }
        .pag-btn[disabled] { opacity:.35; pointer-events:none; }

        .footer { border-top:1px solid var(--border); padding:28px 5%; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; }
        .footer-copy { font-size:12px; color:var(--sub); }
        .footer-links { display:flex; gap:18px; }
        .footer-links a { font-size:13px; color:var(--sub); transition:color .15s; }
        .footer-links a:hover { color:var(--text); }
    </style>
</head>
<body>

<nav class="nav">
    <a class="nav-brand" href="/">
        <img src="{{ asset('images/nexovadesklogo.png') }}" alt="Nexova Desk"
             style="filter:brightness(0) saturate(100%) invert(58%) sepia(72%) saturate(500%) hue-rotate(95deg) brightness(95%)">
        <span class="nav-brand-name">Nexova Desk</span>
    </a>
    <div style="display:flex;gap:8px;align-items:center">
        <a href="/" class="btn btn-sm btn-ghost">← Inicio</a>
        @auth
        <a href="{{ auth()->user()?->is_super_admin ? '/nx-hq' : '/app' }}" class="btn btn-sm btn-primary">Mi panel</a>
        @else
        <a href="/login" class="btn btn-sm btn-primary">Acceder</a>
        @endauth
    </div>
</nav>

<div class="page-wrap">
    <div class="page-eyebrow"><span></span><span class="page-eyebrow-text">Blog</span></div>
    <h1 class="page-h1">Novedades</h1>
    <p class="page-sub">Actualizaciones, eventos y noticias de Nexova Desk.</p>

    {{-- Category filter --}}
    @php
        $catParam = request('cat', 'all');
        $cats = [
            'all'           => 'Todos',
            'novedad'       => 'Novedades',
            'evento'        => 'Eventos',
            'producto'      => 'Producto',
            'actualizacion' => 'Actualizaciones',
        ];
        $catColors = [
            'novedad'       => ['bg'=>'rgba(30,64,175,.25)','color'=>'#93c5fd'],
            'evento'        => ['bg'=>'rgba(146,64,14,.25)', 'color'=>'#fcd34d'],
            'producto'      => ['bg'=>'rgba(21,128,61,.25)', 'color'=>'#86efac'],
            'actualizacion' => ['bg'=>'rgba(55,65,81,.3)',   'color'=>'#d1d5db'],
        ];
    @endphp
    <div class="cat-filter">
        @foreach($cats as $key => $label)
        <a href="{{ $key === 'all' ? '/novedades' : '/novedades?cat='.$key }}"
           class="cat-btn {{ $catParam === $key ? 'active' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Posts --}}
    @if($posts->isEmpty())
    <div class="empty-state">
        <h3>Sin publicaciones todavía</h3>
        <p>Vuelve pronto para ver novedades.</p>
    </div>
    @else
    <div class="posts-grid">
        @foreach($posts as $post)
        @php $cc = $catColors[$post->category] ?? ['bg'=>'rgba(55,65,81,.3)','color'=>'#d1d5db']; @endphp
        <a href="/novedades/{{ $post->slug }}" class="post-card">
            @if($post->cover_image)
            <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="post-cover">
            @else
            <div class="post-cover-placeholder">
                <svg fill="none" stroke="rgba(255,255,255,.15)" viewBox="0 0 24 24" width="40" height="40"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><path d="M21 15l-5-5L5 21" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            @endif
            <div class="post-body">
                <span class="post-badge" style="background:{{ $cc['bg'] }};color:{{ $cc['color'] }}">
                    {{ $post->categoryLabel() }}
                </span>
                <div class="post-title">{{ $post->title }}</div>
                @if($post->excerpt)
                <p class="post-excerpt">{{ Str::limit($post->excerpt, 120) }}</p>
                @endif
                <div class="post-meta">{{ $post->published_at?->translatedFormat('d \d\e F, Y') ?? '' }}</div>
                <span class="post-read">
                    Leer más
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </span>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($posts->hasPages())
    <div class="pagination-wrap">
        @if($posts->onFirstPage())
        <span class="pag-btn" disabled>← Anterior</span>
        @else
        <a href="{{ $posts->previousPageUrl() }}" class="pag-btn">← Anterior</a>
        @endif

        @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="pag-btn {{ $page === $posts->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if($posts->hasMorePages())
        <a href="{{ $posts->nextPageUrl() }}" class="pag-btn">Siguiente →</a>
        @else
        <span class="pag-btn" disabled>Siguiente →</span>
        @endif
    </div>
    @endif
    @endif
</div>

<footer class="footer">
    <div style="display:flex;align-items:center;gap:9px">
        <img src="{{ asset('images/nexovadesklogo.png') }}" alt="" style="height:24px;width:24px;filter:brightness(0) saturate(100%) invert(58%) sepia(72%) saturate(500%) hue-rotate(95deg) brightness(95%)">
        <span style="font-size:13.5px;font-weight:700">Nexova Desk</span>
    </div>
    <span class="footer-copy">© {{ date('Y') }} Nexova Digital Solutions</span>
    <div class="footer-links">
        <a href="/">Inicio</a>
        <a href="/#pricing">Precios</a>
        <a href="/novedades">Blog</a>
    </div>
</footer>

</body>
</html>
