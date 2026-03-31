<x-filament-panels::page>
<style>
.fi-page-header,.fi-breadcrumbs{display:none!important}
.ac-wrap{padding:32px 36px 64px}
.ac-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px;max-width:900px}
@media(max-width:600px){.ac-grid{grid-template-columns:1fr}}
.ac-card{background:var(--c-surface,#fff);border:1px solid var(--c-border,#e3e6ea);border-radius:10px;padding:20px 22px;display:flex;flex-direction:column;gap:14px}
.ac-head{display:flex;align-items:center;gap:12px}
.ac-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0}
.ac-title{font-size:13px;font-weight:700;color:var(--c-text,#111)}
.ac-desc{font-size:11.5px;color:var(--c-sub,#6b7280);margin-top:1px}
.ac-badge{display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;padding:3px 9px;border-radius:99px;margin-left:auto;flex-shrink:0}
.ac-badge-on{background:rgba(5,150,105,.08);color:#059669;border:1px solid rgba(5,150,105,.18)}
.ac-badge-off{background:rgba(107,114,128,.08);color:#9ca3af;border:1px solid rgba(107,114,128,.18)}
.ac-field{display:flex;flex-direction:column;gap:4px}
.ac-label{font-size:10px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em}
.ac-input-wrap{position:relative}
.ac-input{width:100%;background:var(--c-bg,#f5f6f8);border:1px solid var(--c-border,#e3e6ea);border-radius:7px;color:var(--c-text,#111);font-size:13px;font-family:monospace;padding:8px 38px 8px 11px;outline:none;box-sizing:border-box;transition:border-color .12s}
.ac-input:focus{border-color:#16a34a}
.ac-eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--c-sub,#6b7280);padding:0;display:flex}
.ac-eye:hover{color:var(--c-text,#111)}
.ac-footer{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.ac-divider{height:1px;background:var(--c-border,#e3e6ea)}
.ac-btn{display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:7px;font-size:12px;font-weight:500;cursor:pointer;border:1px solid transparent;font-family:inherit;transition:background .1s}
.ac-btn-primary{background:#1e293b;color:#f8fafc}
.ac-btn-primary:hover{background:#0f172a}
.ac-btn-outline{background:transparent;color:var(--c-text,#111);border-color:var(--c-border,#e3e6ea)}
.ac-btn-outline:hover{background:var(--c-surf2,#f0f2f5)}
.ac-btn-danger{background:transparent;color:#dc2626;border-color:rgba(220,38,38,.25)}
.ac-btn-danger:hover{background:rgba(220,38,38,.05)}
</style>

<div class="ac-wrap">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:6px;letter-spacing:-.02em">Configuración de IA</h1>
    <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:0 0 28px;max-width:600px">
        Claves API de los proveedores de inteligencia artificial utilizados por la plataforma. Estas claves se comparten entre todas las organizaciones que no tienen sus propias claves configuradas.
    </p>

    <div class="ac-grid">
        @foreach($this->getProviders() as $key => $info)
        @php $hasKey = $this->hasKey($key); $isOn = $activeFlags[$key] ?? true; @endphp
        <div class="ac-card">

            <div class="ac-head">
                <div class="ac-icon" style="background:{{ $info['color'] }}">{{ $info['letter'] }}</div>
                <div style="flex:1;min-width:0">
                    <div class="ac-title">{{ $info['label'] }}</div>
                    <div class="ac-desc">{{ $info['desc'] }}</div>
                </div>
                @if($hasKey)
                    <span class="ac-badge {{ $isOn ? 'ac-badge-on' : 'ac-badge-off' }}">
                        <svg fill="currentColor" viewBox="0 0 8 8" width="6" height="6"><circle cx="4" cy="4" r="4"/></svg>
                        {{ $isOn ? 'Activa' : 'Inactiva' }}
                    </span>
                @else
                    <span class="ac-badge ac-badge-off">Sin configurar</span>
                @endif
            </div>

            <div class="ac-field">
                <label class="ac-label">API Key</label>
                <div class="ac-input-wrap" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" class="ac-input"
                        wire:model.blur="keys.{{ $key }}"
                        placeholder="{{ $hasKey ? '••••••••••••••••• (configurada — pega nueva para reemplazar)' : 'Pega tu clave aquí…' }}"
                        autocomplete="off">
                    <button type="button" class="ac-eye" @click="show = !show" tabindex="-1">
                        <svg x-show="!show" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <div class="ac-divider"></div>

            <div class="ac-footer">
                <button class="ac-btn ac-btn-primary" wire:click="save('{{ $key }}')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Guardar
                </button>
                @if($hasKey)
                    <button class="ac-btn ac-btn-outline" wire:click="toggleActive('{{ $key }}')">
                        {{ $isOn ? 'Desactivar' : 'Activar' }}
                    </button>
                    <button class="ac-btn ac-btn-danger" wire:click="delete('{{ $key }}')"
                        wire:confirm="¿Eliminar la clave de {{ $info['label'] }}? El bot dejará de funcionar si no hay clave de respaldo.">
                        Eliminar
                    </button>
                @endif
            </div>

        </div>
        @endforeach
    </div>

    <div style="margin-top:24px;max-width:900px;padding:14px 16px;background:rgba(234,179,8,.08);border:1px solid rgba(234,179,8,.2);border-radius:8px;font-size:12.5px;color:rgba(202,138,4,1);line-height:1.6">
        <strong>Nota:</strong> Las organizaciones con sus propias claves API configuradas y activadas usarán sus propias claves en lugar de estas. Las llaves configuradas aquí son el fallback global de la plataforma.
    </div>
</div>
</x-filament-panels::page>
