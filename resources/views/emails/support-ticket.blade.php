<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ticket #{{ $ticket->ticket_number }}</title>
@php
    $accentColor = $org->accent_color ?? '#7c3aed';
    $orgName     = $org->name ?? 'Soporte';
    $tz          = $org->timezone ?? 'America/Managua';
    $openedAt    = $ticket->ticket_opened_at
        ? \Carbon\Carbon::parse($ticket->ticket_opened_at)->setTimezone($tz)->format('d/m/Y H:i')
        : \Carbon\Carbon::now($tz)->format('d/m/Y H:i');
@endphp
<style>
  body { margin: 0; padding: 0; background: #f5f6f8; font-family: 'Inter', -apple-system, sans-serif; color: #1f2937; }
  .wrap { max-width: 580px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
  .header { background: {{ $accentColor }}; padding: 28px 32px; }
  .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
  .header p { color: rgba(255,255,255,.8); margin: 6px 0 0; font-size: 13px; }
  .body { padding: 28px 32px; }
  .ticket-badge { display: inline-block; background: #f3f0ff; color: {{ $accentColor }}; border: 1px solid #ddd6fe; border-radius: 99px; padding: 4px 14px; font-size: 12px; font-weight: 700; margin-bottom: 16px; }
  .field { margin-bottom: 14px; }
  .field label { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .06em; display: block; margin-bottom: 4px; }
  .field p { font-size: 14px; color: #111827; margin: 0; }
  .divider { height: 1px; background: #f3f4f6; margin: 20px 0; }
  .notice { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px 16px; font-size: 13px; color: #4b5563; line-height: 1.6; }
  .notice strong { color: #111827; }
  .footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #f3f4f6; font-size: 11px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Tu ticket ha sido creado</h1>
    <p>Nuestro equipo revisará tu solicitud a la brevedad.</p>
  </div>
  <div class="body">
    <span class="ticket-badge">#{{ $ticket->ticket_number }}</span>

    <div class="field">
      <label>Asunto</label>
      <p>{{ $ticket->ticket_subject }}</p>
    </div>

    @if($ticket->client_name && $ticket->client_name !== 'Visitante')
    <div class="field">
      <label>Nombre</label>
      <p>{{ $ticket->client_name }}</p>
    </div>
    @endif

    <div class="field">
      <label>Estado</label>
      <p>Abierto</p>
    </div>

    <div class="field">
      <label>Fecha de apertura</label>
      <p>{{ $openedAt }}</p>
    </div>

    <div class="divider"></div>

    <div class="notice">
      <strong>¿Tienes más información que agregar?</strong><br>
      Puedes responder directamente a este correo y tu mensaje se añadirá al ticket. Asegúrate de no eliminar el asunto del correo al responder.
    </div>
  </div>
    @include('emails._email-footer', ['orgName' => $orgName, 'org' => $org, 'ticket' => $ticket, 'footerNote' => 'Gestión de Soporte'])
  </div>
</div>
</body>
</html>
