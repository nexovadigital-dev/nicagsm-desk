@php
    $tickets = $contact->tickets()->orderByDesc('created_at')->get();
    $statusLabel = ['bot' => 'Bot', 'human' => 'Agente', 'closed' => 'Cerrado', 'open' => 'Abierto'];
    $statusColor = ['bot' => '#3b82f6', 'human' => '#22c55e', 'closed' => '#9ca3af', 'open' => '#f59e0b'];
    $sourceLabel = ['woocommerce' => 'WooCommerce', 'pre_chat' => 'Pre-chat', 'widget' => 'Widget', 'manual' => 'Manual'];
    $orgTz = auth()->user()?->organization?->timezone ?? 'UTC';
@endphp

<style>
.cx-modal { font-family: 'Inter', system-ui, sans-serif; }
.cx-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
.cx-field { display: flex; flex-direction: column; gap: 2px; }
.cx-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
.cx-val   { font-size: 13.5px; color: #111827; font-weight: 500; word-break: break-word; }
.cx-badge { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 99px; font-size: 11.5px; font-weight: 600; }
.cx-section { margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; }
.cx-section:last-child { border-bottom: none; padding-bottom: 0; }
.cx-section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6366f1; margin-bottom: 12px; }
.cx-tk-row { display: grid; grid-template-columns: 1fr auto auto auto; gap: 8px; align-items: center; padding: 10px 12px; border: 1px solid #f1f5f9; border-radius: 8px; margin-bottom: 6px; background: #fafafa; }
.cx-tk-name { font-size: 13px; font-weight: 600; color: #1e293b; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cx-tk-sub  { font-size: 11px; color: #94a3b8; margin-top: 1px; }
.cx-tk-date { font-size: 11px; color: #94a3b8; white-space: nowrap; }
.cx-stars   { color: #f59e0b; font-size: 13px; }
.cx-empty   { text-align: center; padding: 20px; color: #94a3b8; font-size: 13px; }
@media (max-width: 600px) {
    .cx-grid { grid-template-columns: 1fr; }
    .cx-tk-row { grid-template-columns: 1fr auto; }
    .cx-tk-date, .cx-stars { display: none; }
}
</style>

<div class="cx-modal">

    {{-- Identity section --}}
    <div class="cx-section">
        <div class="cx-section-title">Informacion del contacto</div>
        <div class="cx-grid">
            <div class="cx-field">
                <span class="cx-label">Nombre</span>
                <span class="cx-val">{{ $contact->name ?: '—' }}</span>
            </div>
            <div class="cx-field">
                <span class="cx-label">Email</span>
                <span class="cx-val">{{ $contact->email ?: '—' }}</span>
            </div>
            <div class="cx-field">
                <span class="cx-label">Telefono</span>
                <span class="cx-val">{{ $contact->phone ?: '—' }}</span>
            </div>
            <div class="cx-field">
                <span class="cx-label">Origen</span>
                <span class="cx-badge" style="background:#f1f5f9;color:#374151">
                    {{ $sourceLabel[$contact->source] ?? $contact->source }}
                </span>
            </div>
            <div class="cx-field">
                <span class="cx-label">Registro</span>
                <span class="cx-val">{{ $contact->created_at->setTimezone($orgTz)->format('d M Y, H:i') }}</span>
            </div>
            <div class="cx-field">
                <span class="cx-label">Ultima visita</span>
                <span class="cx-val">{{ $contact->last_seen_at ? $contact->last_seen_at->setTimezone($orgTz)->format('d M Y, H:i') : '—' }}</span>
            </div>
            @if($contact->woo_customer_id)
            <div class="cx-field">
                <span class="cx-label">WooCommerce ID</span>
                <span class="cx-val">{{ $contact->woo_customer_id }}</span>
            </div>
            @endif
            @if($contact->notes)
            <div class="cx-field" style="grid-column: 1 / -1">
                <span class="cx-label">Notas internas</span>
                <span class="cx-val">{{ $contact->notes }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Conversations section --}}
    <div class="cx-section">
        <div class="cx-section-title">
            Historial de conversaciones
            <span style="font-size:11px;color:#94a3b8;font-weight:500;text-transform:none;letter-spacing:0">
                ({{ $tickets->count() }} ticket{{ $tickets->count() != 1 ? 's' : '' }})
            </span>
        </div>

        @if($tickets->isEmpty())
            <div class="cx-empty">Este contacto no tiene conversaciones registradas.</div>
        @else
            @foreach($tickets as $tk)
            @php
                $sc = $statusColor[$tk->status] ?? '#9ca3af';
                $sl = $statusLabel[$tk->status] ?? $tk->status;
                $rating = $tk->survey_rating;
            @endphp
            <div class="cx-tk-row">
                <div>
                    <div class="cx-tk-name">{{ $tk->conversation_name ?: 'Conversación #'.$tk->id }}</div>
                    <div class="cx-tk-sub">{{ ucfirst($tk->platform ?? 'widget') }} · TKT-{{ str_pad($tk->id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>
                <span class="cx-badge" style="background:{{ $sc }}22;color:{{ $sc }};border:1px solid {{ $sc }}44">
                    {{ $sl }}
                </span>
                @if($rating)
                    <span class="cx-stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5-$rating) }}</span>
                @else
                    <span class="cx-tk-date" style="color:#d1d5db">—</span>
                @endif
                <span class="cx-tk-date">{{ $tk->created_at->setTimezone($orgTz)->format('d M Y') }}</span>
            </div>
            @endforeach
        @endif
    </div>

</div>
