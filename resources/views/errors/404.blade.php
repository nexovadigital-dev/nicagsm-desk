<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Página no encontrada · Nexova Desk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/nexovadeskicon.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', system-ui, sans-serif; background: #0f1117; color: #f1f5f9; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
        .code { font-size: clamp(80px, 18vw, 140px); font-weight: 800; line-height: 1; letter-spacing: -.05em; color: #22c55e; }
        .title { font-size: clamp(20px, 4vw, 28px); font-weight: 700; margin: 12px 0 10px; }
        .desc { font-size: 15px; color: rgba(255,255,255,.45); text-align: center; max-width: 380px; line-height: 1.7; }
        .actions { display: flex; gap: 10px; margin-top: 32px; flex-wrap: wrap; justify-content: center; }
        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px; border-radius: 9px; font-size: 14px; font-weight: 600; font-family: inherit; text-decoration: none; transition: background .15s, border-color .15s; }
        .btn-primary { background: #22c55e; color: #0d1117; }
        .btn-primary:hover { background: #16a34a; }
        .btn-ghost { border: 1px solid rgba(255,255,255,.12); color: rgba(255,255,255,.6); }
        .btn-ghost:hover { border-color: rgba(255,255,255,.25); color: #fff; }
        .brand { position: fixed; bottom: 28px; font-size: 12px; color: rgba(255,255,255,.2); }
    </style>
</head>
<body>
    <div class="code">404</div>
    <div class="title">Página no encontrada</div>
    <p class="desc">La página que buscas no existe o fue movida a otra dirección.</p>
    <div class="actions">
        <a href="/" class="btn btn-primary">Ir al inicio</a>
        <a href="javascript:history.back()" class="btn btn-ghost">← Volver</a>
    </div>
    <span class="brand">Nexova Desk · Nexova Digital Solutions</span>
</body>
</html>
