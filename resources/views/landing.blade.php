<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexova Desk — Soporte en vivo con IA para tu negocio</title>
    <meta name="description" content="Chat en vivo, bot automático con IA y panel de agentes para atender a tus clientes 24/7 desde un solo lugar.">
    <link rel="canonical" href="{{ url('/') }}">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url('/') }}">
    <meta property="og:title"       content="Nexova Desk — Soporte en vivo con IA">
    <meta property="og:description" content="Chat en vivo, bot automático con IA y panel de agentes para atender a tus clientes 24/7.">
    <meta property="og:image"       content="{{ asset('images/nexovadeskicon.png') }}">
    <meta property="og:locale"      content="es_CO">
    <meta property="og:site_name"   content="Nexova Desk">
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="Nexova Desk — Soporte en vivo con IA">
    <meta name="twitter:description" content="Chat en vivo, bot automático con IA y panel de agentes para atender a tus clientes 24/7.">
    <meta name="twitter:image"       content="{{ asset('images/nexovadeskicon.png') }}">
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "SoftwareApplication",
        "name": "Nexova Desk",
        "description": "Chat en vivo, bot automático con IA y panel de agentes.",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "url": "{{ url('/') }}",
        "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "USD" }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/nexovadeskicon.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:#0f1117; --surface:#161b27; --surf2:#1e2636; --surf3:#252e42;
            --border:rgba(255,255,255,.075); --border2:rgba(255,255,255,.11);
            --green:#22c55e; --green-d:#16a34a; --green-glow:rgba(34,197,94,.12);
            --text:#f1f5f9; --sub:#64748b; --muted:rgba(255,255,255,.45);
        }
        html { scroll-behavior: smooth; }
        body { font-family:'Inter',system-ui,sans-serif; background:var(--bg); color:var(--text); line-height:1.6; overflow-x:hidden; }
        a { color:inherit; text-decoration:none; }

        /* ── NAV ── */
        .nav {
            position:sticky; top:0; z-index:200;
            height:56px; background:rgba(15,17,23,.92); backdrop-filter:blur(14px);
            border-bottom:1px solid var(--border); display:flex; align-items:center;
            justify-content:space-between; padding:0 5%;
        }
        .nav-brand { display:flex; align-items:center; gap:10px; }
        .nav-brand img { height:34px; width:34px; border-radius:7px; object-fit:contain; }
        .nav-brand-name { font-size:15px; font-weight:700; }
        .nav-links { display:flex; gap:24px; }
        .nav-links a { font-size:13.5px; color:var(--muted); transition:color .15s; }
        .nav-links a:hover { color:var(--text); }
        .nav-actions { display:flex; gap:8px; align-items:center; }
        .nav-hamburger { display:none; background:none; border:none; color:var(--text); cursor:pointer; padding:4px; }
        @media(max-width:700px) {
            .nav-links { display:none; }
            .nav-hamburger { display:flex; }
        }
        /* Mobile menu */
        .nav-mobile { display:none; position:fixed; top:56px; left:0; right:0; background:rgba(15,17,23,.98);
            backdrop-filter:blur(14px); border-bottom:1px solid var(--border); padding:16px 5% 20px; z-index:199;
            flex-direction:column; gap:4px; }
        .nav-mobile.open { display:flex; }
        .nav-mobile a { font-size:14px; color:var(--muted); padding:10px 0; border-bottom:1px solid var(--border); }
        .nav-mobile a:last-child { border-bottom:none; }
        .nav-mobile a:hover { color:var(--text); }

        /* ── BUTTONS ── */
        .btn { display:inline-flex; align-items:center; gap:7px; font-family:inherit; cursor:pointer; border:none; font-weight:600; transition:.15s; text-decoration:none; white-space:nowrap; }
        .btn-sm  { padding:7px 14px; border-radius:7px; font-size:13px; }
        .btn-md  { padding:9px 20px; border-radius:8px; font-size:13.5px; }
        .btn-lg  { padding:13px 26px; border-radius:9px; font-size:14px; font-weight:700; }
        .btn-ghost  { background:transparent; border:1px solid var(--border2); color:var(--muted); }
        .btn-ghost:hover { border-color:rgba(255,255,255,.22); color:var(--text); }
        .btn-primary { background:var(--green); color:#0d1117; }
        .btn-primary:hover { background:var(--green-d); }
        .btn-dark { background:var(--surface); border:1px solid var(--border); color:var(--text); }
        .btn-dark:hover { background:var(--surf2); }

        /* ── HERO ── */
        .hero {
            padding:80px 5% 64px; max-width:1160px; margin:0 auto;
            display:grid; grid-template-columns:1fr 1fr; gap:64px; align-items:center;
        }
        @media(max-width:820px) { .hero { grid-template-columns:1fr; gap:40px; text-align:center; padding:52px 5% 48px; } }
        .hero-eyebrow {
            display:inline-flex; align-items:center; gap:7px; margin-bottom:18px;
            font-size:11.5px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--green);
        }
        .hero-eyebrow span { width:18px; height:1px; background:var(--green); display:inline-block; }
        .hero-h1 { font-size:clamp(2rem,4.2vw,3rem); font-weight:800; letter-spacing:-.03em; line-height:1.12; margin-bottom:20px; }
        .hero-h1 em { color:var(--green); font-style:normal; }
        .hero-desc { font-size:15px; color:var(--muted); line-height:1.8; margin-bottom:30px; max-width:440px; }
        @media(max-width:820px) { .hero-desc { margin-inline:auto; } }
        .hero-actions { display:flex; gap:10px; flex-wrap:wrap; }
        @media(max-width:820px) { .hero-actions { justify-content:center; } }
        .hero-sub { font-size:12px; color:var(--sub); margin-top:14px; }
        .hero-trust { display:flex; align-items:center; gap:10px; margin-top:24px; flex-wrap:wrap; }
        @media(max-width:820px) { .hero-trust { justify-content:center; } }
        .trust-item { display:flex; align-items:center; gap:5px; font-size:12px; color:var(--sub); }
        .trust-dot { width:5px; height:5px; border-radius:50%; background:var(--green); }

        /* ── ANIMATED CHAT MOCKUP ── */
        .chat-demo-wrap { position:relative; }
        .chat-demo {
            background:var(--surface); border:1px solid var(--border2); border-radius:18px;
            overflow:hidden; box-shadow:0 28px 70px rgba(0,0,0,.55), 0 0 0 1px rgba(34,197,94,.06);
            font-size:13px; max-width:360px; margin-inline:auto;
        }
        @media(max-width:820px) { .chat-demo { max-width:340px; } }
        .cd-header {
            background:linear-gradient(135deg,#16a34a,#22c55e);
            padding:14px 16px; display:flex; align-items:center; gap:11px;
        }
        .cd-avatar {
            width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,.2);
            display:flex; align-items:center; justify-content:center; flex-shrink:0;
        }
        .cd-name { font-size:13.5px; font-weight:700; color:#fff; }
        .cd-status { font-size:11px; color:rgba(255,255,255,.8); display:flex; align-items:center; gap:5px; margin-top:1px; }
        .cd-status-dot { width:7px; height:7px; border-radius:50%; background:#4ade80; animation:pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
        .cd-msgs { padding:16px; display:flex; flex-direction:column; gap:10px; min-height:220px; background:var(--bg); }
        .cd-msg-bot, .cd-msg-user { display:flex; gap:8px; align-items:flex-end; }
        .cd-msg-user { flex-direction:row-reverse; }
        .cd-bubble {
            padding:9px 13px; border-radius:14px; font-size:12.5px; line-height:1.55; max-width:78%;
            animation:fadeUp .3s ease;
        }
        @keyframes fadeUp { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .cd-bubble-bot  { background:var(--surface); border:1px solid var(--border); color:var(--text); border-radius:4px 14px 14px 14px; }
        .cd-bubble-user { background:var(--green); color:#0d1117; font-weight:500; border-radius:14px 14px 4px 14px; }
        .cd-dot { width:28px; height:28px; border-radius:50%; background:var(--green); flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color:#0d1117; }
        .cd-typing { display:flex; align-items:center; gap:4px; padding:10px 13px; background:var(--surface); border:1px solid var(--border); border-radius:4px 14px 14px 14px; width:fit-content; }
        .cd-typing span { width:6px; height:6px; border-radius:50%; background:var(--sub); display:inline-block; }
        .cd-typing span:nth-child(1) { animation:typing .9s infinite .0s; }
        .cd-typing span:nth-child(2) { animation:typing .9s infinite .2s; }
        .cd-typing span:nth-child(3) { animation:typing .9s infinite .4s; }
        @keyframes typing { 0%,80%,100%{transform:translateY(0);opacity:.4} 40%{transform:translateY(-5px);opacity:1} }
        .cd-input {
            padding:10px 14px; border-top:1px solid var(--border); background:var(--surface);
            display:flex; gap:8px; align-items:center;
        }
        .cd-input-field { flex:1; height:34px; background:var(--surf2); border-radius:9px; border:1px solid var(--border); display:flex; align-items:center; padding:0 12px; }
        .cd-input-placeholder { font-size:12px; color:var(--sub); }
        .cd-send { width:34px; height:34px; border-radius:9px; background:var(--green); display:flex; align-items:center; justify-content:center; flex-shrink:0; }

        /* Floating inbox card */
        .demo-inbox {
            position:absolute; top:-18px; right:-24px; z-index:10;
            background:var(--surface); border:1px solid var(--border2); border-radius:12px;
            padding:12px 14px; width:190px; box-shadow:0 10px 36px rgba(0,0,0,.45);
        }
        @media(max-width:820px) { .demo-inbox { display:none; } }
        .di-title { font-size:10px; font-weight:700; color:var(--sub); text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px; }
        .di-row { display:flex; align-items:center; gap:8px; padding:5px 0; }
        .di-row + .di-row { border-top:1px solid var(--border); }
        .di-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
        .di-name { font-size:12px; font-weight:600; color:var(--text); flex:1; }
        .di-time { font-size:10px; color:var(--sub); }
        .di-badge { font-size:9px; font-weight:700; background:rgba(34,197,94,.15); color:var(--green); border-radius:99px; padding:2px 7px; }

        /* ── SECTIONS ── */
        .section { padding:72px 5%; max-width:1160px; margin:0 auto; }
        .section-sep { border:none; border-top:1px solid var(--border); margin:0; }
        .label-row { display:flex; align-items:center; gap:8px; margin-bottom:10px; }
        .label-line { width:18px; height:1px; background:var(--green); }
        .label-text { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--green); }
        .section-h2 { font-size:clamp(1.5rem,3vw,2.1rem); font-weight:800; letter-spacing:-.025em; margin-bottom:12px; }
        .section-sub { font-size:14px; color:var(--muted); line-height:1.8; max-width:500px; }

        /* ── FEATURES ── */
        .feat-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px; margin-top:44px; }
        .feat-card {
            background:var(--surface); border:1px solid var(--border); border-radius:13px; padding:24px;
            transition:border-color .2s, transform .2s;
        }
        .feat-card:hover { border-color:rgba(34,197,94,.25); transform:translateY(-2px); }
        .feat-icon { width:40px; height:40px; border-radius:9px; background:var(--green-glow); border:1px solid rgba(34,197,94,.18); display:flex; align-items:center; justify-content:center; color:var(--green); margin-bottom:16px; }
        .feat-title { font-size:14px; font-weight:700; margin-bottom:7px; }
        .feat-desc  { font-size:13px; color:var(--muted); line-height:1.65; }

        /* ── ADMIN PANEL DEMO ── */
        .admin-demo-section { padding:72px 5%; }
        .admin-demo-inner { max-width:1160px; margin:0 auto; }
        .admin-demo-layout {
            margin-top:48px; background:var(--surface); border:1px solid var(--border2); border-radius:16px;
            overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.45);
            display:grid; grid-template-columns:200px 280px 1fr;
            min-height:420px;
        }
        @media(max-width:900px) { .admin-demo-layout { grid-template-columns:52px 1fr; } .adm-inbox-col { display:none; } }
        @media(max-width:600px) { .admin-demo-layout { grid-template-columns:1fr; } .adm-sidebar { display:none; } }

        /* Sidebar */
        .adm-sidebar {
            background:#0d1117; border-right:1px solid var(--border);
            display:flex; flex-direction:column; gap:2px; padding:12px 8px;
        }
        .adm-logo { display:flex; align-items:center; gap:8px; padding:8px; margin-bottom:12px; }
        .adm-logo-dot { width:28px; height:28px; border-radius:7px; background:var(--green); display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color:#0d1117; flex-shrink:0; }
        .adm-logo-name { font-size:12px; font-weight:700; color:var(--text); white-space:nowrap; overflow:hidden; }
        .adm-nav-item {
            display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px;
            font-size:12px; color:var(--sub); cursor:pointer; transition:.15s; white-space:nowrap; overflow:hidden;
        }
        .adm-nav-item.active { background:rgba(34,197,94,.1); color:var(--green); }
        .adm-nav-item:hover:not(.active) { background:var(--surf2); color:var(--muted); }
        .adm-nav-icon { width:15px; height:15px; flex-shrink:0; }
        .adm-badge { margin-left:auto; background:var(--green); color:#0d1117; font-size:9px; font-weight:800; padding:2px 6px; border-radius:99px; }

        /* Inbox column */
        .adm-inbox-col { border-right:1px solid var(--border); display:flex; flex-direction:column; }
        .adm-inbox-hdr { padding:12px 14px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
        .adm-inbox-title { font-size:12px; font-weight:700; color:var(--text); }
        .adm-inbox-count { font-size:11px; color:var(--sub); }
        .adm-ticket { padding:10px 14px; border-bottom:1px solid var(--border); cursor:pointer; transition:.12s; }
        .adm-ticket:hover { background:var(--surf2); }
        .adm-ticket.selected { background:rgba(34,197,94,.07); border-left:2px solid var(--green); }
        .adm-ticket-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:3px; }
        .adm-ticket-name { font-size:12px; font-weight:700; color:var(--text); }
        .adm-ticket-time { font-size:10px; color:var(--sub); }
        .adm-ticket-msg { font-size:11px; color:var(--sub); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px; }
        .adm-ticket-badge { font-size:9px; font-weight:700; padding:2px 7px; border-radius:99px; }
        .adm-badge-bot  { background:rgba(59,130,246,.15); color:#60a5fa; }
        .adm-badge-human { background:rgba(34,197,94,.15); color:var(--green); }

        /* Chat column */
        .adm-chat-col { display:flex; flex-direction:column; }
        .adm-chat-hdr { padding:12px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
        .adm-chat-avatar { width:30px; height:30px; border-radius:50%; background:var(--green); display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color:#0d1117; flex-shrink:0; }
        .adm-chat-info-name { font-size:12.5px; font-weight:700; color:var(--text); }
        .adm-chat-info-status { font-size:10.5px; color:var(--sub); }
        .adm-chat-body { flex:1; padding:14px 16px; display:flex; flex-direction:column; gap:8px; background:var(--bg); overflow:hidden; }
        .adm-msg { display:flex; gap:6px; align-items:flex-end; }
        .adm-msg.right { flex-direction:row-reverse; }
        .adm-msg-bubble { padding:7px 11px; border-radius:12px; font-size:11.5px; line-height:1.5; max-width:80%; }
        .adm-msg-bubble.bot { background:var(--surface); border:1px solid var(--border); color:var(--text); border-radius:3px 12px 12px 12px; }
        .adm-msg-bubble.user { background:var(--surf2); border:1px solid var(--border); color:var(--muted); border-radius:12px 12px 3px 12px; }
        .adm-msg-bubble.agent { background:var(--green); color:#0d1117; font-weight:600; border-radius:12px 3px 12px 12px; }
        .adm-msg-dot { width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:800; flex-shrink:0; }
        .adm-chat-input { padding:10px 14px; border-top:1px solid var(--border); display:flex; gap:8px; align-items:center; background:var(--surface); }
        .adm-chat-field { flex:1; height:32px; background:var(--surf2); border-radius:8px; border:1px solid var(--border); display:flex; align-items:center; padding:0 10px; }
        .adm-chat-placeholder { font-size:11px; color:var(--sub); }

        /* ── STEPS ── */
        .steps-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-top:44px; }
        .step-card { background:var(--surface); border:1px solid var(--border); border-radius:13px; padding:24px; position:relative; }
        .step-num { font-size:11px; font-weight:800; color:var(--green); letter-spacing:.06em; text-transform:uppercase; margin-bottom:12px; }
        .step-title { font-size:14.5px; font-weight:700; margin-bottom:7px; }
        .step-desc  { font-size:13px; color:var(--muted); line-height:1.65; }

        /* ── PRICING ── */
        .pricing-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px; max-width:620px; margin:44px auto 0; }
        .plan-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:28px; position:relative; }
        .plan-card.featured { border-color:rgba(34,197,94,.35); }
        .plan-badge { position:absolute; top:-12px; left:50%; transform:translateX(-50%); background:var(--green); color:#0d1117; font-size:11px; font-weight:700; padding:3px 14px; border-radius:99px; white-space:nowrap; }
        .plan-name  { font-size:12px; font-weight:700; color:var(--sub); text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px; }
        .plan-price { font-size:2.2rem; font-weight:800; letter-spacing:-.03em; line-height:1; margin-bottom:6px; }
        .plan-price span { font-size:14px; font-weight:500; color:var(--sub); }
        .plan-desc  { font-size:13px; color:var(--muted); margin-bottom:22px; line-height:1.5; }
        .plan-feats { list-style:none; display:flex; flex-direction:column; gap:9px; margin-bottom:24px; }
        .plan-feats li { display:flex; align-items:center; gap:9px; font-size:13px; color:rgba(255,255,255,.7); }
        .plan-feats li.off { color:var(--sub); }
        .plan-feats svg { flex-shrink:0; }

        /* ── FAQ ── */
        .faq-list { max-width:700px; margin:44px auto 0; }
        .faq-item { border-bottom:1px solid var(--border); }
        .faq-q { display:flex; justify-content:space-between; align-items:center; padding:18px 0; cursor:pointer; font-size:14px; font-weight:600; gap:16px; }
        .faq-q:hover { color:var(--green); }
        .faq-a { font-size:13px; color:var(--muted); line-height:1.75; padding-bottom:16px; display:none; }
        .faq-item.open .faq-a { display:block; }
        .faq-item.open .faq-arrow { transform:rotate(180deg); }
        .faq-arrow { transition:transform .2s; flex-shrink:0; color:var(--sub); }

        /* ── CTA ── */
        .cta-section {
            border-top:1px solid var(--border); padding:88px 5%; text-align:center;
            background:radial-gradient(ellipse 80% 60% at 50% 100%, rgba(34,197,94,.07) 0%, transparent 70%);
        }
        .cta-h2 { font-size:clamp(1.6rem,3.2vw,2.2rem); font-weight:800; letter-spacing:-.025em; margin-bottom:14px; }
        .cta-sub { font-size:14px; color:var(--muted); margin-bottom:34px; line-height:1.75; }

        /* ── FOOTER ── */
        .footer-main {
            border-top:1px solid var(--border); background:#0a0d14;
            padding:56px 5% 32px;
        }
        .footer-grid {
            max-width:1160px; margin:0 auto;
            display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:40px;
            margin-bottom:40px;
        }
        @media(max-width:860px) { .footer-grid { grid-template-columns:1fr 1fr; } }
        @media(max-width:520px) { .footer-grid { grid-template-columns:1fr; } }
        .footer-brand-col {}
        .footer-brand-row { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
        .footer-brand-row img { height:32px; width:32px; border-radius:7px; }
        .footer-brand-row span { font-size:15px; font-weight:700; color:var(--text); }
        .footer-tagline { font-size:13px; color:var(--sub); line-height:1.7; max-width:260px; margin-bottom:18px; }
        .footer-col-title { font-size:11px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--sub); margin-bottom:16px; }
        .footer-col-links { display:flex; flex-direction:column; gap:10px; }
        .footer-col-links a { font-size:13px; color:var(--muted); transition:color .15s; }
        .footer-col-links a:hover { color:var(--text); }
        .footer-bottom {
            max-width:1160px; margin:0 auto;
            padding-top:20px; border-top:1px solid var(--border);
            display:flex; align-items:center; justify-content:space-between;
            flex-wrap:wrap; gap:10px;
        }
        .footer-copy { font-size:12px; color:var(--sub); }
        .footer-legal-links { display:flex; gap:16px; }
        .footer-legal-links a { font-size:12px; color:var(--sub); transition:color .15s; }
        .footer-legal-links a:hover { color:var(--text); }

        /* ── SOCIAL ICONS ── */
        .footer-socials { display:flex; gap:8px; margin-top:2px; }
        .social-btn { width:32px; height:32px; border-radius:8px; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--sub); transition:.15s; }
        .social-btn:hover { border-color:var(--border2); color:var(--text); background:var(--surface); }

        /* ── BLOG POSTS ── */
        @media(max-width:600px) {
            .section { padding:52px 5%; }
            .cta-section { padding:60px 5%; }
        }
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
        <a href="#demo-panel">Panel demo</a>
        <a href="#pricing">Precios</a>
        <a href="#faq">FAQ</a>
        <a href="/novedades">Blog</a>
    </div>
    <div class="nav-actions">
        @auth
            <a href="{{ auth()->user()?->is_super_admin ? '/nx-hq' : '/app' }}" class="btn btn-sm btn-primary">Mi panel</a>
        @else
            <a href="/login" class="btn btn-sm btn-ghost">Iniciar sesión</a>
            <a href="/register" class="btn btn-sm btn-primary">Probar gratis</a>
        @endauth
        <button class="nav-hamburger" onclick="toggleMobileNav()" aria-label="Menú">
            <svg id="hbIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
</nav>
<div class="nav-mobile" id="mobileNav">
    <a href="#features" onclick="closeMobileNav()">Funciones</a>
    <a href="#demo-panel" onclick="closeMobileNav()">Panel demo</a>
    <a href="#pricing" onclick="closeMobileNav()">Precios</a>
    <a href="#faq" onclick="closeMobileNav()">FAQ</a>
    <a href="/novedades" onclick="closeMobileNav()">Blog</a>
    <a href="/login" onclick="closeMobileNav()">Iniciar sesión</a>
    <a href="/register" onclick="closeMobileNav()" style="color:var(--green);font-weight:700">Probar gratis →</a>
</div>

<!-- HERO -->
<div class="hero">
    <div>
        <div class="hero-eyebrow"><span></span>Chat en vivo · IA · Agentes · Telegram</div>
        <h1 class="hero-h1">Soporte 24/7 para<br>tu web, con <em>IA que<br>sí responde</em></h1>
        <p class="hero-desc">
            Un widget de chat inteligente que atiende clientes solo. Cuando el bot no puede, escala a tu equipo en segundos. Todo en un panel unificado.
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
                <a href="#features" class="btn btn-lg btn-dark">Ver funciones</a>
            @endauth
        </div>
        @guest
        <div class="hero-trust">
            <div class="trust-item"><div class="trust-dot"></div>Sin tarjeta de crédito</div>
            <div class="trust-item"><div class="trust-dot"></div>Configuración en 5 min</div>
            <div class="trust-item"><div class="trust-dot"></div>Plan gratis siempre</div>
        </div>
        @endguest
    </div>

    <!-- Chat animado -->
    <div class="chat-demo-wrap">
        <div class="chat-demo">
            <div class="cd-header">
                <div class="cd-avatar">
                    <svg fill="none" stroke="rgba(255,255,255,.9)" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <div>
                    <div class="cd-name">Nexova IA</div>
                    <div class="cd-status"><div class="cd-status-dot"></div>En línea ahora</div>
                </div>
            </div>
            <div class="cd-msgs" id="chatMsgs"></div>
            <div class="cd-input">
                <div class="cd-input-field"><span class="cd-input-placeholder" id="chatInputPlaceholder">Escribe tu mensaje...</span></div>
                <div class="cd-send">
                    <svg fill="none" stroke="#0d1117" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
            </div>
        </div>
        <!-- Floating inbox -->
        <div class="demo-inbox">
            <div class="di-title">Inbox activo</div>
            <div class="di-row">
                <div class="di-dot" style="background:#22c55e"></div>
                <div class="di-name">Carlos M.</div>
                <div class="di-badge">IA</div>
            </div>
            <div class="di-row">
                <div class="di-dot" style="background:#f59e0b"></div>
                <div class="di-name">Laura P.</div>
                <div class="di-time">2m</div>
            </div>
            <div class="di-row">
                <div class="di-dot" style="background:var(--sub)"></div>
                <div class="di-name">Andrés R.</div>
                <div class="di-time">18m</div>
            </div>
        </div>
    </div>
</div>

<hr class="section-sep">

<!-- FEATURES -->
<section class="section" id="features">
    <div class="label-row"><span class="label-line"></span><span class="label-text">Funciones</span></div>
    <h2 class="section-h2">Todo lo que necesita tu equipo de soporte</h2>
    <p class="section-sub">Desde el bot hasta el historial completo, en un solo lugar sin complicaciones.</p>

    <div class="feat-grid">
        <div class="feat-card">
            <div class="feat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></div>
            <div class="feat-title">Bot con IA real</div>
            <div class="feat-desc">Usa Groq (Llama 3.3) y Gemini como fallback. Responde con tu base de conocimiento o IA general cuando es necesario.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></div>
            <div class="feat-title">Widget personalizable</div>
            <div class="feat-desc">Colores, posición, horario de atención, formulario previo y mensajes de bienvenida. Todo configurable en segundos.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div class="feat-title">Equipo de agentes</div>
            <div class="feat-desc">Invita a tu equipo con roles y permisos. Cada agente ve las conversaciones en tiempo real y puede tomar el control.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
            <div class="feat-title">Base de conocimiento</div>
            <div class="feat-desc">Crea artículos o importa contenido desde URLs de tu sitio. El bot los usa para responder con precisión antes de recurrir a la IA.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></div>
            <div class="feat-title">Telegram conectado</div>
            <div class="feat-desc">Conecta tu bot de Telegram y atiende sus conversaciones desde el mismo inbox que el chat web. Sin cambiar de pantalla.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            <div class="feat-title">Dashboard y métricas</div>
            <div class="feat-desc">Tickets abiertos, calificaciones, tiempos de respuesta y actividad del bot. Todo en tiempo real en el panel principal.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></div>
            <div class="feat-title">Integración WooCommerce</div>
            <div class="feat-desc">Plugin oficial para WordPress. El bot conoce tu catálogo, precios y stock automáticamente. Los clientes consultan sus pedidos.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></div>
            <div class="feat-title">Adjuntos e imágenes</div>
            <div class="feat-desc">Clientes y agentes comparten imágenes y PDFs en el chat. Con soporte de pegar directo con Ctrl+V desde el portapapeles.</div>
        </div>
    </div>
</section>

<hr class="section-sep">

<!-- ADMIN PANEL DEMO -->
<section class="admin-demo-section" id="demo-panel">
    <div class="admin-demo-inner">
        <div class="label-row"><span class="label-line"></span><span class="label-text">Panel de agentes</span></div>
        <h2 class="section-h2">Tu equipo atiende desde un solo lugar</h2>
        <p class="section-sub">Inbox unificado con todas las conversaciones — web, Telegram, bot y agentes — en tiempo real.</p>

        <div class="admin-demo-layout">
            <!-- Sidebar -->
            <div class="adm-sidebar">
                <div class="adm-logo">
                    <div class="adm-logo-dot">N</div>
                    <div class="adm-logo-name">Mi empresa</div>
                </div>
                <div class="adm-nav-item">
                    <svg class="adm-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </div>
                <div class="adm-nav-item active">
                    <svg class="adm-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Conversaciones</span>
                    <span class="adm-badge">4</span>
                </div>
                <div class="adm-nav-item">
                    <svg class="adm-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>
                    <span>Conocimiento</span>
                </div>
                <div class="adm-nav-item">
                    <svg class="adm-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857"/></svg>
                    <span>Agentes</span>
                </div>
                <div class="adm-nav-item">
                    <svg class="adm-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    <span>Ajustes</span>
                </div>
            </div>

            <!-- Inbox column -->
            <div class="adm-inbox-col">
                <div class="adm-inbox-hdr">
                    <span class="adm-inbox-title">Activos</span>
                    <span class="adm-inbox-count">4 conversaciones</span>
                </div>
                <div class="adm-ticket selected">
                    <div class="adm-ticket-top">
                        <span class="adm-ticket-name">Carlos M.</span>
                        <span class="adm-ticket-badge adm-badge-human">Agente</span>
                    </div>
                    <div class="adm-ticket-msg">¿Hacen envíos a Medellín?</div>
                </div>
                <div class="adm-ticket">
                    <div class="adm-ticket-top">
                        <span class="adm-ticket-name">Laura P.</span>
                        <span class="adm-ticket-badge adm-badge-bot">Bot IA</span>
                    </div>
                    <div class="adm-ticket-msg">Quiero cancelar mi pedido #4521</div>
                </div>
                <div class="adm-ticket">
                    <div class="adm-ticket-top">
                        <span class="adm-ticket-name">Andrés R. <span style="font-size:9px;color:var(--sub);margin-left:4px">Telegram</span></span>
                        <span class="adm-ticket-badge adm-badge-bot">Bot IA</span>
                    </div>
                    <div class="adm-ticket-msg">¿Cuál es el tiempo de garantía?</div>
                </div>
                <div class="adm-ticket">
                    <div class="adm-ticket-top">
                        <span class="adm-ticket-name">Sofia V.</span>
                        <span class="adm-ticket-time">8m</span>
                    </div>
                    <div class="adm-ticket-msg">Hola, necesito ayuda con mi cuenta</div>
                </div>
            </div>

            <!-- Chat column -->
            <div class="adm-chat-col">
                <div class="adm-chat-hdr">
                    <div class="adm-chat-avatar">C</div>
                    <div>
                        <div class="adm-chat-info-name">Carlos Martínez</div>
                        <div class="adm-chat-info-status">Atendido por: Ana López · hace 2 min</div>
                    </div>
                    <div style="margin-left:auto;display:flex;gap:6px">
                        <div style="padding:4px 10px;border-radius:6px;font-size:10px;font-weight:700;background:rgba(34,197,94,.12);color:var(--green);border:1px solid rgba(34,197,94,.2)">Activo</div>
                    </div>
                </div>
                <div class="adm-chat-body">
                    <div class="adm-msg">
                        <div class="adm-msg-dot" style="background:var(--green);color:#0d1117">N</div>
                        <div class="adm-msg-bubble bot">Hola Carlos, soy Nexova IA 👋 ¿En qué puedo ayudarte hoy?</div>
                    </div>
                    <div class="adm-msg right">
                        <div class="adm-msg-bubble user">¿Hacen envíos a Medellín?</div>
                    </div>
                    <div class="adm-msg">
                        <div class="adm-msg-dot" style="background:var(--green);color:#0d1117">N</div>
                        <div class="adm-msg-bubble bot">¡Claro! Hacemos envíos a todo Colombia incluyendo Medellín. El tiempo estimado es de 2 a 4 días hábiles con Servientrega y Coordinadora.</div>
                    </div>
                    <div class="adm-msg right">
                        <div class="adm-msg-bubble user">¿Y cuánto vale el envío?</div>
                    </div>
                    <div class="adm-msg" style="align-items:center;gap:6px">
                        <div style="font-size:10px;color:var(--sub);padding:4px 10px;background:rgba(255,255,255,.04);border-radius:99px;border:1px solid var(--border)">👤 Ana López se unió a la conversación</div>
                    </div>
                    <div class="adm-msg">
                        <div class="adm-msg-dot" style="background:#7c3aed;color:#fff">A</div>
                        <div class="adm-msg-bubble agent">¡Hola Carlos! El envío a Medellín tiene un costo de $8.500 COP para pedidos menores a $80.000. Gratis por compras mayores. ¿Te ayudo a iniciar el pedido?</div>
                    </div>
                </div>
                <div class="adm-chat-input">
                    <div class="adm-chat-field"><span class="adm-chat-placeholder">Responder como agente...</span></div>
                    <div style="width:30px;height:30px;border-radius:8px;background:var(--green);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg fill="none" stroke="#0d1117" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<hr class="section-sep">

<!-- HOW IT WORKS -->
<section class="section">
    <div class="label-row"><span class="label-line"></span><span class="label-text">Inicio rápido</span></div>
    <h2 class="section-h2">De cero a tu primer chat en minutos</h2>
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
            <div class="step-desc">Escribe artículos o importa páginas de tu sitio. El bot los usa para responder con precisión antes de llamar a la IA.</div>
        </div>
        <div class="step-card">
            <div class="step-num">Paso 03</div>
            <div class="step-title">Responde en vivo cuando lo necesites</div>
            <div class="step-desc">Recibirás una alerta cuando un cliente pida hablar con un agente. Toma el control desde el panel en segundos.</div>
        </div>
    </div>
</section>

<hr class="section-sep">

<!-- PRICING -->
<section class="section" id="pricing" style="text-align:center">
    <div class="label-row" style="justify-content:center"><span class="label-line"></span><span class="label-text">Precios</span></div>
    <h2 class="section-h2">Simple, sin sorpresas</h2>
    <p class="section-sub" style="margin-inline:auto">Empieza gratis, escala cuando lo necesites.</p>
    @php
        $landingPlans = isset($plans) ? $plans : collect();
        $checkSvg = '<svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        $xSvg     = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
    @endphp
    <div class="pricing-grid" style="max-width:{{ $landingPlans->count() > 2 ? '900px' : '620px' }};margin:44px auto 0">
        @forelse($landingPlans as $plan)
        @php
            $isFeatured = ! $plan->isFree();
            $features   = is_array($plan->features) ? $plan->features : [];
        @endphp
        <div class="plan-card {{ $isFeatured ? 'featured' : '' }}">
            @if($isFeatured)<div class="plan-badge">Recomendado</div>@endif
            <div class="plan-name">{{ $plan->name }}</div>
            <div class="plan-price">{{ $plan->isFree() ? '$0' : '$'.number_format($plan->price_usd, 0) }}<span> / mes</span></div>
            <div class="plan-desc">{{ $plan->description ?? ($plan->isFree() ? 'Para explorar la plataforma sin costo.' : 'Para negocios en crecimiento.') }}</div>
            <ul class="plan-feats">
                @if($plan->max_widgets) <li>{!! $checkSvg !!}{{ $plan->max_widgets >= 999 ? 'Widgets ilimitados' : $plan->max_widgets.' widget'.($plan->max_widgets>1?'s':'') }}</li>@endif
                @if($plan->max_agents)  <li>{!! $checkSvg !!}{{ $plan->max_agents >= 999 ? 'Agentes ilimitados' : 'Hasta '.$plan->max_agents.' agentes' }}</li>@endif
                <li>{!! $checkSvg !!}Base de conocimiento</li>
                @if($plan->max_sessions_per_day)<li>{!! $checkSvg !!}{{ $plan->max_sessions_per_day >= 9999 ? 'Sesiones ilimitadas' : number_format($plan->max_sessions_per_day).' sesiones bot/día' }}</li>@endif
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
                    @php $featLabels=['kb_manual'=>'Artículos manuales','kb_scrape'=>'Escaneo automático','own_api_keys'=>'Claves IA propias','priority_support'=>'Soporte prioritario']; @endphp
                    <li>{!! $checkSvg !!}{{ $featLabels[$feat] ?? ucfirst(str_replace('_',' ',$feat)) }}</li>
                @endforeach
            </ul>
            @if($plan->isFree())
                <a href="/register" class="btn btn-md btn-dark" style="width:100%;justify-content:center">Comenzar gratis</a>
            @else
                <a href="/register" class="btn btn-md btn-primary" style="width:100%;justify-content:center">Empezar con {{ $plan->name }}</a>
            @endif
        </div>
        @empty
        <div class="plan-card">
            <div class="plan-name">Free</div>
            <div class="plan-price">$0<span> / mes</span></div>
            <div class="plan-desc">Para explorar la plataforma sin costo.</div>
            <ul class="plan-feats">
                <li>{!! $checkSvg !!}1 widget · 3 agentes</li>
                <li>{!! $checkSvg !!}Base de conocimiento</li>
                <li>{!! $checkSvg !!}50 sesiones bot/día</li>
                <li class="off">{!! $xSvg !!}Sin IA generativa</li>
            </ul>
            <a href="/register" class="btn btn-md btn-dark" style="width:100%;justify-content:center">Comenzar gratis</a>
        </div>
        <div class="plan-card featured">
            <div class="plan-badge">Recomendado</div>
            <div class="plan-name">Pro</div>
            <div class="plan-price">$49<span> / mes</span></div>
            <div class="plan-desc">Para negocios en crecimiento.</div>
            <ul class="plan-feats">
                <li>{!! $checkSvg !!}Widgets y agentes ilimitados</li>
                <li>{!! $checkSvg !!}IA con Groq + Gemini</li>
                <li>{!! $checkSvg !!}Sesiones ilimitadas</li>
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
            <div class="faq-q" onclick="toggleFaq(this)">¿Necesito conocimientos técnicos para instalarlo?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">No. Copia dos líneas de código en tu sitio web. Si usas WordPress, el plugin oficial lo instala automáticamente sin tocar código.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">¿Cómo aprende el bot a responder mis preguntas?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">Creas artículos en la "Base de conocimiento" o importas páginas de tu sitio. El bot busca en esa información antes de usar la IA general de Groq o Gemini.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">¿Qué pasa cuando el bot no sabe responder?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">El bot puede ofrecer escalar la conversación a un agente humano. El cliente también puede pedirlo en cualquier momento. Tú recibirás una notificación inmediata.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">¿Qué métodos de pago aceptan?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">MercadoPago (tarjeta, PSE, Nequi, Daviplata, Efecty) y criptomonedas USDT/USDC en redes Tron (TRC-20), BNB Chain (BEP-20) y Polygon. Los pagos cripto se verifican automáticamente en blockchain.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">¿Puedo cancelar en cualquier momento?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">Sí, sin contratos ni penalizaciones. Al no renovar, seguirás con acceso Pro hasta la fecha de vencimiento y luego pasarás al plan Free automáticamente.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q" onclick="toggleFaq(this)">¿Dónde están almacenados mis datos?
                <svg class="faq-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="faq-a">Nexova Desk es operado por Nexova Digital Solutions, empresa colombiana. Tratamos tus datos conforme a la Ley 1581 de 2012 de protección de datos personales. Consulta nuestra <a href="/p/privacidad" style="color:var(--green)">Política de Privacidad</a>.</div>
        </div>
    </div>
</section>

{{-- BLOG / NOVEDADES --}}
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
        $landingCatColors = ['novedad'=>['bg'=>'rgba(30,64,175,.25)','color'=>'#93c5fd'],'evento'=>['bg'=>'rgba(146,64,14,.25)','color'=>'#fcd34d'],'producto'=>['bg'=>'rgba(21,128,61,.25)','color'=>'#86efac'],'actualizacion'=>['bg'=>'rgba(55,65,81,.3)','color'=>'#d1d5db']];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px">
        @foreach($latestPosts as $post)
        @php $cc = $landingCatColors[$post->category] ?? ['bg'=>'rgba(55,65,81,.3)','color'=>'#d1d5db']; @endphp
        <a href="/novedades/{{ $post->slug }}"
           style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,transform .2s"
           onmouseover="this.style.borderColor='rgba(34,197,94,.22)';this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='rgba(255,255,255,.075)';this.style.transform='translateY(0)'">
            @if($post->cover_image)<img src="{{ $post->cover_image }}" alt="{{ $post->title }}" style="width:100%;height:160px;object-fit:cover">
            @else<div style="width:100%;height:160px;background:var(--surf2);display:flex;align-items:center;justify-content:center"><svg fill="none" stroke="rgba(255,255,255,.12)" viewBox="0 0 24 24" width="36" height="36"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><path d="M21 15l-5-5L5 21" stroke-width="1.5" stroke-linecap="round"/></svg></div>@endif
            <div style="padding:18px;display:flex;flex-direction:column;gap:9px;flex:1">
                <span style="display:inline-flex;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;width:fit-content;background:{{ $cc['bg'] }};color:{{ $cc['color'] }}">{{ $post->categoryLabel() }}</span>
                <div style="font-size:15px;font-weight:700;line-height:1.35;color:var(--text)">{{ $post->title }}</div>
                @if($post->excerpt)<p style="font-size:13px;color:var(--muted);line-height:1.6;margin:0">{{ Str::limit($post->excerpt, 100) }}</p>@endif
                <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between">
                    <span style="font-size:11px;color:var(--sub)">{{ $post->published_at?->format('d/m/Y') }}</span>
                    <span style="font-size:12px;font-weight:600;color:var(--green);display:inline-flex;align-items:center;gap:4px">Leer <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></span>
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
        <h2 class="cta-h2">Empieza gratis hoy mismo</h2>
        <p class="cta-sub">Configura tu primer widget en menos de 5 minutos. Sin tarjeta de crédito.</p>
        <a href="/register" class="btn btn-lg btn-primary" style="display:inline-flex;margin-inline:auto">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Crear cuenta gratis
        </a>
    @endauth
</section>

<!-- FOOTER -->
<footer class="footer-main">
    <div class="footer-grid">
        <div class="footer-brand-col">
            <div class="footer-brand-row">
                <img src="{{ asset('images/nexovadesklogo.png') }}" alt="Nexova Desk"
                     style="filter:brightness(0) saturate(100%) invert(58%) sepia(72%) saturate(500%) hue-rotate(95deg) brightness(95%)">
                <span>Nexova Desk</span>
            </div>
            <p class="footer-tagline">Chat en vivo con IA para tu negocio. Atiende clientes 24/7, automatiza respuestas y gestiona tu equipo desde un panel unificado.</p>
            <div class="footer-socials">
                <a href="#" class="social-btn" title="Facebook">
                    <svg fill="currentColor" viewBox="0 0 24 24" width="14" height="14"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#" class="social-btn" title="Instagram">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                </a>
                <a href="#" class="social-btn" title="LinkedIn">
                    <svg fill="currentColor" viewBox="0 0 24 24" width="14" height="14"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
            </div>
        </div>

        <div>
            <div class="footer-col-title">Producto</div>
            <div class="footer-col-links">
                <a href="#features">Funciones</a>
                <a href="#pricing">Precios</a>
                <a href="#demo-panel">Demo del panel</a>
                <a href="/novedades">Blog</a>
                <a href="#faq">FAQ</a>
            </div>
        </div>

        <div>
            <div class="footer-col-title">Cuenta</div>
            <div class="footer-col-links">
                <a href="/register">Crear cuenta gratis</a>
                <a href="/login">Iniciar sesión</a>
                <a href="/forgot-password">Recuperar contraseña</a>
                @auth
                <a href="{{ auth()->user()?->is_super_admin ? '/nx-hq' : '/app' }}">Mi panel</a>
                @endauth
            </div>
        </div>

        <div>
            <div class="footer-col-title">Legal</div>
            <div class="footer-col-links">
                <a href="/p/terminos">Términos y condiciones</a>
                <a href="/p/privacidad">Política de privacidad</a>
                <a href="/p/habeas-data">Habeas Data</a>
                <a href="/p/contacto">Contacto</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-copy">
            © {{ date('Y') }} Nexova Digital Solutions S.A.S. — Colombia
            <span style="margin:0 6px;color:var(--border)">·</span>
            NIT pendiente de registro
        </div>
        <div class="footer-legal-links">
            <a href="/p/terminos">Términos</a>
            <a href="/p/privacidad">Privacidad</a>
            <a href="/p/habeas-data">Habeas Data</a>
        </div>
    </div>
</footer>

<script>
// ── Mobile nav ──
function toggleMobileNav() {
    const nav = document.getElementById('mobileNav');
    nav.classList.toggle('open');
}
function closeMobileNav() {
    document.getElementById('mobileNav').classList.remove('open');
}

// ── FAQ accordion ──
function toggleFaq(el) {
    const item = el.closest('.faq-item');
    const open = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    if (!open) item.classList.add('open');
}

// ── Animated chat demo ──
(function() {
    const msgs = document.getElementById('chatMsgs');
    const placeholder = document.getElementById('chatInputPlaceholder');
    if (!msgs) return;

    const script = [
        { type:'bot',  text:'¡Hola! 👋 Soy Nexova IA. ¿En qué puedo ayudarte hoy?' },
        { type:'user', text:'¿Cuáles son sus métodos de pago?' },
        { type:'bot',  text:'Aceptamos MercadoPago (tarjeta, PSE, Nequi, Daviplata, Efecty) y criptomonedas USDT/USDC en Tron, BNB Chain y Polygon. ¿Te ayudo con algo más?' },
        { type:'user', text:'¿Hacen envíos a Medellín?' },
        { type:'bot',  text:'¡Claro! Hacemos envíos a todo Colombia incluyendo Medellín. El tiempo estimado es de 2 a 4 días hábiles con Servientrega y Coordinadora. 📦' },
        { type:'user', text:'Perfecto, gracias!' },
        { type:'bot',  text:'Con gusto 😊 Si tienes más preguntas, aquí estaré. También puedo conectarte con un agente en cualquier momento.' },
    ];

    let step = 0, typingEl = null;

    function addMsg(type, text) {
        const isBot = type === 'bot';
        const row = document.createElement('div');
        row.className = isBot ? 'cd-msg-bot' : 'cd-msg-user';
        if (isBot) {
            row.innerHTML = `<div class="cd-dot">N</div><div class="cd-bubble cd-bubble-bot">${text}</div>`;
        } else {
            row.innerHTML = `<div class="cd-bubble cd-bubble-user">${text}</div>`;
        }
        msgs.appendChild(row);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function showTyping() {
        if (typingEl) typingEl.remove();
        const row = document.createElement('div');
        row.className = 'cd-msg-bot';
        row.innerHTML = `<div class="cd-dot">N</div><div class="cd-typing"><span></span><span></span><span></span></div>`;
        typingEl = row;
        msgs.appendChild(row);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function hideTyping() {
        if (typingEl) { typingEl.remove(); typingEl = null; }
    }

    function animateInput(text, cb) {
        let i = 0;
        placeholder.textContent = '';
        const iv = setInterval(() => {
            if (i < text.length) { placeholder.textContent += text[i++]; }
            else { clearInterval(iv); setTimeout(cb, 400); }
        }, 38);
    }

    function runStep() {
        if (step >= script.length) {
            // Reset
            setTimeout(() => {
                msgs.innerHTML = '';
                placeholder.textContent = 'Escribe tu mensaje...';
                step = 0;
                setTimeout(runStep, 800);
            }, 3500);
            return;
        }
        const s = script[step++];
        if (s.type === 'user') {
            animateInput(s.text, () => {
                placeholder.textContent = 'Escribe tu mensaje...';
                addMsg('user', s.text);
                setTimeout(runStep, 500);
            });
        } else {
            showTyping();
            setTimeout(() => {
                hideTyping();
                addMsg('bot', s.text);
                setTimeout(runStep, 900);
            }, 1400);
        }
    }

    setTimeout(runStep, 600);
})();
</script>
</body>
</html>
