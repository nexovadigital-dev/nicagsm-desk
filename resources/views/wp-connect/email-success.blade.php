<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexión autorizada — Nexova Desk</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 36px 32px; width: 100%; max-width: 400px; text-align: center; }
        .icon { display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: #f0fdf4; border-radius: 50%; color: #22c55e; margin-bottom: 16px; }
        h1 { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 8px; }
        p { font-size: 13.5px; color: #6b7280; line-height: 1.6; }
        .org { font-weight: 600; color: #111827; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <h1>¡Conexión autorizada!</h1>
    <p>La tienda WooCommerce ha sido vinculada a <span class="org">{{ $orgName }}</span>.</p>
    <p style="margin-top:12px">La conexión se completará automáticamente en el plugin. Puedes cerrar esta ventana.</p>
</div>
</body>
</html>
