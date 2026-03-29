<x-filament-panels::page>
<div style="max-width:580px">
    <div style="margin-bottom:24px">
        <h1 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;letter-spacing:-.02em">Servidor de correo</h1>
        <p style="font-size:13px;color:#64748b;margin:0">Configura el SMTP para verificaciones, notificaciones y alertas de la plataforma.</p>
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden">

        {{-- SMTP fields --}}
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px">

            <div style="display:grid;grid-template-columns:1fr 120px;gap:12px">
                <div>
                    <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Host SMTP</label>
                    <input wire:model="smtpHost" placeholder="smtp.hostinger.com"
                           style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                           onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                           onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                </div>
                <div>
                    <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Puerto</label>
                    <input wire:model="smtpPort" placeholder="587"
                           style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                           onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                           onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Usuario</label>
                    <input wire:model="smtpUsername" placeholder="no-reply@tudominio.com"
                           style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                           onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                           onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                </div>
                <div>
                    <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">
                        Contraseña <span style="font-size:10.5px;color:#94a3b8;text-transform:none;letter-spacing:0">(en blanco = sin cambios)</span>
                    </label>
                    <input wire:model="smtpPassword" type="password" placeholder="••••••••"
                           style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                           onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                           onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:140px 1fr;gap:12px">
                <div>
                    <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Cifrado</label>
                    <select wire:model="smtpEncryption"
                            style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;outline:none;transition:border .15s"
                            onfocus="this.style.borderColor='#22c55e'" onblur="this.style.borderColor='#e2e8f0'">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="">Ninguno</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Email remitente</label>
                    <input wire:model="smtpFromAddress" placeholder="soporte@tudominio.com"
                           style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                           onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                           onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                </div>
            </div>

            <div>
                <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Nombre remitente</label>
                <input wire:model="smtpFromName" placeholder="Nexova Desk"
                       style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                       onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                       onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
            </div>
        </div>

        {{-- Footer actions --}}
        <div style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <button wire:click="save"
                    style="padding:8px 20px;background:#0f172a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
                Guardar configuración
            </button>

            <div style="display:flex;align-items:center;gap:8px">
                <input wire:model="testEmailTo" placeholder="correo@prueba.com"
                       style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;color:#0f172a;background:#fff;width:200px;outline:none;transition:border .15s"
                       onfocus="this.style.borderColor='#22c55e'" onblur="this.style.borderColor='#e2e8f0'">
                <button wire:click="testEmail"
                        style="padding:8px 16px;background:#fff;color:#0f172a;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;white-space:nowrap;transition:background .15s"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                    Enviar prueba
                </button>
            </div>
        </div>
    </div>

    {{-- Info box --}}
    <div style="margin-top:16px;padding:14px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;gap:10px">
        <svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" width="16" height="16" style="flex-shrink:0;margin-top:1px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p style="font-size:12.5px;color:#166534;margin:0;line-height:1.55">
            La contraseña SMTP se almacena cifrada en la base de datos. El correo de prueba usa la configuración guardada en DB, no la del archivo <code style="font-size:11.5px;background:rgba(0,0,0,.06);padding:1px 5px;border-radius:4px">.env</code>.
        </p>
    </div>
</div>
</x-filament-panels::page>
