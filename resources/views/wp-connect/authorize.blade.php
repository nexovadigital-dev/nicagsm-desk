<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conectar con Nexova Desk</title>
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
        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            background: #22c55e;
            border-radius: 12px;
            margin-bottom: 18px;
        }
        .logo svg { color: #fff; }
        h1 { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 8px; }
        p  { font-size: 13.5px; color: #6b7280; line-height: 1.6; margin-bottom: 0; }
        .origin {
            display: inline-block;
            background: #f3f4f6;
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 12.5px;
            color: #374151;
            font-weight: 500;
            margin: 4px 0 16px;
        }
        .user-row {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px;
            margin: 20px 0;
            text-align: left;
        }
        .avatar {
            width: 38px;
            height: 38px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }
        .user-info strong { display: block; font-size: 13.5px; color: #111827; }
        .user-info span   { font-size: 12px; color: #6b7280; }
        .btn {
            display: block;
            width: 100%;
            padding: 11px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            text-decoration: none;
            text-align: center;
        }
        .btn-primary  { background: #22c55e; color: #fff; margin-bottom: 10px; }
        .btn-primary:hover { background: #16a34a; }
        .btn-ghost    { background: transparent; color: #6b7280; border: 1px solid #e5e7eb; }
        .btn-ghost:hover { background: #f3f4f6; }
        .divider { font-size: 12px; color: #9ca3af; margin: 16px 0; }
        .login-link { font-size: 13px; color: #6b7280; margin-top: 16px; }
        .login-link a { color: #22c55e; text-decoration: none; font-weight: 500; }
        .error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.8" width="26" height="26">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
    </div>

    <h1>Conectar con Nexova Desk</h1>
    <p>Autoriza la conexión desde tu tienda:</p>

    @if($origin)
        <span class="origin">{{ $origin }}</span>
    @endif

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    @if($isLoggedIn)
        <p>Conectarás esta tienda usando tu cuenta:</p>

        <div class="user-row">
            <div class="avatar">{{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}</div>
            <div class="user-info">
                <strong>{{ $user->name ?? $user->email }}</strong>
                <span>{{ $user->email }}</span>
            </div>
        </div>

        <form method="POST" action="{{ url('/wp-connect/authorize') }}">
            @csrf
            <input type="hidden" name="origin" value="{{ $origin }}">
            <button type="submit" class="btn btn-primary">
                Autorizar conexión
            </button>
        </form>

        <div class="divider">o</div>

        <form method="POST" action="{{ route('auth.logout') }}" style="margin-bottom:0">
            @csrf
            <button type="submit" class="btn btn-ghost">
                Iniciar sesión con otra cuenta
            </button>
        </form>
    @else
        <p style="margin-bottom:20px;">Inicia sesión en Nexova Desk para continuar.</p>

        <a href="{{ route('auth.login') }}?redirect={{ urlencode(url()->current() . '?' . http_build_query(['origin' => $origin])) }}"
           class="btn btn-primary">
            Iniciar sesión
        </a>
    @endif
</div>
</body>
</html>
