<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f7; margin: 0; padding: 0; }
  .wrap { max-width: 560px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,.08); }
  .header { background: #7c3aed; padding: 28px 32px; }
  .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
  .header p  { color: rgba(255,255,255,.8); margin: 4px 0 0; font-size: 13px; }
  .body { padding: 28px 32px; }
  .greeting { font-size: 15px; color: #111; margin-bottom: 16px; }
  .msg-box { background: #f8f7ff; border-left: 3px solid #7c3aed; padding: 14px 16px; border-radius: 0 8px 8px 0; font-size: 14px; color: #374151; line-height: 1.6; }
  .meta { font-size: 12px; color: #9ca3af; margin-top: 6px; }
  .cta { margin-top: 24px; text-align: center; }
  .cta a { display: inline-block; background: #7c3aed; color: #fff; text-decoration: none; padding: 11px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; }
  .footer { padding: 16px 32px; background: #f9f9fb; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>{{ \App\Models\WidgetSetting::instance()->bot_name ?: 'Nexova Chat' }}</h1>
    <p>Tienes una nueva respuesta a tu consulta</p>
  </div>
  <div class="body">
    <p class="greeting">Hola {{ $ticket->client_name ?? 'Usuario' }},</p>
    <div class="msg-box">{{ $message->content }}</div>
    <p class="meta">
      {{ $message->sender_type === 'bot' ? 'Respuesta automática' : 'Respondido por un agente' }}
      · {{ $message->created_at->format('d/m/Y H:i') }}
    </p>
  </div>
  <div class="footer">
    Nexova Digital Solutions · Este email fue enviado porque tienes una consulta activa.
  </div>
</div>
</body>
</html>
