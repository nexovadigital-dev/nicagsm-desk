<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

.tk-page { display: flex; flex-direction: column; gap: 14px; padding: 20px 24px 48px; }

/* ── Toolbar ── */
.tk-toolbar {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 16px;
}
.tk-search-wrap { position: relative; flex: 1; min-width: 160px; }
.tk-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
.tk-search {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    color: #0f172a;
    font-size: 13px;
    padding: 7px 10px 7px 32px;
    outline: none; width: 100%; font-family: inherit;
    transition: border-color .15s;
}
.tk-search:focus { border-color: #334155; background: #fff; }
.tk-search::placeholder { color: #94a3b8; }
.tk-select {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    color: #0f172a;
    font-size: 12.5px;
    padding: 7px 10px;
    outline: none; font-family: inherit;
    transition: border-color .15s;
}
.tk-select:focus { border-color: #334155; background: #fff; }

/* ── New ticket button ── */
.tk-btn-new {
    display: inline-flex; align-items: center; gap: 6px;
    background: #1e293b; color: #f8fafc;
    border: none; border-radius: 8px;
    padding: 8px 14px; font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: inherit; white-space: nowrap;
    transition: background .15s;
}
.tk-btn-new:hover { background: #0f172a; }

/* ── Table ── */
.tk-table {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}
.tk-thead { background: #f9fafb; border-bottom: 1px solid #e2e8f0; }
.tk-thead tr th {
    padding: 10px 16px;
    font-size: 11px; font-weight: 600; color: #6b7280;
    text-transform: uppercase; letter-spacing: .05em;
    text-align: left; white-space: nowrap;
}
.tk-tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .1s; }
.tk-tbody tr:last-child { border-bottom: none; }
.tk-tbody tr:hover { background: #f9fafb; }
.tk-tbody td { padding: 11px 16px; font-size: 13px; color: #374151; vertical-align: middle; }

/* ── Badges ── */
.tk-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 8px; border-radius: 99px;
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .03em; white-space: nowrap;
}
/* Status */
.tk-badge-bot     { background: #eef2ff; color: #4338ca; }
.tk-badge-human   { background: #eff6ff; color: #1d4ed8; }
.tk-badge-closed  { background: #f8fafc; color: #475569; }
/* Platform */
.tk-badge-web      { background: #eff6ff; color: #1d4ed8; }
.tk-badge-email    { background: #f8fafc; color: #6b7280; }
.tk-badge-telegram { background: #eff6ff; color: #1d4ed8; }
.tk-badge-internal { background: #f8fafc; color: #6b7280; }
/* Priority */
.tk-badge-high   { background: #fef2f2; color: #dc2626; }
.tk-badge-medium { background: #fffbeb; color: #b45309; }
.tk-badge-low    { background: #f0fdf4; color: #16a34a; }
.tk-badge-normal { background: #f8fafc; color: #64748b; }

/* ── Avatar ── */
.tk-avatar {
    width: 30px; height: 30px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
}

/* ── Rating stars ── */
.tk-rating { display: inline-flex; align-items: center; gap: 2px; font-size: 13px; }
.tk-rating-star  { color: #f59e0b; }
.tk-rating-empty { color: #e2e8f0; }

/* ── Actions ── */
.tk-actions { display: flex; gap: 4px; align-items: center; }
.tk-icon-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 6px;
    border: 1px solid #e2e8f0; background: transparent;
    cursor: pointer; color: #64748b;
    transition: background .1s, color .1s, border-color .1s;
}
.tk-icon-btn:hover { background: #f8fafc; color: #0f172a; }
.tk-icon-btn.green:hover  { background: rgba(34,197,94,.08); color: #16a34a; border-color: rgba(34,197,94,.25); }
.tk-icon-btn.danger:hover { background: rgba(239,68,68,.07); color: #ef4444; border-color: rgba(239,68,68,.25); }

.tk-empty { text-align: center; padding: 60px 20px; color: #94a3b8; font-size: 14px; }
.tk-pagination { display: flex; justify-content: flex-end; padding: 10px 16px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; }

/* ── Modal ── */
.tk-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.4);
    z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}
.tk-modal {
    background: #ffffff;
    border-radius: 12px;
    width: 100%; max-width: 500px;
    box-shadow: 0 20px 60px rgba(0,0,0,.15);
    overflow: hidden;
    display: flex; flex-direction: column;
}
.tk-modal-header {
    padding: 18px 22px;
    border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: space-between;
}
.tk-modal-title { font-size: 15px; font-weight: 700; color: #0f172a; }
.tk-modal-close {
    background: none; border: none; cursor: pointer;
    color: #94a3b8; display: flex; padding: 4px;
    transition: color .12s;
}
.tk-modal-close:hover { color: #0f172a; }
.tk-modal-body {
    padding: 20px 22px;
    display: flex; flex-direction: column; gap: 14px;
    max-height: 60vh; overflow-y: auto;
}
.tk-modal-footer {
    padding: 14px 22px;
    border-top: 1px solid #e2e8f0;
    display: flex; justify-content: flex-end; gap: 8px;
}
.tk-label {
    font-size: 11px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .04em;
    display: block; margin-bottom: 5px;
}
.tk-input {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 11px;
    font-size: 13px; font-family: inherit;
    color: #0f172a; outline: none;
    background: #f8fafc;
    box-sizing: border-box;
    transition: border-color .15s, background .15s;
}
.tk-input:focus { border-color: #334155; background: #fff; }
.tk-input::placeholder { color: #94a3b8; }
.tk-textarea { resize: vertical; min-height: 72px; }
.tk-tab-row {
    display: flex; gap: 0;
    border: 1px solid #e2e8f0;
    border-radius: 8px; overflow: hidden; margin-bottom: 2px;
}
.tk-tab-btn {
    flex: 1; padding: 7px 10px;
    font-size: 12px; font-weight: 600;
    border: none; cursor: pointer; font-family: inherit;
    background: #f8fafc; color: #64748b;
    transition: background .12s, color .12s;
}
.tk-tab-btn.active { background: #1e293b; color: #f8fafc; }
.tk-contact-list {
    border: 1px solid #e2e8f0;
    border-radius: 8px; overflow: hidden; margin-top: 4px;
}
.tk-contact-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 12px; cursor: pointer;
    transition: background .1s;
    border-bottom: 1px solid #e2e8f0;
}
.tk-contact-item:last-child { border-bottom: none; }
.tk-contact-item:hover { background: rgba(30,41,59,.05); }
.tk-contact-item.selected { background: rgba(30,41,59,.08); }
.tk-btn-ghost {
    background: transparent;
    border: 1px solid #e2e8f0;
    border-radius: 8px; padding: 8px 16px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: inherit;
    color: #374151;
    transition: background .12s;
}
.tk-btn-ghost:hover { background: #f8fafc; }
.tk-btn-primary {
    background: #1e293b; color: #f8fafc;
    border: 1px solid #1e293b;
    border-radius: 8px; padding: 8px 18px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: inherit;
    transition: background .12s;
}
.tk-btn-primary:hover { background: #0f172a; }
.tk-btn-primary:disabled { background: #cbd5e1; border-color: #cbd5e1; cursor: default; }
</style>

@php
$tickets  = $this->tickets;
$palette  = ['#0ea5e9','#10b981','#f59e0b','#ef4444','#ec4899','#6366f1'];
$suggestions = $this->contactSuggestions;
@endphp

<div class="tk-page">

    {{-- ── Toolbar ── --}}
    <div class="tk-toolbar">
        <div class="tk-search-wrap">
            <svg class="tk-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" class="tk-search" wire:model.live.debounce.300ms="search" placeholder="Buscar por número, asunto, nombre, email...">
        </div>

        <select class="tk-select" wire:model.live="filterStatus">
            <option value="all">Todos los estados</option>
            <option value="bot">Bot</option>
            <option value="human">Con agente</option>
            <option value="closed">Cerrados</option>
        </select>

        <select class="tk-select" wire:model.live="filterPriority">
            <option value="all">Toda prioridad</option>
            <option value="high">Alta</option>
            <option value="medium">Media</option>
            <option value="low">Baja</option>
            <option value="normal">Normal</option>
        </select>

        <select class="tk-select" wire:model.live="filterPlatform">
            <option value="all">Todos los canales</option>
            <option value="web">Web</option>
            <option value="email">Email</option>
            <option value="telegram">Telegram</option>
        </select>

        <button class="tk-btn-new" wire:click="openNewModal">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Nuevo Ticket
        </button>
    </div>

    {{-- ── Table ── --}}
    <div class="tk-table">
        <table style="width:100%;border-collapse:collapse">
            <thead class="tk-thead">
                <tr>
                    <th>Ticket</th>
                    <th>Cliente</th>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Agente</th>
                    <th>Encuesta</th>
                    <th>Abierto</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="tk-tbody">
                @forelse($tickets as $ticket)
                @php
                    $color    = $palette[abs(crc32($ticket->client_name)) % count($palette)];
                    $priority = $ticket->priority ?? 'normal';
                    $rating   = $ticket->survey_rating;
                @endphp
                <tr>
                    <td>
                        <span style="font-family:ui-monospace,monospace;font-size:12px;font-weight:700;color:#334155">
                            {{ $ticket->ticket_number }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div class="tk-avatar" style="background:{{ $color }}">
                                {{ strtoupper(substr($ticket->client_name ?? 'V', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:#0f172a">{{ $ticket->client_name ?? 'Visitante' }}</div>
                                @if($ticket->client_email)
                                    <div style="font-size:11px;color:#64748b">{{ $ticket->client_email }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13px;color:#0f172a;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $ticket->ticket_subject ?? '—' }}
                        </div>
                    </td>
                    <td>
                        @php $statusLabel = ['bot'=>'Bot','human'=>'Agente','closed'=>'Cerrado'][$ticket->status] ?? $ticket->status; @endphp
                        <span class="tk-badge tk-badge-{{ $ticket->status }}">{{ $statusLabel }}</span>
                    </td>
                    <td>
                        <span class="tk-badge tk-badge-{{ $priority }}">{{ $priority }}</span>
                    </td>
                    <td>
                        @if($ticket->assigned_agent)
                            <span style="font-size:12px;color:#0f172a">{{ $ticket->assigned_agent }}</span>
                        @else
                            <span style="font-size:12px;color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td>
                        @if($rating)
                            <div class="tk-rating" title="{{ $ticket->survey_comment }}">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $rating ? 'tk-rating-star' : 'tk-rating-empty' }}">★</span>
                                @endfor
                                <span style="font-size:11px;color:#94a3b8;margin-left:3px">{{ $rating }}/5</span>
                            </div>
                        @elseif($ticket->status === 'closed' && $ticket->client_email)
                            <span style="font-size:11px;color:#94a3b8">Pendiente</span>
                        @else
                            <span style="font-size:11px;color:#cbd5e1">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:12px;color:#64748b">{{ ($ticket->ticket_opened_at ?? $ticket->created_at)->format('d/m/Y') }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ ($ticket->ticket_opened_at ?? $ticket->created_at)->diffForHumans() }}</div>
                    </td>
                    <td>
                        <div class="tk-actions">
                            @if($ticket->status !== 'closed')
                                <button class="tk-icon-btn green" wire:click="closeTicket({{ $ticket->id }})"
                                    wire:confirm="¿Marcar como resuelto? Se notificará al cliente por email." title="Cerrar ticket">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            @else
                                <button class="tk-icon-btn" wire:click="reopenTicket({{ $ticket->id }})" title="Reabrir ticket">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="tk-empty">
                    @if($search || $filterStatus !== 'all')
                        Sin resultados para los filtros aplicados.
                    @else
                        No hay tickets aún. Crea uno con el botón "Nuevo Ticket".
                    @endif
                </td></tr>
                @endforelse
            </tbody>
        </table>

        @if($tickets->hasPages())
        <div class="tk-pagination">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>

    <div style="font-size:11px;color:#94a3b8">
        Mostrando {{ $tickets->firstItem() ?? 0 }}–{{ $tickets->lastItem() ?? 0 }} de {{ $tickets->total() }} tickets
    </div>

</div>

{{-- ══════════════════════════════════════════
     MODAL — Nuevo ticket de soporte
══════════════════════════════════════════════ --}}
@if($showNewModal)
<div class="tk-overlay" wire:click.self="$set('showNewModal', false)">
    <div class="tk-modal">
        <div class="tk-modal-header">
            <span class="tk-modal-title">Nuevo ticket de soporte</span>
            <button class="tk-modal-close" wire:click="$set('showNewModal', false)" aria-label="Cerrar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="tk-modal-body">

            {{-- Contact mode tabs --}}
            <div>
                <label class="tk-label">Cliente</label>
                <div class="tk-tab-row">
                    <button type="button" class="tk-tab-btn {{ $newContactMode === 'existing' ? 'active' : '' }}"
                        wire:click="$set('newContactMode','existing')">Buscar contacto</button>
                    <button type="button" class="tk-tab-btn {{ $newContactMode === 'new' ? 'active' : '' }}"
                        wire:click="$set('newContactMode','new')">Nuevo contacto</button>
                </div>
            </div>

            @if($newContactMode === 'existing')
                {{-- Contact search --}}
                <div>
                    <input type="text" class="tk-input" wire:model.live.debounce.250ms="newContactSearch"
                        placeholder="Buscar por nombre, email o teléfono…">
                    @if($suggestions->isNotEmpty())
                        <div class="tk-contact-list">
                            @foreach($suggestions as $c)
                                <div class="tk-contact-item {{ $newContactId === $c->id ? 'selected' : '' }}"
                                     wire:click="selectContact({{ $c->id }})">
                                    @php $ci = strtoupper(substr($c->name ?? $c->email ?? '?', 0, 1)); $cc = $palette[abs(crc32($c->name ?? '')) % count($palette)]; @endphp
                                    <div class="tk-avatar" style="background:{{ $cc }};width:26px;height:26px;font-size:10px">{{ $ci }}</div>
                                    <div>
                                        <div style="font-size:13px;font-weight:600;color:#0f172a">{{ $c->name ?? '—' }}</div>
                                        <div style="font-size:11px;color:#64748b">{{ $c->email }} {{ $c->phone ? '· '.$c->phone : '' }}</div>
                                    </div>
                                    @if($newContactId === $c->id)
                                        <svg style="margin-left:auto;flex-shrink:0;color:#16a34a" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif(strlen($newContactSearch) >= 2)
                        <div style="font-size:12px;color:#94a3b8;padding:8px 2px">Sin resultados. Puedes crear un contacto nuevo en la otra pestaña.</div>
                    @endif
                </div>

            @else
                {{-- New contact form --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div>
                        <label class="tk-label">Nombre</label>
                        <input type="text" class="tk-input" wire:model="newClientName" placeholder="Juan Pérez">
                    </div>
                    <div>
                        <label class="tk-label">Teléfono</label>
                        <input type="text" class="tk-input" wire:model="newClientPhone" placeholder="+1 555 0000">
                    </div>
                </div>
                <div>
                    <label class="tk-label">Email <span style="color:#ef4444">*</span></label>
                    <input type="email" class="tk-input" wire:model="newClientEmail" placeholder="cliente@email.com">
                    <div style="font-size:10.5px;color:#94a3b8;margin-top:4px">Se enviará la confirmación del ticket a este correo.</div>
                </div>
            @endif

            {{-- Subject --}}
            <div>
                <label class="tk-label">Asunto <span style="color:#ef4444">*</span></label>
                <input type="text" class="tk-input" wire:model="newSubject" placeholder="Describe brevemente el problema…">
            </div>

            {{-- Initial message --}}
            <div>
                <label class="tk-label">Mensaje inicial (opcional)</label>
                <textarea class="tk-input tk-textarea" wire:model="newMessage"
                    placeholder="Escribe el primer mensaje del agente al cliente…"></textarea>
            </div>

        </div>
        <div class="tk-modal-footer">
            <button class="tk-btn-ghost" wire:click="$set('showNewModal', false)">Cancelar</button>
            <button class="tk-btn-primary" wire:click="createTicket"
                @if(!trim($newSubject)) disabled @endif>
                Crear ticket y enviar email
            </button>
        </div>
    </div>
</div>
@endif

</x-filament-panels::page>
