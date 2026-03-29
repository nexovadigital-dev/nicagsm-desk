<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>¿Cómo fue tu experiencia? — Nexova Desk</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    color: #1e293b;
    padding: 24px;
  }
  .card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,.07);
    width: 100%;
    max-width: 460px;
    overflow: hidden;
  }
  .card-header {
    background: #111827;
    padding: 28px 32px 24px;
    text-align: center;
  }
  .logo {
    font-size: 15px;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.02em;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }
  .logo-dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; }
  .card-header h1 { font-size: 20px; font-weight: 700; color: #fff; line-height: 1.3; }
  .card-header p { font-size: 13px; color: rgba(255,255,255,.55); margin-top: 6px; }
  .ticket-ref {
    display: inline-block;
    background: rgba(255,255,255,.12);
    color: rgba(255,255,255,.8);
    border-radius: 99px;
    padding: 3px 12px;
    font-size: 11px;
    font-weight: 700;
    font-family: ui-monospace, monospace;
    margin-top: 10px;
  }

  .card-body { padding: 32px; }

  /* Stars */
  .stars-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 12px; text-align: center; }
  .stars-row {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-bottom: 8px;
  }
  .star-input { display: none; }
  .stars-row label {
    font-size: 36px;
    cursor: pointer;
    line-height: 1;
    filter: grayscale(1) opacity(.35);
    transition: filter .15s, transform .1s;
    user-select: none;
  }
  /* Filled state via CSS: when a radio is checked, fill that label and all before it */
  .star-input:checked ~ label,
  .stars-row:has(.star-input:checked) .star-input:checked ~ label { filter: none; }
  /* Trick: stars are rendered RTL visually but the DOM order is reversed */
  .stars-row { flex-direction: row-reverse; }
  .stars-row label:hover,
  .stars-row label:hover ~ label { filter: none; transform: scale(1.1); }

  /* Filled star when checked */
  .star-input:checked + label,
  .star-input:checked ~ label { filter: none; }

  /* Alpine-powered active class approach used below */
  .star-btn {
    font-size: 36px;
    cursor: pointer;
    line-height: 1;
    transition: transform .1s;
    background: none;
    border: none;
    padding: 0 2px;
  }
  .star-btn:hover { transform: scale(1.15); }

  .stars-hint { text-align: center; font-size: 11px; color: #94a3b8; margin-bottom: 20px; min-height: 16px; }

  /* Comment */
  .form-group { margin-bottom: 16px; }
  .form-label { display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; }
  .form-textarea {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 13px;
    font-family: inherit;
    color: #1e293b;
    resize: vertical;
    min-height: 90px;
    outline: none;
    transition: border-color .15s;
    background: #f8fafc;
  }
  .form-textarea:focus { border-color: #334155; background: #fff; }
  .form-textarea::placeholder { color: #94a3b8; }

  /* Submit */
  .btn-submit {
    width: 100%;
    background: #1e293b;
    color: #f8fafc;
    border: none;
    border-radius: 9px;
    padding: 13px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s;
  }
  .btn-submit:hover { background: #0f172a; }
  .btn-submit:disabled { background: #cbd5e1; cursor: default; }

  /* States */
  .state-box {
    text-align: center;
    padding: 40px 20px;
  }
  .state-icon { font-size: 48px; margin-bottom: 14px; }
  .state-box h2 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
  .state-box p { font-size: 13px; color: #64748b; line-height: 1.6; }

  /* Stars visual (Alpine approach) */
  .nx-star { font-size: 36px; cursor: pointer; padding: 0 2px; transition: transform .1s; display: inline-block; }
  .nx-star:hover { transform: scale(1.15); }
  .nx-star.active { filter: none; }
  .nx-star.inactive { filter: grayscale(1) opacity(.3); }
</style>
</head>
<body>
<div class="card">
  <div class="card-header">
    <div class="logo">
      <div class="logo-dot"></div>
      Nexova Desk
    </div>
    @if(session('submitted') || $ticket->survey_responded_at)
      <h1>¡Gracias por tu opinión!</h1>
      <p>Tu valoración nos ayuda a mejorar el servicio.</p>
    @elseif(session('already'))
      <h1>Ya registramos tu opinión</h1>
      <p>No puedes calificar dos veces el mismo ticket.</p>
    @else
      <h1>¿Cómo fue tu experiencia?</h1>
      <p>Tu opinión es muy importante para nosotros.</p>
    @endif
    <div class="ticket-ref">#{{ $ticket->ticket_number }}</div>
  </div>

  <div class="card-body">

    {{-- ── Already submitted ── --}}
    @if(session('submitted') || $ticket->survey_responded_at)
      <div class="state-box">
        <div class="state-icon">
          @php $r = $ticket->survey_rating; @endphp
          {{ $r >= 4 ? '🎉' : ($r >= 3 ? '👍' : '🙏') }}
        </div>
        <h2>
          @php
            $labels = ['', 'Muy insatisfecho', 'Insatisfecho', 'Neutral', 'Satisfecho', '¡Excelente!'];
          @endphp
          {{ $labels[$ticket->survey_rating] ?? 'Registrado' }}
        </h2>
        <p>Calificación: {{ str_repeat('⭐', $ticket->survey_rating ?? 0) }}<br>
        @if($ticket->survey_comment)
          <em style="margin-top:8px;display:block;">"{{ $ticket->survey_comment }}"</em>
        @endif
        </p>
      </div>

    {{-- ── Survey form ── --}}
    @else
      <form method="POST" action="{{ route('survey.submit', $ticket->ticket_reply_token) }}"
            x-data="{
              rating: {{ $preRating > 0 ? $preRating : 0 }},
              hovered: 0,
              labels: ['', 'Muy insatisfecho 😞', 'Insatisfecho 😕', 'Neutral 😐', 'Satisfecho 😊', '¡Excelente! 🎉'],
              get hint() { return this.hovered ? this.labels[this.hovered] : (this.rating ? this.labels[this.rating] : 'Selecciona una calificación'); }
            }">
        @csrf

        <div style="margin-bottom:20px">
          <div class="stars-label">Califica tu experiencia</div>

          {{-- Stars rendered LTR with Alpine --}}
          <div style="display:flex;justify-content:center;gap:4px;margin-bottom:8px">
            @for($i = 1; $i <= 5; $i++)
              <button type="button"
                class="nx-star"
                :class="(hovered || rating) >= {{ $i }} ? 'active' : 'inactive'"
                @mouseenter="hovered = {{ $i }}"
                @mouseleave="hovered = 0"
                @click="rating = {{ $i }}">⭐</button>
            @endfor
          </div>
          <div class="stars-hint" x-text="hint"></div>

          <input type="hidden" name="rating" :value="rating">
        </div>

        <div class="form-group">
          <label class="form-label">Comentario opcional</label>
          <textarea class="form-textarea" name="comment"
            placeholder="¿Qué podemos mejorar? ¿Hay algo que te haya gustado especialmente?"></textarea>
        </div>

        <button type="submit" class="btn-submit" :disabled="rating === 0">
          Enviar calificación
        </button>
      </form>
    @endif

  </div>
  <div style="padding:12px 32px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8;text-align:center">
    Ticket #{{ $ticket->ticket_number }}
    &nbsp;·&nbsp;
    Powered by <strong style="color:#64748b">Nexova Digital Solutions</strong>
  </div>
</div>

<script src="//unpkg.com/alpinejs@3" defer></script>
</body>
</html>
