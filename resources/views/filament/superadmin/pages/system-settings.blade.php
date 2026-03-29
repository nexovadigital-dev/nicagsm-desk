<x-filament-panels::page>
<div style="max-width:560px">

    {{-- Registration toggle card --}}
    <div style="background:var(--fi-color-white,#fff);border:1px solid var(--c-border,#e5e7eb);border-radius:12px;padding:24px;margin-bottom:20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <div>
                <p style="font-size:15px;font-weight:700;color:var(--c-text,#111827);margin:0">Registro de nuevas organizaciones</p>
                <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:4px 0 0">Controla si se permiten nuevos registros en la plataforma.</p>
            </div>
            {{-- Toggle --}}
            <div x-data="{ on: @entangle('allowRegistrations') }">
                <button type="button"
                    @click="on = !on; $wire.set('allowRegistrations', on)"
                    :style="'position:relative;width:48px;height:26px;border-radius:99px;border:none;cursor:pointer;transition:background .2s;background:' + (on ? '#22c55e' : '#d1d5db')">
                    <span :style="'position:absolute;top:3px;width:20px;height:20px;border-radius:50%;background:#fff;transition:left .2s;left:' + (on ? '25px' : '3px')"></span>
                </button>
            </div>
        </div>

        {{-- Status badge --}}
        <div wire:key="reg-status" style="margin-bottom:16px">
            @if($allowRegistrations)
                <span style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;font-size:12px;font-weight:600;padding:5px 10px;border-radius:99px">
                    <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>
                    Registros abiertos — nuevos usuarios pueden registrarse
                </span>
            @else
                <span style="display:inline-flex;align-items:center;gap:6px;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;font-size:12px;font-weight:600;padding:5px 10px;border-radius:99px">
                    <span style="width:7px;height:7px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
                    Registros cerrados — se muestra mensaje de mantenimiento
                </span>
            @endif
        </div>

        {{-- Message when closed --}}
        <div style="margin-bottom:16px">
            <label style="display:block;font-size:12px;font-weight:600;color:var(--c-sub,#6b7280);margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em">
                Mensaje que verá el usuario al intentar registrarse
            </label>
            <textarea wire:model="registrationClosedMessage" rows="3"
                style="width:100%;font-size:13px;padding:9px 11px;border:1.5px solid var(--c-border,#e5e7eb);border-radius:8px;
                       font-family:inherit;color:var(--c-text,#111827);background:var(--fi-color-white,#fff);
                       resize:vertical;box-sizing:border-box;outline:none;line-height:1.5"
                placeholder="Mensaje que verá el usuario..."></textarea>
        </div>

        <button wire:click="save"
            style="background:#22c55e;color:#fff;border:none;border-radius:8px;padding:9px 20px;
                   font-size:13px;font-weight:700;cursor:pointer;font-family:inherit">
            Guardar configuración
        </button>
    </div>

</div>
</x-filament-panels::page>
