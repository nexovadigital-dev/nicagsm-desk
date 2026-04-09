<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Nexova Desk' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Inter', system-ui, sans-serif;
            background: #0d1117; color: #f1f5f9;
            min-height: 100vh; display: flex;
        }

        /* ── Left panel — decorative ── */
        .auth-left {
            display: none;
            width: 420px; flex-shrink: 0;
            background: #111827;
            border-right: 1px solid rgba(255,255,255,.06);
            padding: 48px 44px;
            flex-direction: column;
            justify-content: space-between;
        }
        @media (min-width: 900px) { .auth-left { display: flex; } }

        .auth-brand { display: flex; align-items: center; gap: 14px; }
        .auth-brand-logo { height: 64px; width: 64px; border-radius: 12px; object-fit: contain; }
        .auth-brand-name { font-size: 17px; font-weight: 700; color: #fff; letter-spacing: -.02em; }
        .auth-brand-sub  { font-size: 11px; color: #22c55e; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }

        .auth-tagline { font-size: 26px; font-weight: 700; color: #fff; line-height: 1.3; letter-spacing: -.02em; }
        .auth-tagline span { color: #22c55e; }

        .auth-features { display: flex; flex-direction: column; gap: 14px; }
        .auth-feat { display: flex; align-items: flex-start; gap: 12px; }
        .auth-feat-dot { width: 20px; height: 20px; border-radius: 50%; background: rgba(34,197,94,.15); border: 1px solid rgba(34,197,94,.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }
        .auth-feat-dot svg { color: #22c55e; }
        .auth-feat-text { font-size: 13.5px; color: rgba(255,255,255,.7); line-height: 1.5; }
        .auth-feat-text strong { color: #fff; font-weight: 600; }

        .auth-footer-note { font-size: 11px; color: rgba(255,255,255,.3); }

        /* ── Right panel — form ── */
        .auth-right {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 32px 20px;
        }
        .auth-box { width: 100%; max-width: 400px; }

        .auth-logo-mobile {
            display: flex; align-items: center; gap: 10px; margin-bottom: 32px;
        }
        @media (min-width: 900px) { .auth-logo-mobile { display: none; } }
        .auth-logo-mobile-name { font-size: 16px; font-weight: 700; color: #fff; }

        .auth-title { font-size: 22px; font-weight: 700; color: #fff; letter-spacing: -.02em; margin-bottom: 6px; }
        .auth-subtitle { font-size: 13.5px; color: rgba(255,255,255,.45); margin-bottom: 28px; line-height: 1.5; }

        .auth-field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
        .auth-label { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.6); letter-spacing: .04em; text-transform: uppercase; }
        .auth-input {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 9px;
            color: #fff;
            font-size: 14px; font-family: inherit;
            padding: 11px 14px; outline: none;
            transition: border-color .15s;
            width: 100%;
        }
        .auth-input:focus { border-color: #22c55e; }
        .auth-input::placeholder { color: rgba(255,255,255,.25); }

        .auth-btn {
            width: 100%; padding: 12px; border-radius: 9px;
            background: #22c55e; color: #0d1117;
            font-size: 14px; font-weight: 700; font-family: inherit;
            border: none; cursor: pointer; transition: background .1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .auth-btn:hover { background: #16a34a; }
        .auth-btn:disabled { opacity: .6; cursor: not-allowed; }

        .auth-divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
        .auth-divider-line { flex: 1; height: 1px; background: rgba(255,255,255,.08); }
        .auth-divider-text { font-size: 11px; color: rgba(255,255,255,.3); }

        .auth-link-row { text-align: center; margin-top: 20px; font-size: 13px; color: rgba(255,255,255,.4); }
        .auth-link { color: #22c55e; text-decoration: none; font-weight: 600; }
        .auth-link:hover { text-decoration: underline; }

        .auth-error {
            background: rgba(220,38,38,.1); border: 1px solid rgba(220,38,38,.25);
            border-radius: 8px; padding: 10px 13px;
            font-size: 13px; color: #f87171; margin-bottom: 14px;
        }
        .auth-success {
            background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.25);
            border-radius: 8px; padding: 10px 13px;
            font-size: 13px; color: #4ade80; margin-bottom: 14px;
        }

        /* OTP input */
        .otp-input {
            letter-spacing: .4em; font-size: 22px; font-weight: 700;
            text-align: center; font-family: monospace;
        }
        .otp-hint { font-size: 12.5px; color: rgba(255,255,255,.4); text-align: center; margin-top: 8px; line-height: 1.6; }

        /* Trial badge */
        .trial-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.25);
            color: #4ade80; font-size: 11px; font-weight: 600;
            padding: 3px 10px; border-radius: 99px; margin-bottom: 20px;
        }
    </style>
</head>
<body>

    {{-- Left decorative panel --}}
    <div class="auth-left">
        @if($hqMode ?? false)
        {{-- HQ admin panel — no product marketing --}}
        <div>
            <div class="auth-brand">
                <img src="{{ asset('images/nexovadesklogo.png') }}" alt="Nexova Desk" class="auth-brand-logo">
                <div>
                    <div class="auth-brand-name">Nexova Desk</div>
                    <div class="auth-brand-sub">Panel de Administración HQ</div>
                </div>
            </div>

            <div style="margin-top:48px">
                <div class="auth-tagline">Sistema de<br>gestión <span>central</span></div>
                <div style="margin-top:16px;font-size:13.5px;color:rgba(255,255,255,.45);line-height:1.7">
                    Acceso exclusivo para administradores del sistema Nexova Desk.
                </div>
            </div>

            <div class="auth-features" style="margin-top:36px">
                <div class="auth-feat">
                    <div class="auth-feat-dot">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="auth-feat-text"><strong>Gestión de organizaciones</strong> — control total de cuentas y planes</div>
                </div>
                <div class="auth-feat">
                    <div class="auth-feat-dot">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="auth-feat-text"><strong>Configuración global</strong> — API keys, métodos de pago, canales</div>
                </div>
                <div class="auth-feat">
                    <div class="auth-feat-dot">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="auth-feat-text"><strong>Transacciones</strong> — revisión y confirmación de pagos</div>
                </div>
                <div class="auth-feat">
                    <div class="auth-feat-dot">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="auth-feat-text"><strong>Blog y páginas</strong> — contenido público del sitio</div>
                </div>
            </div>
        </div>
        @else
        {{-- Partner Edition — solo branding, sin marketing --}}
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;text-align:center">
            <img src="{{ asset('images/nexovadesklogo.png') }}" alt="Nexova Desk"
                 style="width:80px;height:80px;border-radius:16px;object-fit:contain;margin-bottom:20px">
            <div style="font-size:22px;font-weight:800;color:#fff;letter-spacing:-.03em">Nexova Desk</div>
            <div style="font-size:12px;color:#22c55e;font-weight:600;letter-spacing:.07em;text-transform:uppercase;margin-top:4px">
                by Nexova Digital Solutions
            </div>
        </div>
        @endif

        <div class="auth-footer-note">© {{ date('Y') }} Nexova Digital Solutions. Todos los derechos reservados.</div>
    </div>

    {{-- Right form panel --}}
    <div class="auth-right">
        <div class="auth-box">
            <div class="auth-logo-mobile">
                <img src="{{ asset('images/nexovadesklogo.png') }}" alt="Nexova Desk" style="height:48px;width:48px;border-radius:8px;object-fit:contain">
                <div class="auth-logo-mobile-name">Nexova Desk</div>
            </div>

            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>
</html>
