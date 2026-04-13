<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ticket #{{ $ticket->ticket_number }} resuelto</title>
@php
    $accentColor = $org->accent_color ?? '#7c3aed';
    $orgName     = $org->name ?? 'Soporte';
    $tz          = $org->timezone ?? 'America/Managua';
    $closedAt    = \Carbon\Carbon::now($tz)->format('d/m/Y H:i');
@endphp
<style>
  body { margin:0;padding:0;background:#f5f6f8;font-family:'Inter',-apple-system,sans-serif;color:#1f2937; }
  .wrap { max-width:580px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08); }
  .header { background:#111827;padding:28px 32px; }
  .header h1 { color:#fff;margin:0;font-size:20px;font-weight:700; }
  .header p  { color:rgba(255,255,255,.6);margin:6px 0 0;font-size:13px; }
  .body { padding:28px 32px; }
  .ticket-badge { display:inline-block;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;border-radius:99px;padding:4px 14px;font-size:12px;font-weight:700;margin-bottom:16px; }
  .field { margin-bottom:14px; }
  .field label { font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:4px; }
  .field p { font-size:14px;color:#111827;margin:0; }
  .divider { height:1px;background:#f3f4f6;margin:20px 0; }
  .survey-cta { text-align:center;padding:24px 0 8px; }
  .survey-cta p { font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6; }
  .stars { display:flex;justify-content:center;gap:8px;margin-bottom:20px; }
  .star-btn { display:inline-block;text-decoration:none;font-size:28px;line-height:1; }
  .survey-link { display:inline-block;background:#111827;color:#f8fafc;text-decoration:none;border-radius:8px;padding:11px 28px;font-size:14px;font-weight:600;letter-spacing:.01em; }
  .survey-link:hover { background:#0f172a; }
  .footer { padding:16px 32px;background:#f9fafb;border-top:1px solid #f3f4f6;font-size:11px;color:#9ca3af;text-align:center; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Tu ticket ha sido resuelto ✓</h1>
    <p>Gracias por contactarnos. Tu solicitud fue atendida.</p>
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
      <label>Fecha de cierre</label>
      <p>{{ $closedAt }}</p>
    </div>

    <div class="divider"></div>

    <div class="survey-cta">
      <p><strong>¿Cómo calificarías la atención que recibiste?</strong><br>
      Tu opinión nos ayuda a mejorar. Solo toma un segundo.</p>

      {{-- Quick-rate links (one click = direct rating) --}}
      <div class="stars">
        @for($i = 1; $i <= 5; $i++)
          <a href="{{ url('/survey/' . $ticket->ticket_reply_token . '?rating=' . $i) }}"
             class="star-btn" title="{{ $i }} estrella{{ $i > 1 ? 's' : '' }}">⭐</a>
        @endfor
      </div>

      <p style="font-size:12px;color:#9ca3af;margin:0 0 16px">O haz clic aquí para dejar un comentario:</p>
      <a href="{{ url('/survey/' . $ticket->ticket_reply_token) }}" class="survey-link">
        Dejar mi opinión
      </a>
    </div>
  </div>
  <div class="footer">
    Ticket {{ $ticket->ticket_number }} · {{ $orgName }} · Si tienes más dudas, abre un nuevo ticket
  </div>
</div>
</body>
</html>
