<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Respuesta a tu ticket</title>
@php
    $accentColor = $org->accent_color ?? '#7c3aed';
    $orgName     = $org->name ?? 'Soporte';
    $tz          = $org->timezone ?? 'America/Managua';
    $sentAt      = \Carbon\Carbon::parse($message->created_at)->setTimezone($tz)->format('d/m/Y H:i');
@endphp
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f7; margin: 0; padding: 0; }
  .wrap { max-width: 560px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,.08); }
  .header { background: {{ $accentColor }}; padding: 28px 32px; }
  .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
  .header p  { color: rgba(255,255,255,.8); margin: 4px 0 0; font-size: 13px; }
  .body { padding: 28px 32px; }
  .greeting { font-size: 15px; color: #111; margin-bottom: 16px; }
  .msg-box { background: #f8f7ff; border-left: 3px solid {{ $accentColor }}; padding: 14px 16px; border-radius: 0 8px 8px 0; font-size: 14px; color: #374151; line-height: 1.6; }
  .meta { font-size: 12px; color: #9ca3af; margin-top: 6px; }
  .footer { padding: 16px 32px; background: #f9f9fb; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>{{ $orgName }}</h1>
    <p>Tienes una nueva respuesta a tu consulta</p>
  </div>
  <div class="body">
    <p class="greeting">Hola {{ $ticket->client_name ?? 'Usuario' }},</p>
    <div class="msg-box">{{ $message->content }}</div>
    <p class="meta">
      {{ $message->sender_type === 'bot' ? 'Respuesta automática' : 'Respondido por un agente' }}
      · {{ $sentAt }}
    </p>
  </div>
  <div class="footer">
    {{ $orgName }} · Este email fue enviado porque tienes una consulta activa.
  </div>
</div>
</body>
</html>
