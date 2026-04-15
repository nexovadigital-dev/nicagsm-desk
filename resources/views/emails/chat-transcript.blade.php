<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transcripción de conversación</title>
<style>
  body { margin:0;padding:0;background:#f5f6f8;font-family:"Inter",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#1f2937; }
  *{ box-sizing:border-box; }
  .wrap{ max-width:600px;margin:32px auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.09); }
  .header{ padding:28px 32px 24px; }
  .header-inner{ display:flex;align-items:center;gap:14px; }
  .header-avatar{ width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.22);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#fff;flex-shrink:0; }
  .header h1{ color:#fff;margin:0 0 3px;font-size:18px;font-weight:700;line-height:1.3; }
  .header p{ color:rgba(255,255,255,.8);margin:0;font-size:12.5px; }
  .body{ padding:28px 32px; }
  .greeting{ font-size:15px;color:#111827;margin:0 0 6px;font-weight:600; }
  .subtext{ font-size:13px;color:#6b7280;margin:0 0 22px;line-height:1.55; }
  .meta-row{ display:flex;gap:16px;flex-wrap:wrap;margin-bottom:22px; }
  .meta-pill{ background:#f3f4f6;border-radius:99px;padding:4px 12px;font-size:11px;font-weight:600;color:#6b7280;white-space:nowrap; }
  .meta-pill span{ color:#374151; }
  .msg-user{ display:flex;justify-content:flex-end;margin-bottom:10px; }
  .bubble-user{ border-radius:16px 16px 3px 16px;padding:10px 14px;font-size:13px;line-height:1.55;max-width:78%;color:#fff; }
  .bubble-user .msg-time{ font-size:10px;color:rgba(255,255,255,.65);text-align:right;margin-top:4px; }
  .msg-bot{ display:flex;align-items:flex-end;gap:8px;margin-bottom:10px; }
  .bot-avatar{ width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0; }
  .bubble-bot{ background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px 16px 16px 3px;padding:10px 14px;font-size:13px;line-height:1.55;color:#1f2937;max-width:78%; }
  .bubble-bot .msg-time{ font-size:10px;color:#9ca3af;margin-top:4px; }
  .sender-badge{ font-size:10px;color:#9ca3af;font-weight:600;margin-bottom:3px;text-transform:uppercase;letter-spacing:.04em; }
  .msg-system{ text-align:center;margin:12px 0; }
  .system-pill{ display:inline-block;background:#f3f4f6;border-radius:99px;padding:3px 12px;font-size:10.5px;color:#6b7280; }
  .date-divider{ display:flex;align-items:center;gap:10px;margin:18px 0 14px; }
  .date-divider hr{ flex:1;border:none;border-top:1px solid #e5e7eb;margin:0; }
  .date-divider span{ font-size:10px;font-weight:700;color:#9ca3af;white-space:nowrap;text-transform:uppercase;letter-spacing:.06em; }
  .rating-box{ background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:16px 20px;margin-top:24px;text-align:center; }
  .rating-box p{ font-size:12px;color:#6b7280;margin:0 0 8px; }
  .stars{ font-size:22px;line-height:1;letter-spacing:2px;display:block; }
  .footer{ padding:16px 32px;background:#f9fafb;border-top:1px solid #f3f4f6;font-size:11px;color:#9ca3af;text-align:center;line-height:1.6; }
</style>
</head>
<body>
@php
  $ac = $ticket->widget?->accent_color ?? '#7c3aed';
  $botName = $ticket->widget?->bot_name ?? \App\Models\WidgetSetting::instance()->bot_name ?? 'Nexova IA';
  $clientName = $ticket->client_name ?: 'Visitante';
  $initial = strtoupper(mb_substr($botName, 0, 1));
  $firstMsg = $messages->first();
  $lastMsg  = $messages->last();
  $duration = null;
  if ($firstMsg && $lastMsg && $firstMsg->created_at && $lastMsg->created_at) {
      $diff = $firstMsg->created_at->diffInMinutes($lastMsg->created_at);
      $duration = $diff < 1 ? 'menos de 1 min' : "{$diff} min";
  }
  $currentDate = null;
@endphp
<div class="wrap">
  <div class="header" style="background:{{ $ac }}">
    <div class="header-inner">
      <div class="header-avatar">{{ $initial }}</div>
      <div>
        <h1>{{ $botName }}</h1>
        <p>Transcripción de tu conversación</p>
      </div>
    </div>
  </div>
  <div class="body">
    <p class="greeting">Hola {{ $clientName }},</p>
    <p class="subtext">A continuación el historial completo de tu conversación con {{ $botName }}.</p>
    <div class="meta-row">
      <div class="meta-pill">Mensajes: <span>{{ $messages->count() }}</span></div>
      @if($duration)<div class="meta-pill">Duración: <span>{{ $duration }}</span></div>@endif
      @if($firstMsg)<div class="meta-pill">Inicio: <span>{{ $firstMsg->created_at?->format('d/m/Y H:i') }}</span></div>@endif
    </div>
    @foreach($messages as $msg)
      @php
        $msgDate = $msg->created_at?->format('d \d\e F, Y');
        $msgTime = $msg->created_at?->format('H:i');
        $isUser  = $msg->sender_type === 'user';
        $isBot   = in_array($msg->sender_type, ['bot','agent']);
        $isSys   = $msg->sender_type === 'system';
      @endphp
      @if($msgDate !== $currentDate)
        @php $currentDate = $msgDate; @endphp
        <div class="date-divider"><hr/><span>{{ $msgDate }}</span><hr/></div>
      @endif
      @if($isSys)
        <div class="msg-system"><span class="system-pill">{{ $msg->content }}</span></div>
      @elseif($isUser)
        <div class="msg-user">
          <div class="bubble-user" style="background:{{ $ac }}">
            {{ $msg->content }}
            @if($msg->attachment_name)<div style="margin-top:5px;font-size:11px;opacity:.8;">📎 {{ $msg->attachment_name }}</div>@endif
            <div class="msg-time">{{ $msgTime }}</div>
          </div>
        </div>
      @elseif($isBot)
        <div class="msg-bot">
          <div class="bot-avatar" style="background:{{ $ac }}22;color:{{ $ac }}">{{ $initial }}</div>
          <div>
            <div class="sender-badge">{{ $msg->sender_type === 'agent' ? 'Agente' : $botName }}</div>
            <div class="bubble-bot">
              {!! nl2br(e($msg->content)) !!}
              @if($msg->attachment_name)<div style="margin-top:5px;font-size:11px;color:#9ca3af;">📎 {{ $msg->attachment_name }}</div>@endif
              <div class="msg-time">{{ $msgTime }}</div>
            </div>
          </div>
        </div>
      @endif
    @endforeach
    @if($ticket->rating)
      <div class="rating-box">
        <p>Tu calificación de esta conversación</p>
        <span class="stars">@for($i=1;$i<=5;$i++){{ $i<=$ticket->rating ? '⭐' : '☆' }}@endfor</span>
        @if($ticket->rating_comment)<p style="margin-top:8px;font-size:12px;color:#374151;font-style:italic;">"{{ $ticket->rating_comment }}"</p>@endif
      </div>
    @endif
  </div>
  <div class="footer">Esta transcripción fue enviada a solicitud del visitante.<br>NicaGSM · Nexova Desk</div>
</div>
</body>
</html>