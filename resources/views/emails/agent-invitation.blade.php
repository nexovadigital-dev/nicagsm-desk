<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invitación al equipo</title>
<style>
  body { margin: 0; padding: 0; background: #f5f6f8; font-family: 'Inter', -apple-system, sans-serif; color: #1f2937; }
  .wrap { max-width: 580px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
  .header { background: #111827; padding: 28px 32px; display: flex; align-items: center; gap: 12px; }
  .header-logo { font-size: 18px; font-weight: 800; color: #fff; letter-spacing: -.5px; }
  .header-logo span { color: #22c55e; }
  .body { padding: 32px; }
  .org-badge { display: inline-block; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; border-radius: 99px; padding: 4px 14px; font-size: 12px; font-weight: 700; margin-bottom: 20px; }
  .role-badge { display: inline-block; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 99px; padding: 3px 10px; font-size: 11px; font-weight: 600; margin-left: 6px; text-transform: capitalize; }
  h2 { margin: 0 0 10px; font-size: 20px; color: #111827; }
  p { margin: 0 0 16px; font-size: 14px; color: #4b5563; line-height: 1.6; }
  .cta-btn { display: block; width: fit-content; background: #22c55e; color: #fff; text-decoration: none; padding: 13px 28px; border-radius: 8px; font-size: 14px; font-weight: 700; margin: 24px 0; }
  .divider { height: 1px; background: #f3f4f6; margin: 24px 0; }
  .link-fallback { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; font-size: 12px; color: #6b7280; word-break: break-all; }
  .link-fallback strong { color: #111827; display: block; margin-bottom: 6px; }
  .footer { display:none }
  .expire-note { font-size: 12px; color: #9ca3af; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="header-logo">Nexova<span>Desk</span></div>
  </div>
  <div class="body">
    <span class="org-badge">{{ $invitation->organization->name ?? 'Nexova Desk' }}</span>

    <h2>Te han invitado a unirte al equipo</h2>
    <p>
      <strong>{{ $invitation->invitedBy->name ?? 'Un administrador' }}</strong> te ha invitado a colaborar
      en <strong>{{ $invitation->organization->name ?? 'Nexova Desk' }}</strong>
      como <span class="role-badge">{{ $invitation->role === 'admin' ? 'Administrador' : 'Agente' }}</span>.
    </p>
    <p>Acepta la invitación para crear tu cuenta y acceder al panel de soporte.</p>

    <a href="{{ url('/invitation/' . $invitation->token) }}" class="cta-btn">Aceptar invitación</a>

    <p class="expire-note">Esta invitación expira el {{ $invitation->expires_at->format('d/m/Y') }}.</p>

    <div class="divider"></div>
    <div class="link-fallback">
      <strong>Si el botón no funciona, copia este enlace:</strong>
      {{ url('/invitation/' . $invitation->token) }}
    </div>
  </div>
@php $org = $invitation->organization; $orgName = $org->name ?? 'Nexova Desk'; @endphp
    @include('emails._email-footer', ['orgName' => $orgName, 'org' => $org, 'footerNote' => 'Gestión de Soporte'])
  </div>
</div>
</body>
</html>
