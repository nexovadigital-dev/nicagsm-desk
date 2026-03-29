<x-filament-panels::page>
<div style="max-width:560px">

    {{-- Header --}}
    <div style="margin-bottom:24px">
        <h1 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;letter-spacing:-.02em">Mi perfil</h1>
        <p style="font-size:13px;color:#64748b;margin:0">Gestiona tus datos personales y la seguridad de tu cuenta.</p>
    </div>

    {{-- ── DATOS PERSONALES ── --}}
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:16px">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9">
            <p style="font-size:13.5px;font-weight:700;color:#0f172a;margin:0">Datos personales</p>
            <p style="font-size:12.5px;color:#94a3b8;margin:3px 0 0">Nombre visible en el panel de administración</p>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:16px">
            <div>
                <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Nombre</label>
                <input wire:model="name" placeholder="Tu nombre"
                       style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                       onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                       onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
            </div>
            <div>
                <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Email</label>
                <input value="{{ auth()->user()->email }}" disabled
                       style="width:100%;padding:8px 12px;border:1px solid #f1f5f9;border-radius:8px;font-size:13.5px;font-family:inherit;color:#94a3b8;background:#f8fafc;box-sizing:border-box;cursor:not-allowed">
            </div>
        </div>

        {{-- Cambiar contraseña --}}
        <div style="padding:0 24px 4px">
            <div style="height:1px;background:#f1f5f9;margin-bottom:20px"></div>
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 16px">Cambiar contraseña</p>
            <div style="display:flex;flex-direction:column;gap:14px">
                <div>
                    <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Contraseña actual</label>
                    <input wire:model="currentPassword" type="password" placeholder="Contraseña actual"
                           style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                           onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                           onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Nueva contraseña</label>
                        <input wire:model="newPassword" type="password" placeholder="Mín. 8 caracteres"
                               style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                               onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                               onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="display:block;font-size:11.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Confirmar</label>
                        <input wire:model="confirmPassword" type="password" placeholder="Repite la contraseña"
                               style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13.5px;font-family:inherit;color:#0f172a;background:#fff;box-sizing:border-box;outline:none;transition:border .15s"
                               onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                               onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                    </div>
                </div>
            </div>
        </div>

        <div style="padding:16px 24px;background:#f8fafc;border-top:1px solid #f1f5f9;margin-top:20px">
            <button wire:click="saveProfile"
                    style="padding:8px 20px;background:#0f172a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
                Guardar perfil
            </button>
        </div>
    </div>

    {{-- ── 2FA ── --}}
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden">
        <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div>
                <p style="font-size:13.5px;font-weight:700;color:#0f172a;margin:0">Autenticación 2FA</p>
                <p style="font-size:12.5px;color:#94a3b8;margin:3px 0 0">TOTP con Google Authenticator, Authy u otra app compatible</p>
            </div>
            @if($this->{'2faActive'})
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:99px;font-size:11.5px;font-weight:700;color:#15803d">
                    <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>Activo
                </span>
            @else
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:#fef2f2;border:1px solid #fecaca;border-radius:99px;font-size:11.5px;font-weight:700;color:#b91c1c">
                    <span style="width:6px;height:6px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>Inactivo
                </span>
            @endif
        </div>

        <div style="padding:24px">
            @if($this->{'2faActive'})
                <p style="font-size:13px;color:#64748b;margin:0 0 16px">Tu cuenta está protegida con autenticación de dos factores.</p>
                <button wire:click="disable2fa"
                        style="padding:8px 16px;background:#fff;color:#dc2626;border:1px solid #fecaca;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s"
                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
                    Desactivar 2FA
                </button>

            @elseif($showSetup2fa)
                <div style="margin-bottom:16px">
                    <p style="font-size:13px;color:#0f172a;margin:0 0 14px;line-height:1.55">
                        Escanea el código QR con tu app autenticadora, luego introduce el código de 6 dígitos para confirmar:
                    </p>
                    <div style="display:inline-block;padding:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:18px">
                        {!! $qrSvg !!}
                    </div>
                    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                        <input wire:model="twoFactorCode" placeholder="000000" maxlength="6"
                               style="padding:9px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:20px;font-family:monospace;letter-spacing:.25em;width:140px;text-align:center;outline:none;transition:border .15s;color:#0f172a"
                               onfocus="this.style.borderColor='#22c55e';this.style.boxShadow='0 0 0 3px rgba(34,197,94,.1)'"
                               onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                        <button wire:click="confirm2fa"
                                style="padding:9px 18px;background:#22c55e;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
                            Verificar y activar
                        </button>
                        <button wire:click="$set('showSetup2fa', false)"
                                style="padding:9px 14px;background:#fff;color:#64748b;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
                            Cancelar
                        </button>
                    </div>
                </div>

            @else
                <p style="font-size:13px;color:#64748b;margin:0 0 16px">Añade una capa extra de seguridad a tu cuenta de superadmin.</p>
                <button wire:click="begin2faSetup"
                        style="padding:8px 18px;background:#0f172a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
                    Configurar 2FA
                </button>
            @endif
        </div>
    </div>

</div>
</x-filament-panels::page>
