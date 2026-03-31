<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $metaTitle = $post->meta_title ? $post->meta_title . ' — Nexova Desk' : $post->title . ' — Nexova Desk';
        $metaDesc  = $post->meta_description ?? $post->excerpt ?? Str::limit(strip_tags($post->bodyHtml()), 160);
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <link rel="canonical" href="{{ url('/novedades/' . $post->slug) }}">
    <meta property="og:type"        content="article">
    <meta property="og:url"         content="{{ url('/novedades/' . $post->slug) }}">
    <meta property="og:title"       content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image"       content="{{ $post->cover_image ?: asset('images/nexovadeskicon.png') }}">
    <meta property="og:locale"      content="es_ES">
    <meta property="og:site_name"   content="Nexova Desk">
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image"       content="{{ $post->cover_image ?: asset('images/nexovadeskicon.png') }}">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": {{ json_encode($post->title) }},
        "description": {{ json_encode($metaDesc) }},
        "datePublished": "{{ $post->published_at?->toIso8601String() }}",
        "image": "{{ $post->cover_image ?: asset('images/nexovadeskicon.png') }}",
        "url": "{{ url('/novedades/' . $post->slug) }}",
        "publisher": {
            "@type": "Organization",
            "name": "Nexova Desk",
            "url": "{{ url('/') }}"
        }
    }
    </script>
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

        .article-wrap { max-width:760px; margin:0 auto; padding:60px 5%; }
        .article-badge { display:inline-flex; padding:4px 12px; border-radius:99px; font-size:12px; font-weight:700; margin-bottom:16px; }
        .article-h1 { font-size:clamp(1.7rem,4vw,2.4rem); font-weight:800; letter-spacing:-.03em; line-height:1.2; margin-bottom:14px; }
        .article-meta { font-size:12px; color:var(--sub); margin-bottom:32px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .article-cover { width:100%; max-height:420px; object-fit:cover; border-radius:14px; margin-bottom:40px; border:1px solid var(--border); }

        /* Markdown body */
        .prose { font-size:15px; line-height:1.8; color:rgba(255,255,255,.82); }
        .prose h1,.prose h2,.prose h3,.prose h4 { color:var(--text); font-weight:700; letter-spacing:-.02em; margin:2em 0 .6em; line-height:1.25; }
        .prose h1 { font-size:1.8em; } .prose h2 { font-size:1.4em; } .prose h3 { font-size:1.15em; }
        .prose p { margin-bottom:1.4em; }
        .prose ul,.prose ol { padding-left:1.5em; margin-bottom:1.4em; }
        .prose li { margin-bottom:.4em; }
        .prose blockquote { border-left:3px solid var(--green); padding-left:16px; color:var(--muted); font-style:italic; margin:1.4em 0; }
        .prose code { background:var(--surf2); border:1px solid var(--border); border-radius:5px; padding:2px 6px; font-size:.88em; font-family:monospace; color:#86efac; }
        .prose pre { background:var(--surf2); border:1px solid var(--border); border-radius:10px; padding:16px; overflow-x:auto; margin:1.4em 0; }
        .prose pre code { background:none; border:none; padding:0; color:var(--text); }
        .prose a { color:var(--green); text-decoration:underline; text-underline-offset:3px; }
        .prose a:hover { color:#16a34a; }
        .prose strong { color:var(--text); font-weight:700; }
        .prose hr { border:none; border-top:1px solid var(--border); margin:2em 0; }
        .prose img { max-width:100%; border-radius:10px; border:1px solid var(--border); margin:1.2em 0; }
        .prose table { width:100%; border-collapse:collapse; margin:1.4em 0; font-size:14px; }
        .prose th { background:var(--surf2); padding:8px 12px; text-align:left; font-weight:700; border:1px solid var(--border); }
        .prose td { padding:8px 12px; border:1px solid var(--border); }

        .back-link { display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--sub); margin-bottom:32px; transition:color .15s; }
        .back-link:hover { color:var(--text); }

        .related-section { margin-top:64px; padding-top:40px; border-top:1px solid var(--border); }
        .related-h { font-size:14px; font-weight:700; color:var(--sub); text-transform:uppercase; letter-spacing:.06em; margin-bottom:20px; }
        .related-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px; }
        .related-card { background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:16px; transition:border-color .2s; display:flex; flex-direction:column; gap:8px; }
        .related-card:hover { border-color:rgba(34,197,94,.22); }
        .related-title { font-size:14px; font-weight:700; line-height:1.3; }
        .related-date { font-size:11px; color:var(--sub); }
        .related-read { color:var(--green); font-size:12px; font-weight:600; }

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
        <img src="{{ asset('images/nexovadesklogo.svg') }}" alt="Nexova Desk"
             style="filter:brightness(0) saturate(100%) invert(58%) sepia(72%) saturate(500%) hue-rotate(95deg) brightness(95%)">
        <span class="nav-brand-name">Nexova Desk</span>
    </a>
    <div style="display:flex;gap:8px;align-items:center">
        <a href="/novedades" class="btn btn-sm btn-ghost">← Novedades</a>
        @auth
        <a href="{{ auth()->user()?->is_super_admin ? '/nx-hq' : '/app' }}" class="btn btn-sm btn-primary">Mi panel</a>
        @else
        <a href="/login" class="btn btn-sm btn-primary">Acceder</a>
        @endauth
    </div>
</nav>

<div class="article-wrap">

    <a href="/novedades" class="back-link">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Todas las novedades
    </a>

    @php $cc = $post->categoryColor(); @endphp
    <span class="article-badge" style="background:{{ $cc['bg'] }};color:{{ $cc['color'] }}">
        {{ $post->categoryLabel() }}
    </span>
    <h1 class="article-h1">{{ $post->title }}</h1>
    <div class="article-meta">
        <span>{{ $post->published_at?->translatedFormat('d \d\e F, Y') }}</span>
        @if($post->excerpt)
        <span style="color:var(--border)">·</span>
        <span>{{ $post->excerpt }}</span>
        @endif
    </div>

    @if($post->cover_image)
    <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="article-cover">
    @endif

    <div class="prose">
        {!! $post->bodyHtml() !!}
    </div>

    {{-- Related posts --}}
    @if($related->isNotEmpty())
    <div class="related-section">
        <div class="related-h">También te puede interesar</div>
        <div class="related-grid">
            @foreach($related as $r)
            @php $rc = $r->categoryColor(); @endphp
            <a href="/novedades/{{ $r->slug }}" class="related-card">
                <span style="display:inline-flex;padding:2px 9px;border-radius:99px;font-size:10px;font-weight:700;width:fit-content;background:{{ $rc['bg'] }};color:{{ $rc['color'] }}">{{ $r->categoryLabel() }}</span>
                <div class="related-title">{{ $r->title }}</div>
                <div class="related-date">{{ $r->published_at?->format('d/m/Y') }}</div>
                <span class="related-read">Leer →</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>

<footer class="footer">
    <div style="display:flex;align-items:center;gap:9px">
        <img src="{{ asset('images/nexovadesklogo.svg') }}" alt="" style="height:24px;width:24px;filter:brightness(0) saturate(100%) invert(58%) sepia(72%) saturate(500%) hue-rotate(95deg) brightness(95%)">
        <span style="font-size:13.5px;font-weight:700">Nexova Desk</span>
    </div>
    <span class="footer-copy">© {{ date('Y') }} Nexova Digital Solutions</span>
    <div class="footer-links">
        <a href="/">Inicio</a>
        <a href="/novedades">Blog</a>
    </div>
</footer>

</body>
</html>
