<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexova Desk — Soporte en vivo para tu sitio web</title>
    <meta name="description" content="Chat en vivo, bot automático y base de conocimiento para atender a tus clientes desde un panel unificado.">

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url('/') }}">
    <meta property="og:title"       content="Nexova Desk — Soporte en vivo para tu sitio web">
    <meta property="og:description" content="Chat en vivo, bot automático y base de conocimiento para atender a tus clientes desde un panel unificado.">
    <meta property="og:image"       content="{{ asset('images/nexovadeskicon.png') }}">
    <meta property="og:locale"      content="es_ES">
    <meta property="og:site_name"   content="Nexova Desk">

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="Nexova Desk — Soporte en vivo para tu sitio web">
    <meta name="twitter:description" content="Chat en vivo, bot automático y base de conocimiento para atender a tus clientes desde un panel unificado.">
    <meta name="twitter:image"       content="{{ asset('images/nexovadeskicon.png') }}">

    {{-- JSON-LD structured data --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "SoftwareApplication",
        "name": "Nexova Desk",
        "description": "Chat en vivo, bot automático y base de conocimiento para atender a tus clientes desde un panel unificado.",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "url": "{{ url('/') }}",
        "offers": {
            "@@type": "Offer",
            "price": "0",
            "priceCurrency": "USD",
            "description": "Plan gratuito disponible"
        }
    }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/nexovadeskicon.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:      #0f1117;
            --surface: #161b27;
            --surf2:   #1e2636;
            --border:  rgba(255,255,255,.075);
            --green:   #22c55e;
            --green-d: #16a34a;
            --text:    #f1f5f9;
            --sub:     #64748b;
            --muted:   rgba(255,255,255,.45);
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; overflow-x: hidden; }
        a { color: inherit; text-decoration: none; }

        /* ── NAV ── */
        .nav {
            position: sticky; top: 0; z-index: 100;
            height: 56px;
            background: rgba(22,27,39,.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 5%;
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; }
        .nav-brand img { height: 36px; width: 36px; border-radius: 7px; object-fit: contain; }
        .nav-brand-name { font-size: 15px; font-weight: 700; }
        .nav-links { display: flex; gap: 24px; }
        .nav-links a { font-size: 13.5px; color: var(--muted); transition: color .15s; }
        .nav-links a:hover { color: var(--text); }
        .nav-actions { display: flex; gap: 8px; align-items: center; }
        @media(max-width:640px){ .nav-links { display:none; } }

        .btn { display:inline-flex; align-items:center; gap:7px; font-family:inherit; cursor:pointer; border:none; font-weight:600; transition:.15s; text-decoration:none; }
        .btn-sm  { padding:7px 14px; border-radius:7px; font-size:13px; }
        .btn-md  { padding:9px 20px; border-radius:8px; font-size:13.5px; }
        .btn-lg  { padding:12px 24px; border-radius:9px; font-size:14px; font-weight:700; }
        .btn-ghost  { background:transparent; border:1px solid var(--border); color:var(--muted); }
        .btn-ghost:hover { border-color:rgba(255,255,255,.18); color:var(--text); }
        .btn-primary { background:var(--green); color:#0d1117; }
        .btn-primary:hover { background:var(--green-d); }
        .btn-dark    { background:var(--surface); border:1px solid var(--border); color:var(--text); }
        .btn-dark:hover { background:var(--surf2); }

        /* ── HERO ── */
        .hero {
            padding: 80px 5% 60px;
            display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
            max-width: 1100px; margin: 0 auto;
        }
        @media(max-width:768px){ .hero { grid-template-columns:1fr; gap:40px; text-align:center; } }
        .hero-eyebrow {
            display:inline-flex; align-items:center; gap:7px;
            font-size:12px; font-weight:700; letter-spacing:.06em; text-transform:uppercase;
            color:var(--green); margin-bottom:16px;
        }
        .hero-eyebrow span { width:18px; height:1px; background:var(--green); display:inline-block; }
        .hero-h1 {
            font-size: clamp(1.9rem,4vw,2.8rem); font-weight:800;
            letter-spacing:-.03em; line-height:1.15; margin-bottom:18px;
        }
        .hero-h1 em { color:var(--green); font-style:normal; }
        .hero-desc { font-size:15px; color:var(--muted); line-height:1.75; margin-bottom:28px; max-width:440px; }
        @media(max-width:768px){ .hero-desc { margin-inline:auto; } }
        .hero-actions { display:flex; gap:10px; flex-wrap:wrap; }
        @media(max-width:768px){ .hero-actions { justify-content:center; } }
        .hero-sub { font-size:12px; color:var(--sub); margin-top:14px; }

        /* ── WIDGET MOCKUP ── */
        .mockup-wrap { position:relative; }
        .mockup-widget {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0,0,0,.5);
            font-size: 13px;
        }
        .mock-header {
            background: var(--green);
            padding: 14px 16px;
            display: flex; align-items: center; gap: 10px;
        }
        .mock-avatar { width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; }
        .mock-header-text .mock-name { font-weight:700; font-size:13px; color:#0d1117; }
        .mock-header-text .mock-status { font-size:11px; color:rgba(0,0,0,.55); }
        .mock-msgs { padding:14px 16px; display:flex; flex-direction:column; gap:10px; background:var(--bg); min-height:160px; }
        .mock-msg-bot, .mock-msg-user { display:flex; gap:8px; align-items:flex-end; }
        .mock-msg-user { flex-direction:row-reverse; }
        .mock-bubble {
            padding:9px 12px; border-radius:12px; font-size:12.5px; line-height:1.5; max-width:75%;
        }
        .mock-bubble-bot  { background:var(--surface); color:var(--text); border-radius:2px 12px 12px 12px; }
        .mock-bubble-user { background:var(--green); color:#0d1117; border-radius:12px 12px 2px 12px; }
        .mock-dot { width:26px; height:26px; border-radius:50%; background:var(--green); flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; color:#0d1117; }
        .mock-input {
            padding:10px 14px; border-top:1px solid var(--border);
            display:flex; gap:8px; align-items:center; background:var(--surface);
        }
        .mock-input-field { flex:1; height:32px; background:var(--surf2); border-radius:8px; border:1px solid var(--border); }
        .mock-send { width:32px; height:32px; border-radius:8px; background:var(--green); display:flex; align-items:center; justify-content:center; }

        /* ── INBOX STRIP ── */
        .mockup-inbox {
            position:absolute; top:-20px; right:-30px;
            background:var(--surface); border:1px solid var(--border); border-radius:12px;
            padding:12px 14px; width:200px;
            box-shadow: 0 8px 32px rgba(0,0,0,.4);
        }
        @media(max-width:768px){ .mockup-inbox { display:none; } }
        .inbox-title { font-size:11px; font-weight:700; color:var(--sub); text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px; }
        .inbox-row { display:flex; align-items:center; gap:8px; padding:6px 0; border-bottom:1px solid var(--border); }
        .inbox-row:last-child { border-bottom:none; }
        .inbox-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
        .inbox-name { font-size:12px; font-weight:600; color:var(--text); flex:1; }
        .inbox-time { font-size:10px; color:var(--sub); }

        /* ── SECTIONS ── */
        .section { padding:70px 5%; max-width:1100px; margin:0 auto; }
        .section-sep { border:none; border-top:1px solid var(--border); margin:0; }

        .label-row { display:flex; align-items:center; gap:8px; margin-bottom:10px; }
        .label-line { width:18px; height:1px; background:var(--green); }
        .label-text { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--green); }
        .section-h2 { font-size:clamp(1.5rem,3vw,2rem); font-weight:800; letter-spacing:-.025em; margin-bottom:12px; }
        .section-sub { font-size:14px; color:var(--muted); line-height:1.75; max-width:480px; }

        /* ── FEATURES ── */
        .feat-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:16px; margin-top:44px; }
        .feat-card {
            background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:24px;
            transition:border-color .2s, transform .2s;
        }
        .feat-card:hover { border-color:rgba(34,197,94,.22); transform:translateY(-2px); }
        .feat-icon {
            width:40px; height:40px; border-radius:9px;
            background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.18);
            display:flex; align-items:center; justify-content:center; color:var(--green); margin-bottom:16px;
        }
        .feat-title { font-size:14px; font-weight:700; margin-bottom:7px; }
        .feat-desc  { font-size:13px; color:var(--muted); line-height:1.65; }

        /* ── STEPS ── */
        .steps-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:24px; margin-top:44px; }
        .step-card { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:24px; }
        .step-num { font-size:11px; font-weight:800; color:var(--green); letter-spacing:.06em; text-transform:uppercase; margin-bottom:12px; }
        .step-title { font-size:14.5px; font-weight:700; margin-bottom:7px; }
        .step-desc  { font-size:13px; color:var(--muted); line-height:1.65; }

        /* ── PRICING ── */
        .pricing-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:20px; max-width:620px; margin:44px auto 0; }
        .plan-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:28px; position:relative; }
        .plan-card.featured { border-color:rgba(34,197,94,.35); }
        .plan-badge {
            position:absolute; top:-12px; left:50%; transform:translateX(-50%);
            background:var(--green); color:#0d1117; font-size:11px; font-weight:700;
            padding:3px 14px; border-radius:99px; white-space:nowrap;
        }
        .plan-name  { font-size:12px; font-weight:700; color:var(--sub); text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px; }
        .plan-price { font-size:2.2rem; font-weight:800; letter-spacing:-.03em; line-height:1; margin-bottom:6px; }
        .plan-price span { font-size:14px; font-weight:500; color:var(--sub); }
        .plan-desc  { font-size:13px; color:var(--muted); margin-bottom:22px; line-height:1.5; }
        .plan-feats { list-style:none; display:flex; flex-direction:column; gap:9px; margin-bottom:24px; }
        .plan-feats li { display:flex; align-items:center; gap:9px; font-size:13px; color:rgba(255,255,255,.7); }
        .plan-feats li.off { color:var(--sub); }
        .plan-feats svg { flex-shrink:0; }

        /* ── FAQ ── */
        .faq-list { max-width:680px; margin:44px auto 0; }
        .faq-item { border-bottom:1px solid var(--border); }
        .faq-q { display:flex; justify-content:space-between; align-items:center; padding:18px 0; cursor:pointer; font-size:14px; font-weight:600; gap:16px; }
        .faq-q:hover { color:var(--green); }
        .faq-a { font-size:13px; color:var(--muted); line-height:1.7; padding-bottom:16px; display:none; }
        .faq-item.open .faq-a { display:block; }
        .faq-item.open .faq-arrow { transform:rotate(180deg); }
        .faq-arrow { transition:transform .2s; flex-shrink:0; color:var(--sub); }

        /* ── CTA ── */
        .cta-section {
            border-top:1px solid var(--border);
            padding: 80px 5%; text-align:center;
            background:radial-gradient(ellipse at center, rgba(34,197,94,.06) 0%, transparent 70%);
        }
        .cta-h2 { font-size:clamp(1.5rem,3vw,2rem); font-weight:800; letter-spacing:-.025em; margin-bottom:14px; }
        .cta-sub { font-size:14px; color:var(--muted); margin-bottom:32px; line-height:1.7; }

        /* ── FOOTER ── */
        .footer { border-top:1px solid var(--border); padding:32px 5%; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; }
        .footer-brand { display:flex; align-items:center; gap:9px; }
        .footer-brand img { height:28px; width:28px; border-radius:6px; object-fit:contain; }
        .footer-brand-name { font-size:13.5px; font-weight:700; }
        .footer-copy { font-size:12px; color:var(--sub); }
        .footer-links { display:flex; gap:18px; }
        .footer-links a { font-size:13px; color:var(--sub); transition:color .15s; }
        .footer-links a:hover { color:var(--text); }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
    <div class="nav-brand">
        <img src="{{ asset('images/nexovadesklogo.png') }}" alt="Nexova Desk"
             style="filter:brightness(0) saturate(100%) invert(58%) sepia(72%) saturate(500%) hue-rotate(95deg) brightness(95%)">
        <span class="nav-brand-name">Nexova Desk</span>
    </div>
    <div class="nav-links">
        <a href="#features">Funciones</a>
        <a href="#pricing">Precios</a>
        <a href="#faq">FAQ</a>
        <a href="/novedades">Blog</a>
    </div>
    <div class="nav-actions">
        @auth
            <a href="{{ auth()->user()?->is_super_admin ? '/nx-hq' : '/app' }}"
               class="btn btn-sm btn-primary" style="display:inline-flex;align-items:center;gap:7px">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Mi panel
            </a>
        @else
            <a href="/login"    class="btn btn-sm btn-ghost">Iniciar sesión</a>
            <a href="/register" class="btn btn-sm btn-primary">Probar gratis</a>
        @endauth
    </div>
</nav>

<!-- HERO -->
<div class="hero">
    <div>
        <div class="hero-eyebrow"><span></span>Chat en vivo · Bot automático · Agentes</div>
        <h1 class="hero-h1">Soporte para tu web,<br><em>sin perder ninguna</em><br>conversación</h1>
        <p class="hero-desc">
            Un widget de chat que responde solo. Cuando el bot no puede, escala a tu equipo. Todo en un panel unificado.
        </p>
        <div class="hero-actions">
            @auth
                <a href="{{ auth()->user()?->is_super_admin ? '/nx-hq' : '/app' }}" class="btn btn-lg btn-primary">
                    Ir a mi panel
                </a>
            @else
                <a href="/register" class="btn btn-lg btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Empezar gratis
                </a>
                <a href="/login" class="btn btn-lg btn-dark">Iniciar sesión</a>
            @endauth
        </div>
        @guest
        <p class="hero-sub">Sin tarjeta de crédito · Configuración en 5 minutos</p>
        @endguest
    </div>

    <!-- Widget mockup -->
    <div class="mockup-wrap">
        <div class="mockup-widget">
            <div class="mock-header">
                <div class="mock-avatar">
                    <svg fill="none" stroke="#0d1117" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <div class="mock-header-text">
                    <div class="mock-name">Asistente Nexova</div>
                    <div class="mock-status">● En línea ahora</div>
                </div>
            </div>
            <div class="mock-msgs">
                <div class="mock-msg-bot">
                    <div class="mock-dot">N</div>
                    <div class="mock-bubble mock-bubble-bot">¡Hola! ¿En qué puedo ayudarte hoy?</div>
                </div>
                <div class="mock-msg-user">
                    <div class="mock-bubble mock-bubble-user">¿Cuál es el tiempo de envío?</div>
                </div>
                <div class="mock-msg-bot">
                    <div class="mock-dot">N</div>
                    <div class="mock-bubble mock-bubble-bot">Los envíos nacionales tardan 2-3 días hábiles. ¿Necesitas más información?</div>
                </div>
            </div>
            <div class="mock-input">
                <div class="mock-input-field"></div>
                <div class="mock-send">
                    <svg fill="none" stroke="#0d1117" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
            </div>
        </div>

        <!-- Inbox floating card -->
        <div class="mockup-inbox">
            <div class="inbox-title">Inbox</div>
            <div class="inbox-row">
                <div class="inbox-dot" style="background:#22c55e"></div>
                <div class="inbox-name">María López</div>
                <div class="inbox-time">ahora</div>
            </div>
            <div class="inbox-row">
                <div class="inbox-dot" style="background:#f59e0b"></div>
                <div class="inbox-name">Carlos Ruiz</div>
                <div class="inbox-time">2m</div>
            </div>
            <div class="inbox-row">
                <div class="inbox-dot" style="background:var(--sub)"></div>
                <div class="inbox-name">Ana García</div>
                <div class="inbox-time">18m</div>
            </div>
        </div>
    </div>
</div>

<hr class="section-sep">

<!-- FEATURES -->
<section class="section" id="features">
    <div class="label-row"><span class="label-line"></span><span class="label-text">Funciones</span></div>
    <h2 class="section-h2">Todo lo que necesita tu equipo de soporte</h2>
    <p class="section-sub">Desde el bot hasta el historial de conversaciones, en un solo lugar.</p>

    <div class="feat-grid">
        <div class="feat-card">
            <div class="feat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div class="feat-title">Bot automático</div>
            <div class="feat-desc">Responde preguntas comunes usando tu base de conocimiento. Cuando no sabe, escala a un agente real.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div class="feat-title">Widget personalizable</div>
            <div class="feat-desc">Colores, posición, horario de atención, formulario previo y mensajes de bienvenida configurables desde el panel.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="feat-title">Equipo de agentes</div>
            <div class="feat-desc">Invita a tu equipo. Cada agente ve las conversaciones asignadas y puede responder en tiempo real desde el panel.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="feat-title">Base de conocimiento</div>
            <div class="feat-desc">Agrega artículos de soporte o importa contenido desde URLs. El bot los usa para responder sin depender de la IA general.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </div>
            <div class="feat-title">Telegram conectado</div>
            <div class="feat-desc">Conecta un bot de Telegram y atiende sus conversaciones desde el mismo panel que el chat web. Un inbox unificado.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div class="feat-title">Dashboard y métricas</div>
            <div class="feat-desc">Tickets abiertos, calificaciones, tiempos de respuesta y actividad del bot en tiempo real desde el panel principal.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="feat-title">Integración WooCommerce</div>
            <div class="feat-desc">Plugin oficial para WordPress. El bot conoce tu catálogo, precios y stock automáticamente. Sin configuración manual.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
            <div class="feat-title">Adjuntos y archivos</div>
            <div class="feat-desc">Los clientes y agentes pueden compartir imágenes y PDF directamente en el chat, con soporte para pegar con Ctrl+V.</div>
        </div>
    </div>
</section>

<hr class="section-sep">

<!-- HOW IT WORKS -->
<section class="section">
    <div class="label-row"><span class="label-line"></span><span class="label-text">Inicio rápido</span></div>
    <h2 class="section-h2">Desde cero a tu primer chat en minutos</h2>
    <p class="section-sub">Sin instalar nada complejo. Dos líneas de código y listo.</p>

    <div class="steps-grid">
        <div class="step-card">
            <div class="step-num">Paso 01</div>
            <div class="step-title">Crea tu cuenta y configura el widget</div>
            <div class="step-desc">Personaliza nombre del bot, colores, horario y el mensaje de bienvenida. Copia el script en tu HTML.</div>
        </div>
        <div class="step-card">
            <div class="step-num">Paso 02</div>
            <div class="step-title">Agrega tu base de conocimiento</div>
            <div class="step-desc">Escribe artículos o importa páginas de tu sitio. El bot las usará para responder antes de llamar a la IA general.</div>
        </div>
        <div class="step-card">
            <div class="step-num">Paso 03</div>
            <div class="step-title">Responde en vivo cuando sea necesario</div>
            <div class="step-desc">Recibirás una notificación cuando un cliente pida hablar con un agente. Toma el control desde el panel.</div>
        </div>
    </div>
</section>

<hr class="section-sep">

<!-- PRICING -->
<section class="section" id="pricing" style="text-align:center">
    <div class="label-row" style="justify-content:center"><span class="label-line"></span><span class="label-text">Precios</span></div>
    <h2 class="section-h2">Dos planes, sin sorpresas</h2>
    <p class="section-sub" style="margin-inline:auto">Empieza gratis, escala cuando lo necesites.</p>

    @php
        $landingPlans = isset($plans) ? $plans : collect();
        $checkSvg = '<svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        $xSvg     = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
    @endphp
    <div class="pricing-grid" style="max-width:{{ $landingPlans->count() > 2 ? '900px' : '620px' }}; margin:44px auto 0">
        @forelse($landingPlans as $plan)
        @php
            $isFeatured = ! $plan->isFree();
            $features   = is_array($plan->features) ? $plan->features : [];
            $featLabels = [
                'kb_manual'        => 'Artículos manuales en KB',
                'kb_scrape'        => 'Escaneo automático del sitio',
                'kb_wordpress'     => 'Integración WooCommerce',
                'ai_enabled'       => 'IA generativa activada',
                'own_api_keys'     => 'Usa tus propias claves de IA',
                'unlimited_agents' => 'Agentes ilimitados',
                'telegram'         => 'Telegram incluido',
                'woocommerce'      => 'Plugin WooCommerce',
                'priority_support' => 'Soporte prioritario',
            ];
        @endphp
        <div class="plan-card {{ $isFeatured ? 'featured' : '' }}">
            @if($isFeatured)<div class="plan-badge">Recomendado</div>@endif
            <div class="plan-name">{{ $plan->name }}</div>
            <div class="plan-price">
                {{ $plan->isFree() ? '$0' : '$'.number_format($plan->price_usd, 0) }}
                <span>/ mes</span>
            </div>
            <div class="plan-desc">{{ $plan->description ?? ($plan->isFree() ? 'Para explorar la plataforma sin costo.' : 'Para negocios en crecimiento.') }}</div>
            <ul class="plan-feats">
                @if($plan->max_widgets) <li>{!! $checkSvg !!}{{ $plan->max_widgets >= 999 ? 'Widgets ilimitados' : $plan->max_widgets.' widget'.($plan->max_widgets>1?'s':'') }}</li> @endif
                @if($plan->max_agents)  <li>{!! $checkSvg !!}{{ $plan->max_agents >= 999 ? 'Agentes ilimitados' : 'Hasta '.$plan->max_agents.' agentes' }}</li> @endif
                <li>{!! $checkSvg !!}Base de conocimiento</li>
                @if($plan->max_sessions_per_day) <li>{!! $checkSvg !!}{{ $plan->max_sessions_per_day >= 9999 ? 'Sesiones ilimitadas' : number_format($plan->max_sessions_per_day).' sesiones bot/día' }}</li> @endif
                @php $botMsgs = $plan->max_bot_messages_monthly ?? 0; @endphp
                <li>{!! $checkSvg !!}{{ $botMsgs === 0 ? 'Mensajes bot ilimitados' : number_format($botMsgs).' mensajes bot/mes' }}</li>
                @if(! $plan->ai_blocked)
                    <li>{!! $checkSvg !!}IA con Groq + Gemini</li>
                    <li>{!! $checkSvg !!}Telegram incluido</li>
                    <li>{!! $checkSvg !!}Plugin WooCommerce</li>
                @else
                    <li class="off">{!! $xSvg !!}Sin IA generativa</li>
                @endif
                @foreach($features as $feat)
                    <li>{!! $checkSvg !!}{{ $featLabels[$feat] ?? ucfirst(str_replace('_', ' ', $feat)) }}</li>
                @endforeach
            </ul>
            @if($plan->isFree())
                <a href="/register" class="btn btn-md btn-dark" style="width:100%;justify-content:center">Comenzar gratis</a>
            @else
                <a href="/register" class="btn btn-md btn-primary" style="width:100%;justify-content:center">Empezar con {{ $plan->name }}</a>
            @endif
        </div>
        @empty
        {{-- Fallback estático si no hay planes en DB --}}
        <div class="plan-card">
            <div class="plan-name">Free</div>
            <div class="plan-price">$0 <span>/ mes</span></div>
            <div class="plan-desc">Para explorar la plataforma sin costo.</div>
            <ul class="plan-feats">
                <li>{!! $checkSvg !!}1 widget · 3 agentes</li>
                <li>{!! $checkSvg !!}Base de conocimiento</li>
                <li>{!! $checkSvg !!}50 sesiones bot/día</li>
                <li>{!! $checkSvg !!}1.000 mensajes bot/mes</li>
                <li class="off">{!! $xSvg !!}Sin IA generativa</li>
            </ul>
            <a href="/register" class="btn btn-md btn-dark" style="width:100%;justify-content:center">Comenzar gratis</a>
        </div>
        <div class="plan-card featured">
            <div class="plan-badge">Recomendado</div>
            <div class="plan-name">Pro</div>
            <div class="plan-price">$49 <span>/ mes</span></div>
            <div class="plan-desc">Para negocios en crecimiento.</div>
            <ul class="plan-feats">
                <li>{!! $checkSvg !!}Widgets y agentes ilimitados</li>
                <li>{!! $checkSvg !!}IA con Groq + Gemini</li>
                <li>{!! $checkSvg !!}Sesiones ilimitadas</li>
                <li>{!! $checkSvg !!}Mensajes bot ilimitados</li>
                <li>{!! $checkSvg !!}Telegram + WooCommerce</li>
            </ul>
            <a href="/register" class="btn btn-md btn-primary" style="width:100%;justify-content:center">Empezar con Pro</a>
        </div>
        @endforelse
    </div>
</section>

<hr class="section-sep">

<!-- FAQ -->
<section class="section" id="faq">
    <div class="label-row"><span class="label-line"></span><span class="label-text">FAQ</span></div>
    <h2 class="section-h2">Preguntas frecuentes</h2>

    <div class="faq-list">
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">
                ¿Necesito conocimientos técnicos para instalarlo?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">No. Copia dos líneas de código en tu sitio. Si usas WordPress, el plugin lo instala automáticamente.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">
                ¿Cómo aprende el bot a responder mis preguntas?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">Creas artículos en "Base de conocimiento" o importas URLs de tu sitio. El bot busca en esa información antes de usar la IA general.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">
                ¿Qué pasa cuando el bot no sabe responder?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">Puede escalar la conversación a un agente. El cliente también puede pedirlo en cualquier momento. Recibirás una notificación inmediata.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">
                ¿Qué métodos de pago aceptan?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">MercadoPago (tarjeta, PSE, Nequi, Efecty) y criptomonedas USDT/USDC en redes Tron, BNB Chain y Polygon. Los pagos cripto se verifican automáticamente.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">
                ¿Puedo cancelar cuando quiera?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">Sí. Sin contratos ni penalizaciones. Al cancelar, seguirás con acceso Pro hasta el vencimiento. Luego pasa al plan Free automáticamente.</div>
        </div>
    </div>
</section>

{{-- NOVEDADES (solo si hay posts publicados) --}}
@if(isset($latestPosts) && $latestPosts->isNotEmpty())
<hr class="section-sep">
<section class="section" id="novedades">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:40px">
        <div>
            <div class="label-row"><span class="label-line"></span><span class="label-text">Blog</span></div>
            <h2 class="section-h2" style="margin-bottom:0">Novedades</h2>
        </div>
        <a href="/novedades" style="font-size:13px;color:var(--green);font-weight:600;display:inline-flex;align-items:center;gap:5px">
            Ver todas
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    @php
        $landingCatColors = [
            'novedad'       => ['bg'=>'rgba(30,64,175,.25)','color'=>'#93c5fd'],
            'evento'        => ['bg'=>'rgba(146,64,14,.25)', 'color'=>'#fcd34d'],
            'producto'      => ['bg'=>'rgba(21,128,61,.25)', 'color'=>'#86efac'],
            'actualizacion' => ['bg'=>'rgba(55,65,81,.3)',   'color'=>'#d1d5db'],
        ];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px">
        @foreach($latestPosts as $post)
        @php $cc = $landingCatColors[$post->category] ?? ['bg'=>'rgba(55,65,81,.3)','color'=>'#d1d5db']; @endphp
        <a href="/novedades/{{ $post->slug }}"
           style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,transform .2s"
           onmouseover="this.style.borderColor='rgba(34,197,94,.22)';this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='rgba(255,255,255,.075)';this.style.transform='translateY(0)'">
            @if($post->cover_image)
            <img src="{{ $post->cover_image }}" alt="{{ $post->title }}"
                 style="width:100%;height:160px;object-fit:cover">
            @else
            <div style="width:100%;height:160px;background:var(--surf2);display:flex;align-items:center;justify-content:center">
                <svg fill="none" stroke="rgba(255,255,255,.12)" viewBox="0 0 24 24" width="36" height="36"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><path d="M21 15l-5-5L5 21" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            @endif
            <div style="padding:18px;display:flex;flex-direction:column;gap:9px;flex:1">
                <span style="display:inline-flex;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;width:fit-content;background:{{ $cc['bg'] }};color:{{ $cc['color'] }}">
                    {{ $post->categoryLabel() }}
                </span>
                <div style="font-size:15px;font-weight:700;line-height:1.35;color:var(--text)">{{ $post->title }}</div>
                @if($post->excerpt)
                <p style="font-size:13px;color:var(--muted);line-height:1.6;margin:0">{{ Str::limit($post->excerpt, 100) }}</p>
                @endif
                <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between">
                    <span style="font-size:11px;color:var(--sub)">{{ $post->published_at?->format('d/m/Y') }}</span>
                    <span style="font-size:12px;font-weight:600;color:var(--green);display:inline-flex;align-items:center;gap:4px">
                        Leer
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

<!-- CTA FINAL -->
<section class="cta-section">
    @auth
        <h2 class="cta-h2">Bienvenido de nuevo, {{ auth()->user()->name }}</h2>
        <p class="cta-sub">Tu panel está listo. Gestiona conversaciones, widgets y agentes.</p>
        <a href="{{ auth()->user()?->is_super_admin ? '/nx-hq' : '/app' }}" class="btn btn-lg btn-primary" style="display:inline-flex;margin-inline:auto">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Ir a mi panel
        </a>
    @else
        <h2 class="cta-h2">Empieza gratis hoy</h2>
        <p class="cta-sub">Configura tu primer widget en menos de 5 minutos.</p>
        <a href="/register" class="btn btn-lg btn-primary" style="display:inline-flex;margin-inline:auto">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Crear cuenta gratis
        </a>
    @endauth
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-brand">
        <img src="{{ asset('images/nexovadesklogo.png') }}" alt="Nexova Desk"
             style="filter:brightness(0) saturate(100%) invert(58%) sepia(72%) saturate(500%) hue-rotate(95deg) brightness(95%)">
        <span class="footer-brand-name">Nexova Desk</span>
    </div>
    <div class="footer-copy">© {{ date('Y') }} Nexova Digital Solutions. Todos los derechos reservados.</div>
    <div class="footer-links">
        <a href="/p/terminos">Términos</a>
        <a href="/p/privacidad">Privacidad</a>
        <a href="/novedades">Blog</a>
        @auth
            <a href="{{ auth()->user()?->is_super_admin ? '/nx-hq' : '/app' }}">Mi cuenta</a>
        @else
            <a href="/login">Iniciar sesión</a>
        @endauth
    </div>
</footer>

<script>
function toggleFaq(el) {
    const item = el.closest('.faq-item');
    const open = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    if (!open) item.classList.add('open');
}
</script>
</body>
</html>
