@php
    // Filament pasa el record como $record en modalContent
    $contact     = $record;
    $tickets     = $contact->tickets()->orderByDesc('created_at')->get();
    $statusLabel = ['bot' => 'Bot', 'human' => 'Agente', 'closed' => 'Cerrado', 'open' => 'Abierto'];
    $statusColor = ['bot' => '#3b82f6', 'human' => '#22c55e', 'closed' => '#9ca3af', 'open' => '#f59e0b'];
    $sourceLabel = ['woocommerce' => 'WooCommerce', 'pre_chat' => 'Pre-chat', 'widget' => 'Widget', 'manual' => 'Manual'];
    $sourceBg    = ['woocommerce' => '#dcfce7', 'pre_chat' => '#dbeafe', 'widget' => '#f1f5f9', 'manual' => '#fef9c3'];
    $sourceFg    = ['woocommerce' => '#15803d', 'pre_chat' => '#1d4ed8', 'widget' => '#475569', 'manual' => '#92400e'];
    $orgTz       = auth()->user()?->organization?->timezone ?? 'UTC';
    $initial     = strtoupper(mb_substr($contact->name ?? $contact->email ?? '?', 0, 1));
@endphp

<style>
.cx-modal { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; padding-bottom: 4px; }

/* Header */
.cx-header { display: flex; align-items: center; gap: 14px; padding: 4px 0 18px; border-bottom: 1px solid #f1f5f9; margin-bottom: 18px; }
.cx-avatar { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
             font-size: 20px; font-weight: 700; color: #fff; flex-shrink: 0;
             background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
.cx-avatar img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; }
.cx-header-name { font-size: 17px; font-weight: 700; color: #0f172a; line-height: 1.2; }
.cx-badge { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; margin-top: 5px; }

/* Info grid */
.cx-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px; }
.cx-field { background: #f8fafc; border-radius: 9px; padding: 10px 12px; min-width: 0; }
.cx-field-full { grid-column: 1 / -1; }
.cx-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 3px; }
.cx-val   { font-size: 13px; color: #1e293b; font-weight: 500; word-break: break-all; line-height: 1.4; }
.cx-val-big { font-size: 22px; font-weight: 700; color: #22c55e; }

/* Notes */
.cx-notes { background: #fffbeb; border-left: 3px solid #fbbf24; border-radius: 8px; padding: 10px 12px; margin-bottom: 18px; }
.cx-notes .cx-label { color: #92400e; }
.cx-notes .cx-val   { color: #78350f; }

/* Section title */
.cx-section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em;
                    color: #6366f1; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
.cx-count { background: #e0e7ff; color: #4338ca; font-size: 10px; font-weight: 700;
            padding: 1px 7px; border-radius: 99px; text-transform: none; letter-spacing: 0; }

/* Ticket rows */
.cx-tk-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;
             padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 9px; margin-bottom: 7px; background: #fff; flex-wrap: wrap; }
.cx-tk-left  { flex: 1; min-width: 0; }
.cx-tk-right { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
.cx-tk-name  { font-size: 13px; font-weight: 600; color: #1e293b; line-height: 1.3; }
.cx-tk-sub   { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.cx-tk-date  { font-size: 11px; color: #94a3b8; white-space: nowrap; }
.cx-stars    { color: #f59e0b; font-size: 12px; white-space: nowrap; }
.cx-st-badge { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 99px; font-size: 11px; font-weight: 600; }

.cx-empty { text-align: center; padding: 20px 0; color: #94a3b8; font-size: 13px; }

@media (max-width: 480px) {
    .cx-grid { grid-template-columns: 1fr; }
    .cx-tk-date { display: none; }
}
</style>

<div class="cx-modal">

    {{-- ── Header ── --}}
    <div class="cx-header">
        <div class="cx-avatar">
            @if($contact->avatar_url)
                <img src="{{ $contact->avatar_url }}" alt="{{ $initial }}">
            @else
                {{ $initial }}
            @endif
        </div>
        <div>
            <div class="cx-header-name">{{ $contact->display_name }}</div>
            <span class="cx-badge"
                  style="background:{{ $sourceBg[$contact->source] ?? '#f1f5f9' }};color:{{ $sourceFg[$contact->source] ?? '#475569' }}">
                {{ $sourceLabel[$contact->source] ?? $contact->source }}
            </span>
        </div>
    </div>

    {{-- ── Info cards ── --}}
    <div class="cx-grid">
        @if($contact->email)
        <div class="cx-field">
            <div class="cx-label">Email</div>
            <div class="cx-val">{{ $contact->email }}</div>
        </div>
        @endif

        <div class="cx-field">
            <div class="cx-label">Teléfono</div>
            <div class="cx-val">{{ $contact->phone ?: '—' }}</div>
        </div>

        <div class="cx-field">
            <div class="cx-label">Conversaciones</div>
            <div class="cx-val cx-val-big">{{ $contact->total_conversations ?? 0 }}</div>
        </div>

        <div class="cx-field">
            <div class="cx-label">Última visita</div>
            <div class="cx-val">
                {{ $contact->last_seen_at ? $contact->last_seen_at->setTimezone($orgTz)->format('d M Y, H:i') : '—' }}
            </div>
        </div>

        <div class="cx-field">
            <div class="cx-label">Registro</div>
            <div class="cx-val">{{ $contact->created_at->setTimezone($orgTz)->format('d M Y') }}</div>
        </div>

    </div>

    {{-- ── Notas ── --}}
    @if($contact->notes)
    <div class="cx-notes">
        <div class="cx-label">Notas internas</div>
        <div class="cx-val" style="margin-top:3px;">{{ $contact->notes }}</div>
    </div>
    @endif

    {{-- ── Historial de tickets ── --}}
    <div class="cx-section-title">
        Historial de conversaciones
        <span class="cx-count">{{ $tickets->count() }}</span>
    </div>

    @if($tickets->isEmpty())
        <div class="cx-empty">Sin conversaciones registradas.</div>
    @else
        @foreach($tickets as $tk)
        @php
            $sc = $statusColor[$tk->status] ?? '#9ca3af';
            $sl = $statusLabel[$tk->status] ?? $tk->status;
            $rt = $tk->survey_rating;
        @endphp
        <div class="cx-tk-row">
            <div class="cx-tk-left">
                <div class="cx-tk-name">{{ $tk->conversation_name ?: 'Conversación #'.$tk->id }}</div>
                <div class="cx-tk-sub">
                    {{ ucfirst($tk->platform ?? 'widget') }} · TKT-{{ str_pad($tk->id, 4, '0', STR_PAD_LEFT) }}
                </div>
            </div>
            <div class="cx-tk-right">
                <span class="cx-st-badge"
                      style="background:{{ $sc }}22;color:{{ $sc }};border:1px solid {{ $sc }}44;">
                    {{ $sl }}
                </span>
                @if($rt)
                    <span class="cx-stars">{{ str_repeat('★', $rt) }}{{ str_repeat('☆', 5-$rt) }}</span>
                @endif
                <span class="cx-tk-date">{{ $tk->created_at->setTimezone($orgTz)->format('d M Y') }}</span>
            </div>
        </div>
        @endforeach
    @endif

</div>
