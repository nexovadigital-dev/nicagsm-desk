<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ticket #{{ $ticket->ticket_number }} — Caso cerrado</title>
@php
    $accentColor = $org->accent_color ?? '#7c3aed';
    $orgName     = $org->name ?? 'Soporte';
    $tz          = $org->timezone ?? 'America/Managua';
@endphp
<style>
  body { margin:0;padding:0;background:#f5f6f8;font-family:'Inter',-apple-system,sans-serif;color:#1f2937; }
  .wrap { max-width:580px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08); }
  .header { background:#111827;padding:28px 32px; }
  .header h1 { color:#fff;margin:0;font-size:20px;font-weight:700; }
  .header p  { color:rgba(255,255,255,.6);margin:6px 0 0;font-size:13px; }
  .body { padding:28px 32px; }
  .ticket-badge { display:inline-block;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;border-radius:99px;padding:4px 14px;font-size:12px;font-weight:700;margin-bottom:16px; }
  .notice-box { background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:16px 20px;margin-bottom:20px; }
  .notice-box p { margin:0;font-size:14px;color:#92400e;line-height:1.6; }
  .notice-box strong { color:#78350f; }
  .steps { margin:0 0 20px 0;padding:0; }
  .steps li { font-size:13.5px;color:#374151;line-height:1.7;margin-bottom:6px; }
  .steps li span { font-weight:700;color:{{ $accentColor }}; }
  .divider { height:1px;background:#f3f4f6;margin:20px 0; }
  .field { margin-bottom:14px; }
  .field label { font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:4px; }
  .field p { font-size:14px;color:#111827;margin:0; }
  .footer { padding:16px 32px;background:#f9fafb;border-top:1px solid #f3f4f6;font-size:11px;color:#9ca3af;text-align:center; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Este caso ya fue cerrado 🔒</h1>
    <p>Tu respuesta no pudo procesarse porque el ticket está resuelto.</p>
  </div>
  <div class="body">
    <span class="ticket-badge">#{{ $ticket->ticket_number }}</span>

    <div class="notice-box">
      <p>
        <strong>Tu mensaje no fue registrado.</strong><br>
        El ticket <strong>#{{ $ticket->ticket_number }}</strong> —
        "{{ $ticket->ticket_subject }}" — ya fue
        marcado como <strong>resuelto</strong> y no puede recibir más respuestas.
      </p>
    </div>

    <p style="font-size:14px;color:#374151;line-height:1.65;margin:0 0 16px;">
      Si necesitas ayuda adicional, te pedimos que <strong>crees un nuevo ticket de soporte</strong>.
      Esto nos permitirá atenderte correctamente.
    </p>

    <ol class="steps">
      <li>Envía un nuevo correo a nuestro equipo de soporte.</li>
      <li>
        Si tu consulta está relacionada a este caso, por favor
        <span>incluye en el asunto o cuerpo del mensaje:</span><br>
        <em style="color:#374151;font-size:13px;">
          "Necesito asistencia relacionada con el ticket #{{ $ticket->ticket_number }}"
        </em>
      </li>
      <li>Nuestro equipo revisará el historial y te ayudará de inmediato.</li>
    </ol>

    <div class="divider"></div>

    <div class="field">
      <label>Ticket de referencia</label>
      <p>#{{ $ticket->ticket_number }} — {{ $ticket->ticket_subject }}</p>
    </div>

    @if($ticket->client_name && $ticket->client_name !== 'Visitante')
    <div class="field">
      <label>Cliente</label>
      <p>{{ $ticket->client_name }}</p>
    </div>
    @endif

    <p style="font-size:12.5px;color:#6b7280;margin:20px 0 0;line-height:1.6;">
      ¿Tienes dudas sobre este proceso? Responde a este correo y un agente
      te orientará para abrir el nuevo ticket correctamente.
    </p>
  </div>
  <div class="footer">
    Ticket {{ $ticket->ticket_number }} · {{ $orgName }} · Este mensaje es automático — no respondas directamente a este correo para reabrir el caso.
  </div>
</div>
</body>
</html>
