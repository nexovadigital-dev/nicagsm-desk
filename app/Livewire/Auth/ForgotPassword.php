<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $step    = 'email';  // 'email' | 'code' | 'reset' | 'done'
    public string $email   = '';
    public string $code    = '';
    public string $newPass = '';
    public string $confirm = '';
    public string $error   = '';
    public string $success = '';

    public function sendCode(): void
    {
        $this->error = '';
        $this->email = strtolower(trim($this->email));

        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->error = 'Introduce un email válido.';
            return;
        }

        // Check SMTP is configured before attempting to send
        $smtpHost = config('mail.mailers.smtp.host', '');
        $smtpUser = config('mail.mailers.smtp.username', '');
        if (empty($smtpHost) || in_array($smtpHost, ['', 'mailpit', 'localhost', 'smtp.example.com'])) {
            $this->error = 'El servidor de correo no está configurado. Contacta al administrador para habilitar el envío de emails antes de restablecer la contraseña.';
            return;
        }
        if (empty($smtpUser)) {
            $this->error = 'Las credenciales del servidor de correo no están configuradas. Contacta al administrador.';
            return;
        }

        $user = User::where('email', $this->email)->first();

        // Always show success to prevent email enumeration
        if ($user) {
            $code = strtoupper(Str::random(6));
            Cache::put('pwd_reset_' . md5($this->email), $code, now()->addMinutes(15));

            try {
                Mail::raw(
                    "Tu código de recuperación de contraseña es: {$code}\n\nExpira en 15 minutos.\n\nSi no solicitaste esto, ignora este correo.",
                    fn ($m) => $m
                        ->to($user->email)
                        ->subject('Recuperar contraseña — Nexova Desk')
                );
            } catch (\Throwable $e) {
                $this->error = 'No se pudo enviar el email. Verifica que el servidor SMTP esté correctamente configurado.';
                return;
            }
        }

        $this->success = 'Si el email está registrado, recibirás un código en los próximos minutos.';
        $this->step    = 'code';
    }

    public function verifyCode(): void
    {
        $this->error = '';
        $stored = Cache::get('pwd_reset_' . md5($this->email));

        if (! $stored || strtoupper(trim($this->code)) !== $stored) {
            $this->error = 'Código incorrecto o expirado.';
            return;
        }

        $this->success = '';
        $this->step    = 'reset';
    }

    public function resetPassword(): void
    {
        $this->error = '';

        if (strlen($this->newPass) < 8) {
            $this->error = 'La contraseña debe tener mínimo 8 caracteres.';
            return;
        }
        if ($this->newPass !== $this->confirm) {
            $this->error = 'Las contraseñas no coinciden.';
            return;
        }

        $stored = Cache::get('pwd_reset_' . md5($this->email));
        if (! $stored) {
            $this->error = 'El código expiró. Solicita uno nuevo.';
            $this->step  = 'email';
            return;
        }

        $user = User::where('email', $this->email)->first();
        if ($user) {
            $user->update(['password' => Hash::make($this->newPass)]);
            Cache::forget('pwd_reset_' . md5($this->email));
        }

        $this->step    = 'done';
        $this->success = 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('layouts.auth');
    }
}
