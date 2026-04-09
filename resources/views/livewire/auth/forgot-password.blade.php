<div class="lf-wrap">

    @if($step === 'email')
        <div class="lf-head">
            <div class="lf-title">Recuperar contraseña</div>
            <div class="lf-sub">Ingresa tu email y te enviaremos un código de verificación.</div>
        </div>

        @if($error)
        <div class="lf-alert lf-alert-err">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" style="flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ $error }}
        </div>
        @endif

        <div class="lf-field">
            <label class="lf-label">Correo electrónico</label>
            <div class="lf-input-wrap">
                <span class="lf-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <input type="email" wire:model="email" class="lf-input"
                       placeholder="tu@empresa.com" autocomplete="email"
                       wire:keydown.enter="sendCode">
            </div>
        </div>

        <button class="lf-btn" wire:click="sendCode" wire:loading.attr="disabled" style="margin-top:6px">
            <svg wire:loading wire:target="sendCode" class="lf-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15" style="display:none">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span wire:loading.remove wire:target="sendCode">Enviar código</span>
            <span wire:loading wire:target="sendCode" style="display:none">Enviando...</span>
        </button>

        <div style="text-align:center;margin-top:22px">
            <a href="{{ route('auth.login') }}" class="lf-forgot">← Volver al inicio de sesión</a>
        </div>

    @elseif($step === 'code')
        <div class="lf-head">
            <div class="lf-title">Verifica tu código</div>
            <div class="lf-sub">Revisa tu bandeja de entrada. El código expira en 15 minutos.</div>
        </div>

        @if($success)
        <div class="lf-alert lf-alert-suc">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" style="flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $success }}
        </div>
        @endif
        @if($error)
        <div class="lf-alert lf-alert-err">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" style="flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ $error }}
        </div>
        @endif

        <div class="lf-field">
            <label class="lf-label">Código de 6 caracteres</label>
            <div class="lf-input-wrap">
                <span class="lf-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </span>
                <input type="text" wire:model="code" class="lf-input otp-input"
                       placeholder="XXXXXX" maxlength="6" autocomplete="one-time-code"
                       wire:keydown.enter="verifyCode">
            </div>
            <div class="otp-hint">Enviamos el código a <strong style="color:rgba(255,255,255,.65)">{{ $email }}</strong></div>
        </div>

        <button class="lf-btn" wire:click="verifyCode" wire:loading.attr="disabled" style="margin-top:6px">
            <svg wire:loading wire:target="verifyCode" class="lf-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15" style="display:none">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span wire:loading.remove wire:target="verifyCode">Verificar código</span>
            <span wire:loading wire:target="verifyCode" style="display:none">Verificando...</span>
        </button>

        <div style="text-align:center;margin-top:22px">
            <a href="{{ route('auth.forgot') }}" class="lf-forgot">¿No llegó? Reenviar código</a>
        </div>

    @elseif($step === 'reset')
        <div class="lf-head">
            <div class="lf-title">Nueva contraseña</div>
            <div class="lf-sub">Elige una contraseña segura de al menos 8 caracteres.</div>
        </div>

        @if($error)
        <div class="lf-alert lf-alert-err">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" style="flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ $error }}
        </div>
        @endif

        <div class="lf-field" x-data="{ showNew: false }">
            <label class="lf-label">Nueva contraseña</label>
            <div class="lf-input-wrap">
                <span class="lf-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <input :type="showNew ? 'text' : 'password'" wire:model="newPass" class="lf-input lf-input-pass"
                       placeholder="Mínimo 8 caracteres" autocomplete="new-password"
                       wire:keydown.enter="resetPassword">
                <button type="button" class="lf-eye" @click="showNew = !showNew" tabindex="-1">
                    <svg x-show="!showNew" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showNew" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="lf-field" x-data="{ showConf: false }">
            <label class="lf-label">Confirmar contraseña</label>
            <div class="lf-input-wrap">
                <span class="lf-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </span>
                <input :type="showConf ? 'text' : 'password'" wire:model="confirm" class="lf-input lf-input-pass"
                       placeholder="Repite la contraseña" autocomplete="new-password"
                       wire:keydown.enter="resetPassword">
                <button type="button" class="lf-eye" @click="showConf = !showConf" tabindex="-1">
                    <svg x-show="!showConf" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showConf" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        <button class="lf-btn" wire:click="resetPassword" wire:loading.attr="disabled" style="margin-top:6px">
            <svg wire:loading wire:target="resetPassword" class="lf-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15" style="display:none">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span wire:loading.remove wire:target="resetPassword">Guardar contraseña</span>
            <span wire:loading wire:target="resetPassword" style="display:none">Guardando...</span>
        </button>

    @elseif($step === 'done')
        <div style="text-align:center;padding:12px 0 20px">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                <svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" width="26" height="26">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="lf-title" style="margin-bottom:8px">¡Contraseña actualizada!</div>
            <div class="lf-sub" style="margin-bottom:28px">Tu contraseña fue cambiada correctamente. Ya puedes iniciar sesión.</div>
        </div>

        <a href="{{ route('auth.login') }}" class="lf-btn" style="text-decoration:none">
            Iniciar sesión
        </a>
    @endif

</div>
