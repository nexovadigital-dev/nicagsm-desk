<x-filament-panels::page>
<style>
.dp-wrap     { display:flex; flex-direction:column; gap:28px; }
.dp-section  { background:var(--nx-surface); border:1px solid var(--nx-border); border-radius:12px; overflow:hidden; }
.dp-head     { padding:16px 20px; border-bottom:1px solid var(--nx-border); display:flex; align-items:center; justify-content:space-between; }
.dp-title    { font-size:13.5px; font-weight:700; color:var(--nx-text); }
.dp-subtitle { font-size:11.5px; color:var(--nx-muted); margin-top:2px; }
.dp-body     { padding:20px; }

/* Cards grid */
.dp-grid     { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:10px; }
.dp-card     { border:1px solid var(--nx-border); border-radius:9px; padding:14px 16px; display:flex; align-items:center; gap:12px; position:relative; transition:border-color .15s, box-shadow .15s; background:var(--nx-surf2); }
.dp-card:hover { border-color:var(--nx-muted); box-shadow:0 2px 8px rgba(0,0,0,.06); }
.dp-swatch   { width:36px; height:36px; border-radius:8px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:#fff; }
.dp-card-info { flex:1; min-width:0; }
.dp-card-name { font-size:13px; font-weight:700; color:var(--nx-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dp-card-meta { font-size:11px; color:var(--nx-muted); margin-top:2px; }
.dp-actions  { display:flex; gap:4px; opacity:0; transition:opacity .15s; }
.dp-card:hover .dp-actions { opacity:1; }
.dp-btn      { width:28px; height:28px; border-radius:6px; border:1px solid var(--nx-border); background:var(--nx-surface); cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--nx-muted); transition:background .12s, color .12s; }
.dp-btn:hover { background:var(--nx-surf2); color:var(--nx-text); }
.dp-btn-del:hover  { background:#fef2f2; border-color:#fecaca; color:#dc2626; }
.dp-inactive { opacity:.5; }

/* Tag pills */
.dp-tags     { display:flex; flex-wrap:wrap; gap:8px; }
.dp-tag      { display:inline-flex; align-items:center; gap:8px; padding:6px 12px 6px 10px; border-radius:99px; border:1px solid; font-size:12px; font-weight:600; position:relative; transition:opacity .15s; }
.dp-tag:hover { opacity:.85; }
.dp-tag-actions { display:none; gap:3px; margin-left:2px; }
.dp-tag:hover .dp-tag-actions { display:flex; }
.dp-tag-btn { width:18px; height:18px; border-radius:4px; border:none; background:rgba(0,0,0,.12); cursor:pointer; display:flex; align-items:center; justify-content:center; padding:0; color:inherit; }
.dp-tag-btn:hover { background:rgba(0,0,0,.22); }

/* Empty state */
.dp-empty    { text-align:center; padding:32px 20px; color:var(--nx-muted); font-size:13px; }

/* Add button */
.dp-add      { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; background:#0f172a; color:#fff; font-size:12.5px; font-weight:600; border:none; cursor:pointer; font-family:inherit; transition:opacity .15s; }
.dp-add:hover { opacity:.85; }
.dp-add-ghost { background:var(--nx-surface); border:1px solid var(--nx-border); color:var(--nx-text); }
.dp-add-ghost:hover { background:var(--nx-surf2); opacity:1; }

/* Modal */
.dp-overlay  { position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:50; display:flex; align-items:center; justify-content:center; padding:16px; }
.dp-modal    { background:var(--nx-surface); border-radius:12px; width:400px; max-width:100%; box-shadow:0 16px 50px rgba(0,0,0,.16); }
.dp-modal-head { padding:16px 20px; border-bottom:1px solid var(--nx-border); font-size:14px; font-weight:700; color:var(--nx-text); display:flex; align-items:center; justify-content:space-between; background:var(--nx-surface); }
.dp-modal-body { padding:20px; display:flex; flex-direction:column; gap:14px; }
.dp-modal-foot { padding:12px 20px; border-top:1px solid var(--nx-border); display:flex; justify-content:flex-end; gap:8px; background:var(--nx-surface); }
.dp-label    { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--nx-muted); margin-bottom:5px; display:block; }
.dp-input    { width:100%; padding:8px 12px; border:1px solid var(--nx-border); border-radius:8px; font-size:13px; font-family:inherit; color:var(--nx-text); outline:none; box-sizing:border-box; transition:border-color .12s; background:var(--nx-surf2); }
.dp-input:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.1); }
.dp-color-row { display:flex; align-items:center; gap:10px; }
.dp-color-input { width:44px; height:36px; padding:2px; border:1px solid var(--nx-border); border-radius:8px; cursor:pointer; background:var(--nx-surf2); }
.dp-color-presets { display:flex; gap:6px; flex-wrap:wrap; }
.dp-color-dot { width:22px; height:22px; border-radius:50%; cursor:pointer; border:2px solid transparent; transition:transform .1s, border-color .1s; }
.dp-color-dot:hover { transform:scale(1.15); border-color:#fff; box-shadow:0 0 0 2px #94a3b8; }

@media (max-width: 640px) {
    .dp-wrap { padding: 0 0 48px; gap: 16px; }
    .dp-head { flex-direction: column; align-items: flex-start; gap: 10px; padding: 14px 16px; }
    .dp-add { width: 100%; justify-content: center; }
    .dp-grid { grid-template-columns: 1fr; }
    .dp-body { padding: 14px; }
    .dp-actions { opacity: 1; } /* Always show on mobile (no hover) */
    .dp-tag-actions { display: flex; } /* Always show on mobile */
}
</style>

@php
$depts = $this->departments;
$tags  = $this->tags;
$presetColors = ['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#64748b'];
@endphp

<div class="dp-wrap">

    {{-- ── Departments section ────────────────────────────────────────────── --}}
    <div class="dp-section">
        <div class="dp-head">
            <div>
                <div class="dp-title">Departamentos</div>
                <div class="dp-subtitle">Organiza las conversaciones por área de tu equipo</div>
            </div>
            <button class="dp-add" wire:click="openDeptModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nuevo departamento
            </button>
        </div>
        <div class="dp-body">
            @if($depts->isEmpty())
                <div class="dp-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="32" height="32" style="margin:0 auto 10px;opacity:.3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p>Aún no tienes departamentos. Crea uno para organizar tu equipo.</p>
                </div>
            @else
                <div class="dp-grid">
                    @foreach($depts as $dept)
                    <div class="dp-card {{ !$dept->is_active ? 'dp-inactive' : '' }}">
                        <div class="dp-swatch" style="background:{{ $dept->color }}">
                            {{ strtoupper(mb_substr($dept->name, 0, 1)) }}
                        </div>
                        <div class="dp-card-info">
                            <div class="dp-card-name">{{ $dept->name }}</div>
                            <div class="dp-card-meta">
                                {{ $dept->tickets_count }} conversacion{{ $dept->tickets_count != 1 ? 'es' : '' }}
                                @if(!$dept->is_active) · <span style="color:#f59e0b">Inactivo</span>@endif
                            </div>
                        </div>
                        <div class="dp-actions">
                            <button class="dp-btn" wire:click="openDeptModal({{ $dept->id }})" title="Editar">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button class="dp-btn dp-btn-del"
                                    wire:click="deleteDept({{ $dept->id }})"
                                    wire:confirm="¿Eliminar el departamento '{{ $dept->name }}'? Las conversaciones asignadas quedarán sin departamento."
                                    title="Eliminar">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Tags section ────────────────────────────────────────────────────── --}}
    <div class="dp-section">
        <div class="dp-head">
            <div>
                <div class="dp-title">Etiquetas (Tags)</div>
                <div class="dp-subtitle">Clasifica conversaciones con etiquetas de color</div>
            </div>
            <button class="dp-add" wire:click="openTagModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nueva etiqueta
            </button>
        </div>
        <div class="dp-body">
            @if($tags->isEmpty())
                <div class="dp-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="32" height="32" style="margin:0 auto 10px;opacity:.3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <p>Aún no tienes etiquetas. Úsalas para clasificar conversaciones fácilmente.</p>
                </div>
            @else
                <div class="dp-tags">
                    @foreach($tags as $tag)
                    @php
                        $hex = $tag->color;
                        // lighten for background
                        $bg  = $hex . '22';
                    @endphp
                    <div class="dp-tag" style="background:{{ $bg }};color:{{ $hex }};border-color:{{ $hex }}44">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $hex }};flex-shrink:0;display:inline-block"></span>
                        {{ $tag->name }}
                        <span style="font-size:10px;opacity:.7">({{ $tag->tickets_count }})</span>
                        <span class="dp-tag-actions">
                            <button class="dp-tag-btn" wire:click="openTagModal({{ $tag->id }})" title="Editar">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button class="dp-tag-btn"
                                    wire:click="deleteTag({{ $tag->id }})"
                                    wire:confirm="¿Eliminar la etiqueta '{{ $tag->name }}'?"
                                    title="Eliminar">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Department modal ────────────────────────────────────────────────────── --}}
@if($showDeptModal)
<div class="dp-overlay" wire:click.self="$set('showDeptModal', false)">
    <div class="dp-modal">
        <div class="dp-modal-head">
            {{ $editDeptId ? 'Editar departamento' : 'Nuevo departamento' }}
            <button wire:click="$set('showDeptModal',false)" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;display:flex">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="dp-modal-body">
            <div>
                <label class="dp-label">Nombre del departamento *</label>
                <input wire:model="deptName" class="dp-input" placeholder="Ej: Soporte técnico, Ventas, Facturación…" autofocus>
            </div>
            <div>
                <label class="dp-label">Descripción (opcional)</label>
                <input wire:model="deptDesc" class="dp-input" placeholder="¿Para qué sirve este departamento?">
            </div>
            <div>
                <label class="dp-label">Color</label>
                <div class="dp-color-row">
                    <input type="color" wire:model="deptColor" class="dp-color-input">
                    <div class="dp-color-presets">
                        @foreach(['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#64748b'] as $c)
                        <div class="dp-color-dot" style="background:{{ $c }}"
                             wire:click="$set('deptColor','{{ $c }}')"
                             title="{{ $c }}"></div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:9px">
                <input type="checkbox" wire:model="deptActive" id="deptActive" style="width:16px;height:16px;accent-color:#22c55e">
                <label for="deptActive" style="font-size:13px;font-weight:600;color:#0f172a;cursor:pointer">Departamento activo</label>
            </div>
        </div>
        <div class="dp-modal-foot">
            <button wire:click="$set('showDeptModal',false)" class="dp-add dp-add-ghost">Cancelar</button>
            <button wire:click="saveDept" class="dp-add">Guardar</button>
        </div>
    </div>
</div>
@endif

{{-- ── Tag modal ────────────────────────────────────────────────────────────── --}}
@if($showTagModal)
<div class="dp-overlay" wire:click.self="$set('showTagModal', false)">
    <div class="dp-modal" style="width:340px">
        <div class="dp-modal-head">
            {{ $editTagId ? 'Editar etiqueta' : 'Nueva etiqueta' }}
            <button wire:click="$set('showTagModal',false)" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;display:flex">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="dp-modal-body">
            <div>
                <label class="dp-label">Nombre de la etiqueta *</label>
                <input wire:model="tagName" class="dp-input" placeholder="Ej: Urgente, Seguimiento, VIP…" autofocus>
            </div>
            <div>
                <label class="dp-label">Color</label>
                <div class="dp-color-row">
                    <input type="color" wire:model="tagColor" class="dp-color-input">
                    <div class="dp-color-presets">
                        @foreach(['#22c55e','#6366f1','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#64748b'] as $c)
                        <div class="dp-color-dot" style="background:{{ $c }}"
                             wire:click="$set('tagColor','{{ $c }}')"
                             title="{{ $c }}"></div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- Preview --}}
            <div style="display:flex;align-items:center;gap:8px;padding:10px 12px;background:#f8fafc;border-radius:8px;border:1px solid #f1f5f9">
                <span style="font-size:11px;color:#94a3b8;font-weight:600">Vista previa:</span>
                <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:99px;border:1px solid;font-size:12px;font-weight:600"
                      :style="`background:${$wire.tagColor}22;color:${$wire.tagColor};border-color:${$wire.tagColor}44`"
                      x-data x-bind:style="`background:{{ '${' }}$wire.tagColor{{ '}' }}22;color:{{ '${' }}$wire.tagColor{{ '}' }};border-color:{{ '${' }}$wire.tagColor{{ '}' }}44`">
                    <span x-bind:style="`width:8px;height:8px;border-radius:50%;background:{{ '${' }}$wire.tagColor{{ '}' }};display:inline-block`"></span>
                    <span x-text="$wire.tagName || 'Etiqueta'"></span>
                </span>
            </div>
        </div>
        <div class="dp-modal-foot">
            <button wire:click="$set('showTagModal',false)" class="dp-add dp-add-ghost">Cancelar</button>
            <button wire:click="saveTag" class="dp-add">Guardar</button>
        </div>
    </div>
</div>
@endif

</x-filament-panels::page>
