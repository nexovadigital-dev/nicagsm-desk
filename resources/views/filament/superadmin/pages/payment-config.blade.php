<x-filament-panels::page>
<style>
.sa-wrap    { display:flex; flex-direction:column; gap:24px; }
.sa-card    { background:var(--c-surface,#fff); border:1px solid var(--c-border,#e3e6ea); border-radius:12px; overflow:hidden; }
.sa-card-head { padding:14px 20px; border-bottom:1px solid var(--c-border,#e3e6ea); font-size:14px; font-weight:800; color:var(--c-text,#111827); }
.sa-card-body { padding:20px; }
.sa-input   { width:100%; padding:8px 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; font-size:13px; background:var(--c-surface,#fff); color:var(--c-text,#111827); outline:none; box-sizing:border-box; font-family:monospace; }
.sa-input:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.15); }
.sa-label   { font-size:12px; font-weight:700; color:var(--c-sub,#6b7280); margin-bottom:4px; text-transform:uppercase; letter-spacing:.04em; }
.sa-btn     { display:inline-flex; align-items:center; gap:4px; padding:7px 16px; border-radius:7px; font-size:13px; font-weight:700; cursor:pointer; border:none; transition:opacity .15s; }
.sa-btn:hover { opacity:.85; }
.sa-crypto-row { display:grid; grid-template-columns:auto 1fr auto auto; gap:12px; align-items:center; padding:14px 0; border-bottom:1px solid var(--c-border,#e3e6ea); }
.sa-crypto-row:last-child { border-bottom:none; }
.sa-badge   { display:inline-block; padding:2px 9px; border-radius:99px; font-size:11px; font-weight:700; }
.sa-toggle  { position:relative; width:40px; height:22px; cursor:pointer; }
.sa-toggle input { opacity:0; width:0; height:0; }
.sa-toggle-slider { position:absolute; inset:0; background:#d1d5db; border-radius:99px; transition:.2s; }
.sa-toggle input:checked + .sa-toggle-slider { background:#22c55e; }
.sa-toggle-slider:before { content:''; position:absolute; width:16px; height:16px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.2s; }
.sa-toggle input:checked + .sa-toggle-slider:before { transform:translateX(18px); }
.sa-select  { padding:8px 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; font-size:13px; background:var(--c-surface,#fff); color:var(--c-text,#111827); outline:none; }
</style>

<div class="sa-wrap">

    {{-- Header --}}
    <div>
        <h1 style="font-size:20px;font-weight:800;color:var(--c-text,#111827);margin:0">Métodos de Pago</h1>
        <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:4px 0 0">Configura las wallets y pasarelas de pago disponibles para los clientes</p>
    </div>

    {{-- Crypto wallets --}}
    <div class="sa-card">
        <div class="sa-card-head">Criptomonedas (USDT / USDC)</div>
        <div class="sa-card-body">
            @php
            $netColors = ['trc20'=>['bg'=>'#fef3c7','color'=>'#92400e'],'bep20'=>['bg'=>'#fef9c3','color'=>'#854d0e'],'polygon'=>['bg'=>'#ede9fe','color'=>'#6d28d9']];
            $currColors = ['USDT'=>['bg'=>'#d1fae5','color'=>'#065f46'],'USDC'=>['bg'=>'#dbeafe','color'=>'#1e40af']];
            @endphp
            @foreach($this->getCryptoMethods() as $method => $meta)
            @php
                $nc = $netColors[$meta['network']] ?? ['bg'=>'#f3f4f6','color'=>'#6b7280'];
                $cc = $currColors[$meta['currency']] ?? ['bg'=>'#f3f4f6','color'=>'#6b7280'];
                $wallet = $cryptoWallets[$method] ?? ['address'=>'','is_active'=>false];
            @endphp
            <div class="sa-crypto-row">
                <div style="min-width:160px">
                    <div style="font-weight:700;font-size:13px;color:var(--c-text,#111827)">
                        <span class="sa-badge" style="background:{{ $cc['bg'] }};color:{{ $cc['color'] }}">{{ $meta['currency'] }}</span>
                        <span style="margin-left:4px">·</span>
                        <span class="sa-badge" style="background:{{ $nc['bg'] }};color:{{ $nc['color'] }};margin-left:4px;text-transform:uppercase">{{ $meta['network'] }}</span>
                    </div>
                    <div style="font-size:11px;color:var(--c-sub,#6b7280);margin-top:4px">{{ $meta['label'] }}</div>
                </div>
                <div>
                    <input wire:model="cryptoWallets.{{ $method }}.address"
                           class="sa-input"
                           placeholder="Dirección de wallet (T..., 0x...)">
                </div>
                <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
                    <label class="sa-toggle">
                        <input type="checkbox" wire:model="cryptoWallets.{{ $method }}.is_active">
                        <span class="sa-toggle-slider"></span>
                    </label>
                    <span style="font-size:10px;color:var(--c-sub,#6b7280)">
                        {{ $wallet['is_active'] ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div>
                    <button wire:click="saveCrypto('{{ $method }}')"
                            class="sa-btn" style="background:#22c55e;color:#fff;white-space:nowrap">
                        Guardar
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- MercadoPago --}}
    <div class="sa-card">
        <div class="sa-card-head" style="display:flex;align-items:center;justify-content:space-between">
            <span>MercadoPago</span>
            <span class="sa-badge" style="background:#009ee3;color:#fff;font-size:10px">Colombia / LATAM</span>
        </div>
        <div class="sa-card-body" style="display:flex;flex-direction:column;gap:16px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                    <div class="sa-label">Access Token (privado)</div>
                    <input wire:model="mpAccessToken"
                           type="password"
                           class="sa-input"
                           placeholder="APP_USR-…  (dejar vacío = no cambiar)">
                    <div style="font-size:11px;color:var(--c-sub,#6b7280);margin-top:4px">Se guarda encriptado. Déjalo vacío si no quieres cambiarlo.</div>
                </div>
                <div>
                    <div class="sa-label">Public Key</div>
                    <input wire:model="mpPublicKey"
                           type="password"
                           class="sa-input"
                           placeholder="APP_USR-…  (dejar vacío = no cambiar)">
                </div>
            </div>
            <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap">
                <div>
                    <div class="sa-label">País / moneda</div>
                    <select wire:model="mpCountry" class="sa-select">
                        <option value="CO">Colombia (COP)</option>
                        <option value="MX">México (MXN)</option>
                        <option value="AR">Argentina (ARS)</option>
                        <option value="PE">Perú (PEN)</option>
                        <option value="CL">Chile (CLP)</option>
                        <option value="UY">Uruguay (UYU)</option>
                        <option value="BR">Brasil (BRL)</option>
                    </select>
                </div>
                <div style="display:flex;align-items:center;gap:10px;margin-top:auto">
                    <label class="sa-toggle">
                        <input type="checkbox" wire:model="mpActive">
                        <span class="sa-toggle-slider"></span>
                    </label>
                    <span style="font-size:13px;font-weight:600;color:var(--c-text,#111827)">
                        {{ $mpActive ? 'Habilitado' : 'Deshabilitado' }}
                    </span>
                </div>
            </div>
            <div>
                <button wire:click="saveMercadoPago"
                        class="sa-btn" style="background:#009ee3;color:#fff">
                    Guardar MercadoPago
                </button>
            </div>
        </div>
    </div>

    {{-- Info box --}}
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px 20px;font-size:13px;color:#166534">
        <strong>Flujo de pago crypto:</strong> el cliente ve la wallet + monto, envía la transferencia y sube el TX hash. El super-admin verifica en el explorer de la red y confirma manualmente desde la sección de Transacciones.
    </div>

</div>
</x-filament-panels::page>
