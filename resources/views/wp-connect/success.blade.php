<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conectado — Nexova Desk</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 36px 32px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: #f0fdf4;
            border-radius: 50%;
            color: #22c55e;
            margin-bottom: 16px;
        }
        h1 { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 8px; }
        p  { font-size: 13.5px; color: #6b7280; line-height: 1.6; }
        .org { font-weight: 600; color: #111827; }
        .spinner {
            display: inline-block;
            width: 16px; height: 16px;
            border: 2px solid #e5e7eb;
            border-top-color: #22c55e;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            vertical-align: middle;
            margin-right: 6px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .closing { font-size: 12.5px; color: #9ca3af; margin-top: 16px; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" width="28" height="28">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>

    <h1>¡Conexión exitosa!</h1>
    <p>Tu tienda ha sido conectada a <span class="org">{{ $orgName }}</span>.</p>

    <p class="closing"><span class="spinner"></span>Cerrando ventana…</p>
</div>

<script>
(function () {
    var payload = {
        source:     'nexova_desk_connect',
        token:      {{ Js::from($token) }},
        server_url: {{ Js::from($serverUrl) }},
        org_name:   {{ Js::from($orgName) }},
        org_id:     {{ Js::from($orgId) }},
    };

    if (window.opener && !window.opener.closed) {
        window.opener.postMessage(payload, {{ Js::from($origin ?: '*') }});
    }

    setTimeout(function () { window.close(); }, 1200);
}());
</script>
</body>
</html>
