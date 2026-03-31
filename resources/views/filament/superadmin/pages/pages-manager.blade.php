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
.sa-modal   { background:#fff; border-radius:14px; width:860px; max-width:100%; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.18); }
.sa-modal-head { padding:18px 22px; border-bottom:1px solid #e2e8f0; font-size:15px; font-weight:800; color:#0f172a; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
.sa-modal-body { padding:22px; display:flex; flex-direction:column; gap:14px; overflow-y:auto; }
.sa-modal-foot { padding:14px 22px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px; background:#f8fafc; border-radius:0 0 14px 14px; flex-shrink:0; }
</style>

<div class="sa-wrap"
     x-data="{ pageModal: false }"
     @open-page-modal.window="pageModal = true"
     @close-page-modal.window="pageModal = false">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-size:18px;font-weight:800;color:#0f172a;margin:0">Páginas</h1>
            <p style="font-size:13px;color:#64748b;margin:4px 0 0">Páginas estáticas públicas: Términos, Privacidad, Sobre nosotros, etc. Accesibles en <code>/p/{slug}</code></p>
        </div>
        <button wire:click="openCreate" class="sa-btn" style="background:#22c55e;color:#fff;padding:9px 18px;font-size:13px">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nueva página
        </button>
    </div>

    {{-- Filters --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <input wire:model.live.debounce.300ms="search"
               class="sa-input" style="max-width:260px"
               placeholder="Buscar por título…">
        <select wire:model.live="filterStatus" class="sa-select" style="width:auto">
            <option value="all">Todas</option>
            <option value="published">Publicadas</option>
            <option value="draft">Borradores</option>
        </select>
        <span style="font-size:12px;color:#64748b;margin-left:auto">{{ count($this->pages) }} página(s)</span>
    </div>

    {{-- Table --}}
    <div class="sa-card" style="overflow-x:auto">
        <table class="sa-tbl">
            <thead>
                <tr>
                    <th>Página</th>
                    <th>URL pública</th>
                    <th>Estado</th>
                    <th>SEO</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->pages as $page)
                <tr>
                    <td style="max-width:280px">
                        <div style="font-weight:700;color:#0f172a">{{ $page->title }}</div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px">
                            Actualizada {{ $page->updated_at->diffForHumans() }}
                        </div>
                    </td>
                    <td>
                        <a href="{{ url('/p/' . $page->slug) }}" target="_blank"
                           style="font-size:12px;color:#3b82f6;font-family:monospace;text-decoration:none">
                            /p/{{ $page->slug }}
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11" stroke-width="2" style="display:inline;margin-left:3px"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </td>
                    <td>
                        @if($page->status === 'published')
                        <span class="sa-badge" style="background:#dcfce7;color:#15803d">Publicada</span>
                        @else
                        <span class="sa-badge" style="background:#f1f5f9;color:#64748b">Borrador</span>
                        @endif
                    </td>
                    <td>
                        @if($page->meta_title || $page->meta_description)
                        <span class="sa-badge" style="background:#eff6ff;color:#1d4ed8">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10" stroke-width="2" style="margin-right:3px"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Configurado
                        </span>
                        @else
                        <span style="font-size:11px;color:#94a3b8">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <button wire:click="openEdit({{ $page->id }})"
                                    class="sa-btn" style="background:#f1f5f9;color:#374151">
                                Editar
                            </button>
                            <button wire:click="toggleStatus({{ $page->id }})"
                                    class="sa-btn" style="background:{{ $page->status === 'published' ? '#fef9c3' : '#dcfce7' }};color:{{ $page->status === 'published' ? '#854d0e' : '#15803d' }}">
                                {{ $page->status === 'published' ? 'Despublicar' : 'Publicar' }}
                            </button>
                            <button wire:click="deletePage({{ $page->id }})"
                                    wire:confirm="¿Eliminar la página '{{ $page->title }}'? Esta acción no se puede deshacer."
                                    class="sa-btn" style="background:#fee2e2;color:#b91c1c">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:40px;color:#94a3b8;font-size:13px">
                    Sin páginas todavía. Crea la primera (ej: "Términos de servicio", slug: <code>terminos</code>).
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Create / Edit Modal --}}
    <div class="sa-overlay" x-show="pageModal" x-cloak @click.self="pageModal = false" style="display:none">
        <div class="sa-modal" @click.stop>
            <div class="sa-modal-head">
                <span>{{ $editingId ? 'Editar página' : 'Nueva página' }}</span>
                <button @click="pageModal = false" style="background:none;border:none;cursor:pointer;color:#64748b;padding:4px">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="sa-modal-body">

                {{-- Title + Slug --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div>
                        <label class="sa-label">Título *</label>
                        <input wire:model.live.debounce.400ms="formTitle" class="sa-input" placeholder="Ej: Términos de Servicio">
                        @error('formTitle')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="sa-label">Slug (URL: /p/…)</label>
                        <input wire:model="formSlug" class="sa-input" placeholder="terminos-de-servicio" style="font-family:monospace;font-size:12px">
                        @error('formSlug')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Status --}}
                <div style="max-width:220px">
                    <label class="sa-label">Estado</label>
                    <select wire:model="formStatus" class="sa-select">
                        <option value="draft">Borrador</option>
                        <option value="published">Publicada</option>
                    </select>
                </div>

                {{-- Content --}}
                <div>
                    <label class="sa-label">Contenido (Markdown) *</label>
                    <textarea wire:model="formContent" class="sa-textarea" rows="14"
                              placeholder="# Términos de Servicio&#10;&#10;Escribe el contenido en **Markdown**. Soporta encabezados, listas, **negrita**, *cursiva*, [links](url), etc."></textarea>
                    @error('formContent')<p style="font-size:11px;color:#ef4444;margin-top:4px">{{ $message }}</p>@enderror
                    <p style="font-size:11px;color:#94a3b8;margin-top:4px">
                        Usa Markdown: <code>## Sección</code>, <code>**negrita**</code>, <code>- lista</code>, <code>[texto](url)</code>
                    </p>
                </div>

                {{-- SEO --}}
                <div style="border-top:1px solid #e2e8f0;padding-top:14px;display:flex;flex-direction:column;gap:10px">
                    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;display:flex;align-items:center;gap:6px">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
                        SEO (opcional)
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div>
                            <label class="sa-label">Meta título <span style="font-weight:400;text-transform:none">(máx. 70 chars)</span></label>
                            <input wire:model="formMetaTitle" class="sa-input" placeholder="{{ $formTitle ?: 'Título de la página' }} — Nexova Desk" maxlength="70">
                            <div style="font-size:11px;color:#94a3b8;margin-top:3px">{{ strlen($formMetaTitle) }}/70</div>
                        </div>
                        <div>
                            <label class="sa-label">Meta descripción <span style="font-weight:400;text-transform:none">(máx. 160 chars)</span></label>
                            <textarea wire:model="formMetaDesc" class="sa-textarea" rows="2" maxlength="160"
                                      placeholder="Descripción breve para buscadores"></textarea>
                            <div style="font-size:11px;color:#94a3b8;margin-top:3px">{{ strlen($formMetaDesc) }}/160</div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="sa-modal-foot">
                <button @click="pageModal = false" class="sa-btn" style="background:#f1f5f9;color:#374151">Cancelar</button>
                <button wire:click="savePage" class="sa-btn" style="background:#22c55e;color:#fff;padding:8px 20px">
                    {{ $editingId ? 'Guardar cambios' : 'Crear página' }}
                </button>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
