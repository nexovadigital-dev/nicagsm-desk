@php
    $contact     = $record;
    $tickets     = $contact->tickets()->orderByDesc('created_at')->get();
    $statusLabel = ['bot' => 'Bot activo', 'human' => 'Con agente', 'closed' => 'Cerrada', 'open' => 'Abierta'];
    $statusColor = ['bot' => '#3b82f6', 'human' => '#22c55e', 'closed' => '#9ca3af', 'open' => '#f59e0b'];
    $sourceLabel = ['woocommerce' => 'WooCommerce', 'pre_chat' => 'Formulario', 'widget' => 'Chat', 'manual' => 'Manual'];
    $sourceBg    = ['woocommerce' => '#dcfce7', 'pre_chat' => '#dbeafe', 'widget' => '#f1f5f9', 'manual' => '#fef9c3'];
    $sourceFg    = ['woocommerce' => '#15803d', 'pre_chat' => '#1d4ed8', 'widget' => '#475569', 'manual' => '#92400e'];
    $orgTz       = auth()->user()?->organization?->timezone ?? 'UTC';
    $initial     = strtoupper(mb_substr($contact->name ?? $contact->email ?? '?', 0, 1));

    // Fecha relativa amigable
    $lastSeen = $contact->last_seen_at;
    $lastSeenText = $lastSeen
        ? ($lastSeen->isToday()    ? 'Hoy, '      . $lastSeen->setTimezone($orgTz)->format('H:i')
          : ($lastSeen->isYesterday() ? 'Ayer, '  . $lastSeen->setTimezone($orgTz)->format('H:i')
          : $lastSeen->setTimezone($orgTz)->format('d M Y, H:i')))
        : 'Nunca';
@endphp

<style>
.cx { font-family:'Inter',ui-sans-serif,system-ui,sans-serif; font-size:13px; padding-top:4px; }

/* Header */
.cx-header { display:flex; align-items:center; gap:12px; padding-bottom:12px; border-bottom:1px solid #f1f5f9; margin-bottom:12px; }
.cx-avatar  { width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center;
              font-size:18px; font-weight:700; color:#fff; flex-shrink:0;
              background:linear-gradient(135deg,#22c55e,#16a34a); overflow:hidden; }
.cx-avatar img { width:44px; height:44px; object-fit:cover; }
.cx-name    { font-size:15px; font-weight:700; color:#0f172a; }
.cx-pill    { display:inline-flex; align-items:center; padding:1px 9px; border-radius:99px; font-size:11px; font-weight:600; margin-top:4px; }

/* Stats */
.cx-stats   { display:grid; grid-template-columns:repeat(3,1fr); gap:7px; margin-bottom:12px; }
.cx-stat    { background:#f8fafc; border-radius:9px; padding:8px 10px; text-align:center; }
.cx-stat-n  { font-size:18px; font-weight:700; color:#22c55e; line-height:1; }
.cx-stat-l  { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#94a3b8; margin-top:2px; }

/* Info rows */
.cx-info    { display:flex; flex-direction:column; gap:5px; margin-bottom:12px; }
.cx-row     { display:flex; align-items:center; gap:9px; padding:7px 10px; background:#f8fafc; border-radius:8px; }
.cx-row-ico { font-size:14px; flex-shrink:0; }
.cx-row-body { min-width:0; flex:1; display:flex; flex-direction:column; }
.cx-row-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; }
.cx-row-val   { font-size:12.5px; font-weight:500; color:#1e293b; word-break:break-all; }

/* Notes */
.cx-notes { background:#fffbeb; border-left:3px solid #fbbf24; border-radius:8px; padding:8px 11px; margin-bottom:12px; }
.cx-notes .cx-row-label { color:#92400e; }
.cx-notes .cx-row-val   { color:#78350f; }

/* Tickets */
.cx-tk-header { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.06em;
                color:#6366f1; display:flex; align-items:center; gap:7px; margin-bottom:8px; }
.cx-tk-count  { background:#e0e7ff; color:#4338ca; font-size:10px; font-weight:700;
                padding:1px 7px; border-radius:99px; text-transform:none; letter-spacing:0; }
.cx-tk-list   { }
.cx-tk        { display:flex; align-items:center; justify-content:space-between; gap:8px;
                padding:7px 10px; border:1px solid #e2e8f0; border-radius:8px; margin-bottom:5px; background:#fff; }
.cx-tk-l      { flex:1; min-width:0; }
.cx-tk-r      { display:flex; flex-direction:column; align-items:flex-end; gap:3px; flex-shrink:0; }
.cx-tk-name   { font-size:12.5px; font-weight:600; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; }
.cx-tk-date   { font-size:11px; color:#94a3b8; margin-top:1px; }
.cx-tk-badge  { display:inline-flex; align-items:center; padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; white-space:nowrap; }
.cx-stars     { color:#f59e0b; font-size:11px; }
.cx-empty     { text-align:center; padding:16px 0; color:#94a3b8; font-size:12px; }

@media (max-width:480px) {
    .cx-stats { grid-template-columns:1fr 1fr; }
    .cx-tk-list { max-height:200px; }
}
</style>

<div class="cx">

    {{-- Header --}}
    <div class="cx-header">
        <div class="cx-avatar">
            @if($contact->avatar_url)
                <img src="{{ $contact->avatar_url }}" alt="{{ $initial }}">
            @else
                {{ $initial }}
            @endif
        </div>
        <div>
            <div class="cx-name">{{ $contact->display_name }}</div>
            <span class="cx-pill"
                  style="background:{{ $sourceBg[$contact->source] ?? '#f1f5f9' }};color:{{ $sourceFg[$contact->source] ?? '#475569' }}">
                {{ $sourceLabel[$contact->source] ?? ucfirst($contact->source) }}
            </span>
        </div>
    </div>

    {{-- Stats --}}
    <div class="cx-stats">
        <div class="cx-stat">
            <div class="cx-stat-n">{{ $contact->total_conversations ?? 0 }}</div>
            <div class="cx-stat-l">Chats</div>
        </div>
        <div class="cx-stat">
            <div class="cx-stat-n" style="font-size:14px;color:#64748b;">
                {{ $contact->created_at->setTimezone($orgTz)->format('M Y') }}
            </div>
            <div class="cx-stat-l">Cliente desde</div>
        </div>
        <div class="cx-stat">
            <div class="cx-stat-n" style="font-size:14px;color:#64748b;">
                {{ $tickets->where('survey_rating', '>', 0)->avg('survey_rating') ? number_format($tickets->where('survey_rating','>',0)->avg('survey_rating'),1) : '—' }}
            </div>
            <div class="cx-stat-l">CSAT prom.</div>
        </div>
    </div>

    {{-- Contact info rows --}}
    <div class="cx-info">
        @if($contact->email)
        <div class="cx-row">
            <span class="cx-row-ico">✉️</span>
            <div class="cx-row-body">
                <div class="cx-row-label">Email</div>
                <div class="cx-row-val">{{ $contact->email }}</div>
            </div>
        </div>
        @endif

        @if($contact->phone)
        <div class="cx-row">
            <span class="cx-row-ico">📞</span>
            <div class="cx-row-body">
                <div class="cx-row-label">Teléfono</div>
                <div class="cx-row-val">{{ $contact->phone }}</div>
            </div>
        </div>
        @endif

        <div class="cx-row">
            <span class="cx-row-ico">🕐</span>
            <div class="cx-row-body">
                <div class="cx-row-label">Última visita</div>
                <div class="cx-row-val">{{ $lastSeenText }}</div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($contact->notes)
    <div class="cx-notes">
        <div class="cx-row-label">📝 Notas internas</div>
        <div class="cx-row-val" style="margin-top:4px;">{{ $contact->notes }}</div>
    </div>
    @endif

    {{-- Conversation history --}}
    <div class="cx-tk-header">
        Conversaciones
        <span class="cx-tk-count">{{ $tickets->count() }}</span>
    </div>

    @if($tickets->isEmpty())
        <div class="cx-empty">Sin conversaciones registradas aún.</div>
    @else
    <div class="cx-tk-list">
        @foreach($tickets as $tk)
        @php
            $sc = $statusColor[$tk->status] ?? '#9ca3af';
            $sl = $statusLabel[$tk->status] ?? ucfirst($tk->status);
            $rt = $tk->survey_rating;
        @endphp
        <div class="cx-tk">
            <div class="cx-tk-l">
                <div class="cx-tk-name">{{ $tk->conversation_name ?: 'Conversación #'.$tk->id }}</div>
                <div class="cx-tk-date">
                    {{ $tk->created_at->setTimezone($orgTz)->format('d M Y') }}
                    @if($rt) · <span class="cx-stars">{{ str_repeat('★',$rt) }}{{ str_repeat('☆',5-$rt) }}</span> @endif
                </div>
            </div>
            <div class="cx-tk-r">
                <span class="cx-tk-badge"
                      style="background:{{ $sc }}18;color:{{ $sc }};border:1px solid {{ $sc }}33;">
                    {{ $sl }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
