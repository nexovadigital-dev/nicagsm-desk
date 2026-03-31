<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $metaTitle = $page->meta_title ?? ($page->title . ' — Nexova Desk');
        $metaDesc  = $page->meta_description ?? '';
    @endphp
    <title>{{ $metaTitle }}</title>
    @if($metaDesc)<meta name="description" content="{{ $metaDesc }}">@endif
    <link rel="canonical" href="{{ url('/p/' . $page->slug) }}">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url('/p/' . $page->slug) }}">
    <meta property="og:title"       content="{{ $metaTitle }}">
    @if($metaDesc)<meta property="og:description" content="{{ $metaDesc }}">@endif
    <meta property="og:image"       content="{{ asset('images/nexovadeskicon.png') }}">
    <meta name="twitter:card"       content="summary">
    <meta name="twitter:title"      content="{{ $metaTitle }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/nexovadeskicon.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg:#0f1117; --surface:#161b27; --surf2:#1e2636; --border:rgba(255,255,255,.075); --green:#22c55e; --text:#f1f5f9; --sub:#64748b; }
        html { scroll-behavior: smooth; }
        body { font-family:'Inter',system-ui,sans-serif; background:var(--bg); color:var(--text); line-height:1.7; overflow-x:hidden; }
        a { color:var(--green); text-decoration:none; }
        a:hover { text-decoration:underline; }

        .nav { position:sticky; top:0; z-index:100; height:56px; background:rgba(22,27,39,.9); backdrop-filter:blur(12px); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 5%; }
        .nav-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .nav-brand img { height:30px; width:30px; border-radius:6px; }
        .nav-brand-name { font-size:15px; font-weight:700; color:var(--text); }

        .page-wrap { max-width:760px; margin:0 auto; padding:56px 24px 96px; }

        .page-header { margin-bottom:40px; padding-bottom:28px; border-bottom:1px solid var(--border); }
        .page-title { font-size:clamp(24px,4vw,36px); font-weight:800; color:#fff; line-height:1.2; margin-bottom:12px; }
        .page-meta { font-size:12.5px; color:var(--sub); }

        /* Markdown content styles */
        .page-content { color:#cbd5e1; }
        .page-content h1,.page-content h2,.page-content h3,.page-content h4 { color:#f1f5f9; font-weight:700; line-height:1.3; margin:32px 0 14px; }
        .page-content h1 { font-size:28px; }
        .page-content h2 { font-size:22px; border-bottom:1px solid var(--border); padding-bottom:8px; }
        .page-content h3 { font-size:18px; }
        .page-content h4 { font-size:15px; }
        .page-content p { margin:0 0 18px; }
        .page-content ul,.page-content ol { margin:0 0 18px 24px; }
        .page-content li { margin-bottom:6px; }
        .page-content strong { color:#f1f5f9; font-weight:700; }
        .page-content em { color:#e2e8f0; }
        .page-content code { background:rgba(255,255,255,.06); border:1px solid var(--border); border-radius:4px; padding:2px 6px; font-size:.9em; font-family:'Courier New',monospace; color:#86efac; }
        .page-content pre { background:rgba(0,0,0,.3); border:1px solid var(--border); border-radius:8px; padding:16px 20px; overflow-x:auto; margin:0 0 18px; }
        .page-content pre code { background:none; border:none; padding:0; color:#86efac; }
        .page-content blockquote { border-left:3px solid var(--green); padding-left:16px; margin:0 0 18px; color:#94a3b8; font-style:italic; }
        .page-content table { width:100%; border-collapse:collapse; margin-bottom:18px; font-size:13.5px; }
        .page-content th { background:rgba(255,255,255,.04); border:1px solid var(--border); padding:8px 12px; text-align:left; font-weight:700; color:#f1f5f9; }
        .page-content td { border:1px solid var(--border); padding:8px 12px; }
        .page-content hr { border:none; border-top:1px solid var(--border); margin:32px 0; }
        .page-content a { color:var(--green); }
        .page-content img { max-width:100%; border-radius:8px; margin:16px 0; }

        footer { border-top:1px solid var(--border); padding:28px 5%; display:flex; align-items:center; justify-content:space-between; font-size:12.5px; color:var(--sub); flex-wrap:wrap; gap:12px; }
    </style>
</head>
<body>

<nav class="nav">
    <a href="/" class="nav-brand" style="text-decoration:none">
        <img src="{{ asset('images/nexovadeskicon.png') }}" alt="Nexova Desk">
        <span class="nav-brand-name">Nexova Desk</span>
    </a>
    <a href="/" style="font-size:13px;color:var(--sub);text-decoration:none">← Inicio</a>
</nav>

<div class="page-wrap">
    <div class="page-header">
        <h1 class="page-title">{{ $page->title }}</h1>
        <div class="page-meta">Actualizado {{ $page->updated_at->format('d/m/Y') }}</div>
    </div>

    <div class="page-content">
        {!! $page->contentHtml() !!}
    </div>
</div>

<footer>
    <span>© {{ date('Y') }} Nexova Desk</span>
    <div style="display:flex;gap:16px">
        <a href="/">Inicio</a>
        <a href="/novedades">Novedades</a>
    </div>
</footer>

</body>
</html>
