<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>¿Cómo fue tu experiencia? — {{ $org->name ?? 'NexovaDesk' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@php $accent = $org->accent_color ?? '#7c3aed'; @endphp
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --accent: {{ $accent }};
    --accent-light: {{ $accent }}18;
    --accent-mid: {{ $accent }}40;
  }

  body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    color: #1e293b;
    padding: 24px 16px;
    gap: 0;
  }

  /* ── Card ── */
  .card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 8px 40px rgba(0,0,0,.09), 0 1px 2px rgba(0,0,0,.05);
    width: 100%;
    max-width: 440px;
    overflow: hidden;
  }

  /* ── Header ── */
  .card-header {
    background: #0f172a;
    padding: 28px 32px 26px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  .card-header::before {
    content: '';
    position: absolute;
    top: -60px; left: 50%;
    transform: translateX(-50%);
    width: 280px; height: 280px;
    background: radial-gradient(circle, var(--accent)30 0%, transparent 70%);
    pointer-events: none;
  }
  .org-name {
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -.01em;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    position: relative;
  }
  .org-dot {
    width: 7px; height: 7px;
    background: var(--accent);
    border-radius: 50%;
    box-shadow: 0 0 0 3px var(--accent-mid);
    animation: dot-pulse 2s ease-in-out infinite;
  }
  @keyframes dot-pulse {
    0%, 100% { box-shadow: 0 0 0 3px var(--accent-mid); }
    50%       { box-shadow: 0 0 0 6px var(--accent)20; }
  }
  .card-header h1 {
    font-size: 19px;
    font-weight: 800;
    color: #fff;
    line-height: 1.3;
    position: relative;
    letter-spacing: -.02em;
  }
  .card-header p {
    font-size: 13px;
    color: rgba(255,255,255,.5);
    margin-top: 5px;
    position: relative;
  }
  .ticket-ref {
    display: inline-block;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    color: rgba(255,255,255,.75);
    border-radius: 99px;
    padding: 3px 12px;
    font-size: 11px;
    font-weight: 700;
    font-family: ui-monospace, monospace;
    margin-top: 12px;
    position: relative;
    letter-spacing: .04em;
  }

  /* ── Body ── */
  .card-body { padding: 28px 32px 24px; }

  /* ── Stars ── */
  .stars-label {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 14px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: .07em;
  }
  .stars-row {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-bottom: 10px;
  }
  .nx-star {
    font-size: 0;
    cursor: pointer;
    padding: 4px;
    background: none;
    border: none;
    transition: transform .15s;
    display: inline-flex;
    align-items: center;
  }
  .nx-star svg {
    width: 34px; height: 34px;
    transition: fill .12s, stroke .12s, filter .12s, transform .12s;
  }
  .nx-star:hover { transform: scale(1.15); }
  .nx-star.active svg { fill: #fbbf24; stroke: #f59e0b; filter: drop-shadow(0 2px 6px #fbbf2455); }
  .nx-star.inactive svg { fill: #e2e8f0; stroke: #cbd5e1; }

  .stars-hint {
    text-align: center;
    font-size: 12px;
    font-weight: 500;
    color: #94a3b8;
    margin-bottom: 22px;
    min-height: 18px;
    transition: color .15s;
  }
  .stars-hint.filled { color: var(--accent); }

  /* ── Textarea ── */
  .form-group { margin-bottom: 18px; }
  .form-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .06em;
  }
  .form-textarea {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 13px;
    font-size: 13px;
    font-family: inherit;
    color: #1e293b;
    resize: vertical;
    min-height: 88px;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    background: #f8fafc;
    line-height: 1.55;
  }
  .form-textarea:focus {
    border-color: var(--accent);
    background: #fff;
    box-shadow: 0 0 0 3px var(--accent-light);
  }
  .form-textarea::placeholder { color: #94a3b8; }

  /* ── Submit ── */
  .btn-submit {
    width: 100%;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 13px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: filter .15s, transform .1s, opacity .15s;
    letter-spacing: -.01em;
  }
  .btn-submit:hover:not(:disabled) { filter: brightness(1.1); transform: translateY(-1px); }
  .btn-submit:active:not(:disabled) { transform: translateY(0); }
  .btn-submit:disabled { opacity: .4; cursor: default; }

  /* ── Thank you state ── */
  .state-box { text-align: center; padding: 36px 20px 32px; }
  .state-icon-wrap {
    width: 68px; height: 68px;
    border-radius: 50%;
    background: var(--accent-light);
    border: 2px solid var(--accent-mid);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px;
  }
  .state-icon-wrap svg { width: 32px; height: 32px; color: var(--accent); }
  .state-box h2 {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
    letter-spacing: -.02em;
  }
  .state-box .state-sub { font-size: 13px; color: #64748b; line-height: 1.6; }
  .state-stars { font-size: 22px; margin-top: 10px; display: block; }
  .state-comment {
    margin-top: 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 12.5px;
    color: #475569;
    font-style: italic;
    line-height: 1.5;
    text-align: left;
  }

  /* ── Footer ── */
  .card-footer {
    padding: 11px 32px 13px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    font-size: 11px;
    color: #94a3b8;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    line-height: 1.8;
  }
  .card-footer a { color: #64748b; text-decoration: none; font-weight: 500; transition: color .15s; }
  .card-footer a:hover { color: var(--accent); }
  .card-footer .sep { color: #cbd5e1; }
</style>
</head>
<body>

<div class="card" x-data="{
    rating: {{ $preRating > 0 ? $preRating : 0 }},
    hovered: 0,
    labels: ['', 'Muy insatisfecho', 'Insatisfecho', 'Neutral', 'Satisfecho', 'Excelente'],
    get hint() {
        const active = this.hovered || this.rating;
        return active ? this.labels[active] : 'Selecciona una calificación';
    },
    get hintFilled() { return !!(this.hovered || this.rating); }
}">

  {{-- ── Header ── --}}
  <div class="card-header">
    <div class="org-name">
      <div class="org-dot"></div>
      {{ $org->name ?? 'Soporte' }}
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

  {{-- ── Body ── --}}
  <div class="card-body">

    {{-- Submitted / already state --}}
    @if(session('submitted') || $ticket->survey_responded_at)
      <div class="state-box">

        {{-- Professional icon based on rating --}}
        @php $r = $ticket->survey_rating ?? 0; @endphp
        <div class="state-icon-wrap">
          @if($r >= 4)
            {{-- Thumbs up / checkmark for high ratings --}}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3z"/>
              <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
            </svg>
          @elseif($r >= 3)
            {{-- Neutral / check --}}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
          @else
            {{-- Low rating: message/feedback icon --}}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
          @endif
        </div>

        <h2>
          @php
            $labels = ['', 'Muy insatisfecho', 'Insatisfecho', 'Neutral', 'Satisfecho', '¡Excelente!'];
          @endphp
          {{ $labels[$r] ?? 'Registrado' }}
        </h2>

        <p class="state-sub">
          Tu calificación ha sido registrada. ¡Gracias!
          <span class="state-stars">{{ str_repeat('⭐', $r) }}</span>
        </p>

        @if($ticket->survey_comment)
          <div class="state-comment">"{{ $ticket->survey_comment }}"</div>
        @endif
      </div>

    {{-- Survey form --}}
    @else
      <form method="POST" action="{{ route('survey.submit', $ticket->ticket_reply_token) }}">
        @csrf

        <div style="margin-bottom:22px">
          <div class="stars-label">Califica tu experiencia</div>

          <div class="stars-row">
            @for($i = 1; $i <= 5; $i++)
              <button type="button"
                class="nx-star"
                :class="(hovered || rating) >= {{ $i }} ? 'active' : 'inactive'"
                @mouseenter="hovered = {{ $i }}"
                @mouseleave="hovered = 0"
                @click="rating = {{ $i }}"
                title="{{ ['', 'Muy insatisfecho', 'Insatisfecho', 'Neutral', 'Satisfecho', 'Excelente'][$i] }}">
                <svg viewBox="0 0 24 24" stroke-width="1.5">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
              </button>
            @endfor
          </div>

          <div class="stars-hint" :class="hintFilled ? 'filled' : ''" x-text="hint"></div>
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

  {{-- ── Footer ── --}}
  <div class="card-footer">
    <span>Ticket #{{ $ticket->ticket_number }}</span>
    <span class="sep">·</span>
    <span>
      Powered by
      <a href="https://nexovadesk.com" target="_blank" rel="noopener">NexovaDesk</a>
      <span class="sep">·</span>
      <a href="https://nexova-digital.com" target="_blank" rel="noopener">Nexova Digital Solutions</a>
    </span>
  </div>

</div>

<script src="//unpkg.com/alpinejs@3" defer></script>
</body>
</html>
