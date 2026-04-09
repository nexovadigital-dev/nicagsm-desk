<div x-data="{ showPass: false }" class="lf-wrap">

    <div class="lf-head">
        <div class="lf-title">Bienvenido de vuelta</div>
        <div class="lf-sub">Inicia sesión en tu panel de administración.</div>
    </div>

    @if($error)
    <div class="lf-alert lf-alert-err">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" style="flex-shrink:0">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    {{-- Email --}}
    <div class="lf-field">
        <label class="lf-label">Correo electrónico</label>
        <div class="lf-input-wrap">
            <span class="lf-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <input type="email" wire:model="email" class="lf-input"
                   placeholder="tu@empresa.com"
                   autocomplete="email"
                   wire:keydown.enter="submit">
        </div>
    </div>

    {{-- Password --}}
    <div class="lf-field">
        <label class="lf-label">Contraseña</label>
        <div class="lf-input-wrap">
            <span class="lf-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </span>
            <input :type="showPass ? 'text' : 'password'" wire:model="password" class="lf-input lf-input-pass"
                   placeholder="••••••••"
                   autocomplete="current-password"
                   wire:keydown.enter="submit">
            <button type="button" class="lf-eye" @click="showPass = !showPass" tabindex="-1">
                <svg x-show="!showPass" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg x-show="showPass" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Remember + Forgot --}}
    <div class="lf-row">
        <label class="lf-check-wrap" x-data="{ checked: false }">
            <input type="checkbox" wire:model="remember" class="lf-check-input"
                   x-on:change="checked = $event.target.checked">
            <span class="lf-check-box" :class="checked ? 'lf-check-on' : ''">
                <svg class="lf-check-mark" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            <span class="lf-check-label">Recordarme por 7 días</span>
        </label>
        <a href="{{ route('auth.forgot') }}" class="lf-forgot">¿Olvidaste tu contraseña?</a>
    </div>

    {{-- Submit --}}
    <button class="lf-btn" wire:click="submit" wire:loading.attr="disabled">
        <svg wire:loading wire:target="submit" class="lf-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15" style="display:none">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        <span wire:loading.remove wire:target="submit">Iniciar sesión</span>
        <span wire:loading wire:target="submit" style="display:none">Verificando...</span>
    </button>

</div>
