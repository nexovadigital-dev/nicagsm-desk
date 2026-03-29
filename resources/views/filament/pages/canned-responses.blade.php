<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

.cr-wrap { padding: 32px 36px 64px; }

/* 2-col: form left (sticky) | list right */
.cr-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
    align-items: start;
    max-width: 1100px;
}
@media (max-width: 780px) {
    .cr-layout { grid-template-columns: 1fr; }
    .cr-sidebar { position: static !important; }
}

.cr-sidebar {
    position: sticky;
    top: 16px;
}

.cr-card {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 10px;
    padding: 18px 20px;
}
.cr-card-title {
    font-size: 12px; font-weight: 700; color: var(--c-sub,#6b7280);
    text-transform: uppercase; letter-spacing: .06em;
    margin-bottom: 16px; display: flex; align-items: center; gap: 6px;
}
.cr-card-title svg { color: #64748b; flex-shrink: 0; }

.cr-field { display: flex; flex-direction: column; gap: 4px; margin-bottom: 12px; }
.cr-label { font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .05em; }

.cr-input, .cr-textarea {
    background: var(--c-bg,#f5f6f8);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 7px; color: var(--c-text,#111);
    font-size: 13px; padding: 8px 11px; outline: none;
    width: 100%; font-family: inherit; transition: border-color .12s; box-sizing: border-box;
}
.cr-input:focus, .cr-textarea:focus { border-color: #3b82f6; }
.cr-textarea { resize: vertical; min-height: 80px; line-height: 1.5; }
.cr-input::placeholder, .cr-textarea::placeholder { color: var(--c-sub); }

.cr-shortcut-wrap { position: relative; }
.cr-shortcut-prefix {
    position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
    color: #3b82f6; font-weight: 700; font-size: 14px; pointer-events: none;
}
.cr-shortcut-wrap .cr-input { padding-left: 22px; }

.cr-hint { font-size: 11px; color: var(--c-sub,#6b7280); line-height: 1.5; margin-top: 10px; }

.cr-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 13px; border-radius: 7px; font-size: 12.5px; font-weight: 500;
    cursor: pointer; border: 1px solid transparent; font-family: inherit; transition: background .1s;
}
.cr-btn-primary { background: #1e293b; color:#f8fafc; }
.cr-btn-primary:hover { background: #0f172a; }
.cr-btn-ghost { background: transparent; color: var(--c-sub,#6b7280); border-color: var(--c-border,#e3e6ea); }
.cr-btn-ghost:hover { background: var(--c-surf2,#f0f2f5); }
.cr-btn-danger { background: transparent; color: #ef4444; border-color: rgba(239,68,68,.2); }
.cr-btn-danger:hover { background: rgba(239,68,68,.06); }
.cr-btn-sm { padding: 5px 10px; font-size: 12px; }
.cr-btn-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--c-border,#e3e6ea);
    background: transparent; cursor: pointer; color: var(--c-sub,#6b7280);
    transition: background .1s, color .1s;
}
.cr-btn-icon:hover { background: var(--c-surf2,#f0f2f5); color: var(--c-text,#111); }
.cr-btn-icon.danger:hover { background: rgba(239,68,68,.07); color: #ef4444; border-color: rgba(239,68,68,.25); }

/* Lista */
.cr-search-wrap { position: relative; }
.cr-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--c-sub,#9ca3af); pointer-events: none; }
.cr-search {
    background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 7px; color: var(--c-text,#111); font-size: 13px;
    padding: 8px 11px 8px 32px; outline: none; width: 100%; font-family: inherit; box-sizing: border-box;
}
.cr-search:focus { border-color: #3b82f6; }

.cr-list { display: flex; flex-direction: column; }
.cr-row {
    padding: 12px 0;
    border-bottom: 1px solid var(--c-border,#e3e6ea);
}
.cr-row:last-child { border-bottom: none; }
.cr-row-main {
    display: flex; align-items: flex-start; gap: 12px;
}
.cr-shortcut-badge {
    display: inline-flex; align-items: center;
    background: rgba(59,130,246,.07); color: #2563eb;
    border: 1px solid rgba(59,130,246,.2);
    padding: 3px 9px; border-radius: 99px;
    font-size: 12px; font-weight: 700; font-family: monospace; flex-shrink: 0;
}
.cr-content-text { font-size: 12.5px; color: var(--c-text,#374151); line-height: 1.5; flex: 1; min-width: 0; }
.cr-row-actions { display: flex; gap: 5px; flex-shrink: 0; }

.cr-edit-wrap { margin-top: 10px; display: flex; flex-direction: column; gap: 8px; }
.cr-edit-grid { display: grid; grid-template-columns: 160px 1fr; gap: 10px; }
.cr-edit-actions { display: flex; gap: 8px; }

.cr-empty { text-align: center; padding: 40px 20px; color: var(--c-sub,#9ca3af); font-size: 13px; }

.cr-list-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.cr-list-title { font-size: 12px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .06em; }
</style>

<div class="cr-wrap">
<h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:28px;letter-spacing:-.02em">Respuestas Rápidas</h1>
<div class="cr-layout">

    {{-- ═══════════════
         SIDEBAR — Crear
    ════════════════════ --}}
    <aside class="cr-sidebar">
        <div class="cr-card">
            <div class="cr-card-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva respuesta rápida
            </div>

            <div class="cr-field">
                <label class="cr-label">Atajo</label>
                <div class="cr-shortcut-wrap">
                    <span class="cr-shortcut-prefix">/</span>
                    <input type="text" class="cr-input" wire:model="newShortcut"
                        placeholder="saludo" autocomplete="off"
                        oninput="this.value=this.value.replace(/[^a-z0-9_\-]/gi,'').toLowerCase()">
                </div>
            </div>

            <div class="cr-field">
                <label class="cr-label">Contenido</label>
                <textarea class="cr-textarea" wire:model="newContent"
                    placeholder="Hola, gracias por contactarnos. ¿En qué te puedo ayudar?"></textarea>
            </div>

            <button class="cr-btn cr-btn-primary" wire:click="create" wire:loading.attr="disabled" style="width:100%;justify-content:center">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar respuesta
            </button>

            <p class="cr-hint">Escribe <strong>/atajo</strong> en el chat para ver sugerencias. Solo letras, números, guiones y guiones bajos.</p>
        </div>
    </aside>

    {{-- ═══════════════
         MAIN — Lista
    ════════════════════ --}}
    <div class="cr-card">
        <div class="cr-list-header">
            <div class="cr-list-title">
                Respuestas guardadas
                <span style="font-weight:400;text-transform:none;letter-spacing:0;margin-left:6px;color:var(--c-sub)">
                    ({{ count($this->cannedResponses) }})
                </span>
            </div>
            <div class="cr-search-wrap" style="width:200px">
                <svg class="cr-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" class="cr-search" wire:model.live.debounce.250ms="search" placeholder="Buscar...">
            </div>
        </div>

        @if(count($this->cannedResponses) > 0)
        <div class="cr-list">
            @foreach($this->cannedResponses as $item)
            <div class="cr-row">
                @if($editingId === $item->id)
                    <div class="cr-edit-wrap">
                        <div class="cr-edit-grid">
                            <div>
                                <label class="cr-label">Atajo</label>
                                <div class="cr-shortcut-wrap">
                                    <span class="cr-shortcut-prefix">/</span>
                                    <input type="text" class="cr-input" wire:model="editShortcut"
                                        oninput="this.value=this.value.replace(/[^a-z0-9_\-]/gi,'').toLowerCase()">
                                </div>
                            </div>
                            <div>
                                <label class="cr-label">Contenido</label>
                                <textarea class="cr-textarea" wire:model="editContent" style="min-height:56px"></textarea>
                            </div>
                        </div>
                        <div class="cr-edit-actions">
                            <button class="cr-btn cr-btn-primary cr-btn-sm" wire:click="saveEdit">Guardar</button>
                            <button class="cr-btn cr-btn-ghost cr-btn-sm" wire:click="cancelEdit">Cancelar</button>
                        </div>
                    </div>
                @else
                    <div class="cr-row-main">
                        <span class="cr-shortcut-badge">/{{ $item->shortcut }}</span>
                        <span class="cr-content-text">{{ \Illuminate\Support\Str::limit($item->content, 140) }}</span>
                        <div class="cr-row-actions">
                            <button class="cr-btn-icon" wire:click="startEdit({{ $item->id }})" title="Editar">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button class="cr-btn-icon danger" wire:click="delete({{ $item->id }})"
                                wire:confirm="¿Eliminar esta respuesta rápida?" title="Eliminar">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="cr-empty">
            @if($search)
                Sin resultados para "{{ $search }}"
            @else
                No hay respuestas rápidas. Agrega la primera desde el panel izquierdo.
            @endif
        </div>
        @endif
    </div>

</div>
</div>
</x-filament-panels::page>
