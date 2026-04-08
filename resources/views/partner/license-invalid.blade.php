<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licencia no activa — Nexova Desk</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, system-ui, sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; color: #0f172a; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 48px 40px; max-width: 460px; width: 100%; text-align: center; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
        .logo { font-size: 22px; font-weight: 800; margin-bottom: 36px; }
        .logo span { color: #22c55e; }
        .icon { width: 60px; height: 60px; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; border: 1px solid #fed7aa; }
        h1 { font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        p  { font-size: 13.5px; color: #64748b; line-height: 1.65; }
        .domain-block {
            margin-top: 24px; background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 8px; padding: 12px 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .domain-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
        .domain-value { font-size: 13px; font-weight: 600; color: #334155; word-break: break-all; text-align: left; }
        .error-id {
            margin-top: 10px; font-size: 11px; color: #94a3b8;
            font-family: ui-monospace, monospace; letter-spacing: .03em;
        }
        .footer { margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #94a3b8; }
        .footer a { color: #22c55e; text-decoration: none; font-weight: 600; }
        .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    @php
        $domain  = parse_url(config('app.url'), PHP_URL_HOST) ?? config('app.url');
        $errorId = 'NX-' . strtoupper(substr(md5($domain . date('Ymd')), 0, 8));
    @endphp
    <div class="card">
        <div class="logo">Nexova <span>Desk</span></div>

        <div class="icon">
            <svg fill="none" stroke="#f97316" viewBox="0 0 24 24" width="26" height="26">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>

        <h1>Licencia no activa</h1>
        <p>
            No se puede constatar que tengas los permisos para ejecutar Nexova Desk en este dominio.
            Contacta al administrador de Nexova para activar tu licencia.
        </p>

        <div class="domain-block">
            <div>
                <div class="domain-label">Dominio</div>
                <div class="domain-value">{{ $domain }}</div>
            </div>
        </div>

        <div class="error-id">ID de error: {{ $errorId }}</div>

        <div class="footer">
            ¿Necesitas ayuda? Escríbenos a <a href="mailto:info@nexovadesk.com">info@nexovadesk.com</a>
            indicando el ID de error.
        </div>
    </div>
</body>
</html>
