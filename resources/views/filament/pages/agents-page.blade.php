<x-filament-panels::page>
<style>
.ag-wrap         { display:flex; flex-direction:column; gap:28px; max-width:900px; padding:32px 36px 64px; }
.ag-section      { background:var(--c-surface,#fff); border:1px solid var(--c-border,#e3e6ea); border-radius:12px; overflow:hidden; }
.ag-section-head { padding:18px 22px; border-bottom:1px solid var(--c-border,#e3e6ea); display:flex; align-items:center; justify-content:space-between; gap:12px; }
.ag-section-title{ font-size:14px; font-weight:700; color:var(--c-text,#111827); margin:0; }
.ag-section-sub  { font-size:12px; color:var(--c-sub,#6b7280); margin:3px 0 0; }
.ag-table        { width:100%; border-collapse:collapse; }
.ag-table th     { padding:10px 18px; font-size:11px; font-weight:700; color:var(--c-sub,#6b7280); text-transform:uppercase; letter-spacing:.06em; text-align:left; border-bottom:1px solid var(--c-border,#e3e6ea); }
.ag-table td     { padding:14px 18px; font-size:13px; color:var(--c-text,#111827); border-bottom:1px solid var(--c-border,#e3e6ea); vertical-align:middle; }
.ag-table tr:last-child td { border-bottom:none; }
.ag-avatar       { width:34px; height:34px; border-radius:50%; object-fit:cover; background:var(--c-surf2,#f0f2f5); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:var(--c-sub,#6b7280); flex-shrink:0; }
.ag-name-cell    { display:flex; align-items:center; gap:10px; }
.ag-role-pill    { display:inline-flex; align-items:center; padding:2px 9px; border-radius:99px; font-size:11px; font-weight:600; }
.ag-role-owner   { background:#fdf4ff; color:#a21caf; border:1px solid #f0abfc; }
.ag-role-admin   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.ag-role-agent   { background:#f8fafc; color:#475569; border:1px solid #e2e8f0; }
.ag-actions      { display:flex; align-items:center; gap:6px; justify-content:flex-end; }
.ag-btn          { display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:7px; font-size:12px; font-weight:500; border:1px solid var(--c-border,#e3e6ea); background:transparent; color:var(--c-text,#111827); cursor:pointer; transition:background .12s; }
.ag-btn:hover    { background:var(--c-surf2,#f0f2f5); }
.ag-btn-danger   { color:#ef4444; border-color:#fecaca; }
.ag-btn-danger:hover { background:#fff5f5; }
.ag-empty        { padding:32px; text-align:center; color:var(--c-sub,#6b7280); font-size:13px; }

/* Invite form */
.ag-invite-form  { padding:20px 22px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; }
.ag-field        { display:flex; flex-direction:column; gap:5px; }
.ag-label        { font-size:11px; font-weight:700; color:var(--c-sub,#6b7280); text-transform:uppercase; letter-spacing:.05em; }
.ag-input        { height:38px; padding:0 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; background:var(--c-bg,#fff); color:var(--c-text,#111827); font-size:13px; outline:none; min-width:220px; transition:border-color .12s; }
.ag-input:focus  { border-color:#3b82f6; }
.ag-select       { height:38px; padding:0 10px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; background:var(--c-bg,#fff); color:var(--c-text,#111827); font-size:13px; outline:none; cursor:pointer; }
.ag-submit-btn   { height:38px; padding:0 18px; background:#1e293b; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; transition:background .12s; white-space:nowrap; }
.ag-submit-btn:hover { background:#0f172a; }
.ag-error        { font-size:12px; color:#ef4444; padding:0 22px 14px; }

/* Permissions modal */
.ag-modal-backdrop { position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:9000;display:flex;align-items:center;justify-content:center; }
.ag-modal        { background:var(--c-surface,#fff);border:1px solid var(--c-border,#e3e6ea);border-radius:14px;padding:28px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.15); }
.ag-modal h3     { margin:0 0 20px;font-size:16px;font-weight:700;color:var(--c-text,#111827); }
.ag-perm-list    { display:flex;flex-direction:column;gap:2px;margin-bottom:22px; }
.ag-perm-row     { display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--c-border,#e3e6ea); }
.ag-perm-row:last-child { border-bottom:none; }
.ag-perm-label   { font-size:13px;color:var(--c-text,#111827); }
.ag-toggle       { position:relative;width:38px;height:21px;cursor:pointer; }
.ag-toggle input { opacity:0;width:0;height:0;position:absolute; }
.ag-toggle-track { position:absolute;inset:0;background:var(--c-border,#e3e6ea);border-radius:99px;transition:background .15s; }
.ag-toggle input:checked + .ag-toggle-track { background:#3b82f6; }
.ag-toggle-thumb { position:absolute;top:3px;left:3px;width:15px;height:15px;background:#fff;border-radius:50%;transition:transform .15s;box-shadow:0 1px 3px rgba(0,0,0,.2); }
.ag-toggle input:checked ~ .ag-toggle-thumb { transform:translateX(17px); }
.ag-modal-actions { display:flex;gap:8px;justify-content:flex-end; }
.ag-modal-cancel { padding:8px 16px;border:1px solid var(--c-border,#e3e6ea);border-radius:8px;background:transparent;color:var(--c-text,#111827);font-size:13px;font-weight:500;cursor:pointer; }
.ag-modal-save   { padding:8px 18px;background:#1e293b;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer; }

/* Pending token block */
.ag-token-cell   { font-size:11px;font-family:monospace;color:var(--c-sub,#6b7280); }
.ag-expire-note  { font-size:11px;color:var(--c-sub,#6b7280); }
</style>

<h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin:32px 36px 0;letter-spacing:-.02em">Agentes</h1>
<div class="ag-wrap"
     x-data="{ showPerms: false }"
     @open-permissions-modal.window="showPerms = true"
     @close-permissions-modal.window="showPerms = false">

    {{-- ── Current agents ── --}}
    <div class="ag-section">
        <div class="ag-section-head">
            <div>
                <p class="ag-section-title">Agentes del equipo</p>
                <p class="ag-section-sub">Miembros con acceso al panel de soporte</p>
            </div>
        </div>

        @php $agents = $this->getAgents(); @endphp

        @if($agents->isEmpty())
            <div class="ag-empty">No hay agentes en tu organización todavía. Invita a tu equipo abajo.</div>
        @else
            <table class="ag-table">
                <thead>
                    <tr>
                        <th>Agente</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agents as $agent)
                    <tr>
                        <td>
                            <div class="ag-name-cell">
                                @if($agent->avatar_path)
                                    <img src="{{ asset('storage/' . $agent->avatar_path) }}" class="ag-avatar" alt="">
                                @else
                                    <div class="ag-avatar">{{ strtoupper(substr($agent->name, 0, 1)) }}</div>
                                @endif
                                <span>{{ $agent->name }}</span>
                            </div>
                        </td>
                        <td>{{ $agent->email }}</td>
                        <td>
                            <span class="ag-role-pill ag-role-{{ $agent->role }}">
                                {{ match($agent->role) { 'owner' => 'Propietario', 'admin' => 'Admin', default => 'Agente' } }}
                            </span>
                        </td>
                        <td>
                            <div class="ag-actions">
                                <button class="ag-btn" wire:click="openPermissions({{ $agent->id }})">
                                    Permisos
                                </button>
                                <button class="ag-btn ag-btn-danger"
                                        wire:click="removeAgent({{ $agent->id }})"
                                        wire:confirm="¿Remover a {{ $agent->name }} del equipo?">
                                    Remover
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ── Invite form ── --}}
    <div class="ag-section">
        <div class="ag-section-head">
            <div>
                <p class="ag-section-title">Invitar agente</p>
                <p class="ag-section-sub">Se enviará un enlace de registro por email</p>
            </div>
        </div>
        <form wire:submit="sendInvitation" class="ag-invite-form">
            <div class="ag-field">
                <label class="ag-label">Email</label>
                <input type="email"
                       wire:model="inviteEmail"
                       class="ag-input"
                       placeholder="agente@empresa.com"
                       required>
            </div>
            <div class="ag-field">
                <label class="ag-label">Rol</label>
                <select wire:model="inviteRole" class="ag-select">
                    <option value="agent">Agente</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <button type="submit" class="ag-submit-btn" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="sendInvitation">Enviar invitación</span>
                <span wire:loading wire:target="sendInvitation">Enviando...</span>
            </button>
        </form>
        @error('inviteEmail')
            <p class="ag-error">{{ $message }}</p>
        @enderror
    </div>

    {{-- ── Pending invitations ── --}}
    @php $pending = $this->getPendingInvitations(); @endphp
    @if($pending->isNotEmpty())
    <div class="ag-section">
        <div class="ag-section-head">
            <div>
                <p class="ag-section-title">Invitaciones pendientes</p>
                <p class="ag-section-sub">{{ $pending->count() }} esperando aceptación</p>
            </div>
        </div>
        <table class="ag-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Expira</th>
                    <th style="text-align:right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pending as $inv)
                <tr>
                    <td>{{ $inv->email }}</td>
                    <td>
                        <span class="ag-role-pill ag-role-{{ $inv->role }}">
                            {{ $inv->role === 'admin' ? 'Admin' : 'Agente' }}
                        </span>
                    </td>
                    <td class="ag-expire-note">{{ $inv->expires_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="ag-actions">
                            <button class="ag-btn ag-btn-danger"
                                    wire:click="revokeInvitation({{ $inv->id }})"
                                    wire:confirm="¿Revocar esta invitación?">
                                Revocar
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ── Permissions modal ── --}}
    <div class="ag-modal-backdrop" x-show="showPerms" x-cloak @click.self="showPerms=false" style="display:none">
        <div class="ag-modal" @click.stop>
            <h3>Permisos del agente</h3>
            <div class="ag-perm-list">
                @php
                $permLabels = [
                    'view_inbox'          => 'Ver bandeja de entrada',
                    'reply_messages'      => 'Responder mensajes',
                    'close_tickets'       => 'Cerrar conversaciones',
                    'assign_tickets'      => 'Asignar conversaciones',
                    'view_knowledge_base' => 'Ver base de conocimiento',
                    'edit_knowledge_base' => 'Editar base de conocimiento',
                    'view_reports'        => 'Ver reportes',
                ];
                @endphp
                @foreach($permLabels as $key => $label)
                <div class="ag-perm-row">
                    <span class="ag-perm-label">{{ $label }}</span>
                    <label class="ag-toggle">
                        <input type="checkbox" wire:model="editPermissions.{{ $key }}">
                        <div class="ag-toggle-track"></div>
                        <div class="ag-toggle-thumb"></div>
                    </label>
                </div>
                @endforeach
            </div>
            <div class="ag-modal-actions">
                <button class="ag-modal-cancel" @click="showPerms=false">Cancelar</button>
                <button class="ag-modal-save" wire:click="savePermissions">Guardar permisos</button>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
