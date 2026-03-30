<x-filament-panels::page>
<style>
.sa-wrap    { display:flex; flex-direction:column; gap:24px; }
.sa-card    { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
.sa-tbl     { width:100%; border-collapse:collapse; font-size:13px; }
.sa-tbl th  { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#64748b; border-bottom:1px solid #e2e8f0; background:#f8fafc; white-space:nowrap; }
.sa-tbl td  { padding:12px 16px; border-bottom:1px solid #f1f5f9; color:#0f172a; vertical-align:middle; }
.sa-tbl tr:last-child td { border-bottom:none; }
.sa-tbl tr:hover td { background:#f8fafc; }
.sa-badge   { display:inline-flex; align-items:center; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:700; white-space:nowrap; }
.sa-btn     { display:inline-flex; align-items:center; gap:4px; padding:5px 11px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; border:none; font-family:inherit; transition:opacity .15s; white-space:nowrap; }
.sa-btn:hover { opacity:.82; }
.sa-input   { width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; font-family:inherit; background:#fff; color:#0f172a; outline:none; box-sizing:border-box; }
.sa-input:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.12); }
.sa-textarea{ width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; font-family:inherit; background:#fff; color:#0f172a; outline:none; box-sizing:border-box; resize:vertical; }
.sa-textarea:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.12); }
.sa-select  { width:100%; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; font-family:inherit; background:#fff; color:#0f172a; outline:none; cursor:pointer; }
.sa-select:focus { border-color:#22c55e; }
.sa-label   { font-size:11.5px; font-weight:700; color:#64748b; margin-bottom:5px; text-transform:uppercase; letter-spacing:.05em; display:block; }
.sa-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; display:flex; align-items:center; justify-content:center; padding:16px; }
.sa-modal   { background:#fff; border-radius:14px; width:780px; max-width:100%; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.18); }
.sa-modal-head { padding:18px 22px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:800; color:#0f172a; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
.sa-modal-body { padding:22px; display:flex; flex-direction:column; gap:14px; overflow-y:auto; }
.sa-modal-foot { padding:14px 22px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px; background:#f8fafc; border-radius:0 0 14px 14px; flex-shrink:0; }
.sa-cover-preview { width:100%;height:120px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;margin-top:6px; }
</style>

@php
$catColors = [
    'novedad'       => ['bg'=>'#dbeafe','color'=>'#1e40af'],
    'evento'        => ['bg'=>'#fef3c7','color'=>'#92400e'],
    'producto'      => ['bg'=>'#dcfce7','color'=>'#15803d'],
    'actualizacion' => ['bg'=>'#f3f4f6','color'=>'#374151'],
];
$catLabels = [
    'novedad'=>'Novedad','evento'=>'Evento',
    'producto'=>'Producto','actualizacion'=>'Actualizacion',
];
@endphp

<div class="sa-wrap"
     x-data="{ postModal: false }"
     @open-post-modal.window="postModal = true"
     @close-post-modal.window="postModal = false">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-size:18px;font-weight:800;color:#0f172a;margin:0">Blog / Novedades</h1>
            <p style="font-size:13px;color:#64748b;margin:4px 0 0">Publica entradas visibles en el homepage y en /novedades</p>
        </div>
        <button wire:click="openCreate" class="sa-btn" style="background:#22c55e;color:#fff;padding:9px 18px;font-size:13px">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nuevo post
        </button>
    </div>

    {{-- Filters --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <input wire:model.live.debounce.300ms="search"
               class="sa-input" style="max-width:260px"
               placeholder="Buscar por título o extracto…">
        <select wire:model.live="filterStatus" class="sa-select" style="width:auto">
            <option value="all">Todos</option>
            <option value="published">Publicados</option>
            <option value="draft">Borradores</option>
        </select>
        <span style="font-size:12px;color:#64748b;margin-left:auto">{{ $this->posts->total() }} entrada(s)</span>
    </div>

    {{-- Table --}}
    <div class="sa-card" style="overflow-x:auto">
        <table class="sa-tbl">
            <thead>
                <tr>
                    <th>Post</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Publicado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->posts as $post)
                @php
                    $cc = $catColors[$post->category] ?? ['bg'=>'#f3f4f6','color'=>'#6b7280'];
                    $cl = $catLabels[$post->category] ?? ucfirst($post->category);
                @endphp
                <tr>
                    <td style="max-width:340px">
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($post->cover_image)
                            <img src="{{ $post->cover_image }}" alt=""
                                 style="width:48px;height:36px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;flex-shrink:0">
                            @else
                            <div style="width:48px;height:36px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <svg fill="none" stroke="#94a3b8" viewBox="0 0 24 24" width="16" height="16"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><path d="M3 9h18M9 21V9" stroke-width="1.5"/></svg>
                            </div>
                            @endif
                            <div>
                                <div style="font-weight:700;color:#0f172a;line-height:1.3">{{ Str::limit($post->title, 60) }}</div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px">/novedades/{{ $post->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="sa-badge" style="background:{{ $cc['bg'] }};color:{{ $cc['color'] }}">{{ $cl }}</span>
                    </td>
                    <td>
                        @if($post->status === 'published')
                        <span class="sa-badge" style="background:#dcfce7;color:#15803d">Publicado</span>
                        @else
                        <span class="sa-badge" style="background:#f1f5f9;color:#64748b">Borrador</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#64748b;white-space:nowrap">
                        {{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <button wire:click="openEdit({{ $post->id }})"
                                    class="sa-btn" style="background:#f1f5f9;color:#374151">
                                Editar
                            </button>
                            <button wire:click="toggleStatus({{ $post->id }})"
                                    class="sa-btn" style="background:{{ $post->status === 'published' ? '#fef9c3' : '#dcfce7' }};color:{{ $post->status === 'published' ? '#854d0e' : '#15803d' }}">
                                {{ $post->status === 'published' ? 'Despublicar' : 'Publicar' }}
                            </button>
                            <button wire:click="deletePost({{ $post->id }})"
                                    wire:confirm="¿Eliminar este post permanentemente?"
                                    class="sa-btn" style="background:#fee2e2;color:#b91c1c">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:36px;color:#94a3b8;font-size:13px">
                    Sin posts todavía. Crea el primero con el botón de arriba.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($this->posts->hasPages())
    <div>{{ $this->posts->links() }}</div>
    @endif

    {{-- Create / Edit Modal --}}
    <div class="sa-overlay" x-show="postModal" x-cloak @click.self="postModal = false" style="display:none">
        <div class="sa-modal" @click.stop>
            <div class="sa-modal-head">
                <span>{{ $editingId ? 'Editar post' : 'Nuevo post' }}</span>
                <button @click="postModal = false" style="background:none;border:none;cursor:pointer;color:#64748b;padding:4px">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="sa-modal-body">

                {{-- Title + Slug --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div>
                        <label class="sa-label">Título *</label>
                        <input wire:model.live.debounce.400ms="formTitle" class="sa-input" placeholder="Título del post">
                        @error('formTitle')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="sa-label">Slug (URL)</label>
                        <input wire:model="formSlug" class="sa-input" placeholder="mi-post-url" style="font-family:monospace;font-size:12px">
                        @error('formSlug')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Category + Status + Date --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                    <div>
                        <label class="sa-label">Categoría</label>
                        <select wire:model="formCategory" class="sa-select">
                            <option value="novedad">Novedad</option>
                            <option value="evento">Evento</option>
                            <option value="producto">Producto</option>
                            <option value="actualizacion">Actualizacion</option>
                        </select>
                    </div>
                    <div>
                        <label class="sa-label">Estado</label>
                        <select wire:model="formStatus" class="sa-select">
                            <option value="draft">Borrador</option>
                            <option value="published">Publicado</option>
                        </select>
                    </div>
                    <div>
                        <label class="sa-label">Fecha publicación</label>
                        <input wire:model="formDate" type="datetime-local" class="sa-input">
                    </div>
                </div>

                {{-- Excerpt --}}
                <div>
                    <label class="sa-label">Extracto (resumen corto)</label>
                    <textarea wire:model="formExcerpt" class="sa-textarea" rows="2"
                              placeholder="Breve descripción visible en el listado y en el homepage…"></textarea>
                </div>

                {{-- Body --}}
                <div>
                    <label class="sa-label">Contenido (Markdown) *</label>
                    <textarea wire:model="formBody" class="sa-textarea" rows="10"
                              placeholder="# Título&#10;&#10;Escribe el contenido en **Markdown**. Soporta *cursiva*, listas, [links](url), etc."></textarea>
                    @error('formBody')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
                </div>

                {{-- Cover image --}}
                <div>
                    <label class="sa-label">URL imagen de portada</label>
                    <input wire:model.live="formCover" class="sa-input" placeholder="https://…/imagen.jpg">
                    @if(trim($formCover))
                    <img src="{{ $formCover }}" alt="Preview" class="sa-cover-preview"
                         onerror="this.style.display='none'">
                    @endif
                </div>

            </div>
            <div class="sa-modal-foot">
                <button @click="postModal = false" class="sa-btn" style="background:#f1f5f9;color:#374151">Cancelar</button>
                <button wire:click="savePost" class="sa-btn" style="background:#22c55e;color:#fff;padding:8px 20px">
                    {{ $editingId ? 'Guardar cambios' : 'Publicar post' }}
                </button>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
