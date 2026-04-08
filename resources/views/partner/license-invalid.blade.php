<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licencia inválida — Nexova Desk</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, system-ui, sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 48px 40px; max-width: 440px; width: 100%; text-align: center; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
        .logo { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 32px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .logo span { color: #22c55e; }
        .icon { width: 56px; height: 56px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        h1 { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
        p  { font-size: 14px; color: #64748b; line-height: 1.6; }
        .code { margin-top: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; font-family: ui-monospace, monospace; font-size: 12px; color: #64748b; word-break: break-all; }
        .footer { margin-top: 28px; font-size: 12px; color: #94a3b8; }
        .footer a { color: #22c55e; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Nexova<span>Desk</span></div>
        <div class="icon">
            <svg fill="none" stroke="#ef4444" viewBox="0 0 24 24" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <h1>Licencia de partner no válida</h1>
        <p>No se pudo verificar la licencia de esta instalación. Esto puede deberse a que el token es incorrecto o a que el acceso fue revocado.</p>
        <div class="code">Token: {{ substr(config('partner.token', 'no configurado'), 0, 16) }}…</div>
        <div class="footer">
            ¿Necesitas ayuda? Contacta a <a href="https://nexovadesk.com">Nexova Desk</a>
        </div>
    </div>
</body>
</html>
