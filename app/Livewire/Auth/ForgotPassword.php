<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\SmtpSetting;
use App\Services\OrgMailer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
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

        // ── Rate limit: max 3 intentos por IP cada 15 minutos ──
        $rateLimitKey = 'pwd_reset_ip_' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $mins    = ceil($seconds / 60);
            $this->error = "Demasiados intentos. Espera {$mins} minuto(s) antes de volver a intentarlo.";
            return;
        }
        RateLimiter::hit($rateLimitKey, 900); // 15 min

        // ── Validar que el email pertenece a un usuario de esta instalación ──
        $user = User::where('email', $this->email)->whereNotNull('organization_id')->first();

        if (! $user) {
            $this->error = 'No existe una cuenta activa con ese correo en este panel. Contacta al administrador de tu organización.';
            return;
        }

        // ── Verificar SMTP configurado (panel admin o .env) ──
        $org  = $user->organization;
        $smtp = $org ? SmtpSetting::forOrg($org->id) : null;
        $hasOrgSmtp = $smtp && $smtp->enabled && !empty($smtp->host) && !empty($smtp->username);

        if (! $hasOrgSmtp) {
            // Fallback: .env SMTP
            $smtpHost = config('mail.mailers.smtp.host', '');
            $smtpUser = config('mail.mailers.smtp.username', '');
            if (empty($smtpHost) || in_array($smtpHost, ['', 'mailpit', 'localhost', 'smtp.example.com']) || empty($smtpUser)) {
                $this->error = 'El servidor de correo no está configurado. El administrador debe configurar el SMTP en Integraciones → Email & SMTP.';
                return;
            }
        }

        // ── Rate limit por email: max 2 códigos cada 30 minutos ──
        $emailKey = 'pwd_reset_email_' . md5($this->email);
        if (RateLimiter::tooManyAttempts($emailKey, 2)) {
            $this->error = 'Ya enviamos un código a este correo recientemente. Revisa tu bandeja o espera 30 minutos.';
            return;
        }
        RateLimiter::hit($emailKey, 1800); // 30 min

        $code = strtoupper(Str::random(6));
        Cache::put('pwd_reset_' . md5($this->email), $code, now()->addMinutes(15));

        $orgName = $user->organization?->name ?? config('app.name', 'Nexova Desk Edge');

        try {
            $html = $this->buildEmailHtml($code, $orgName, $user->name);
            $send = function ($m) use ($user, $orgName, $html) {
                $m->to($user->email)
                  ->subject("Recuperar contraseña — {$orgName}")
                  ->html($html);
            };

            if ($org && $hasOrgSmtp) {
                $mailerName = OrgMailer::mailerNameFor($org);
                [$fromAddr, $fromName] = OrgMailer::fromFor($org);
                Mail::mailer($mailerName)->send([], [], function ($m) use ($user, $orgName, $html, $fromAddr, $fromName) {
                    $m->to($user->email)
                      ->from($fromAddr, $fromName)
                      ->subject("Recuperar contraseña — {$orgName}")
                      ->html($html);
                });
            } else {
                Mail::send([], [], $send);
            }
        } catch (\Throwable $e) {
            $this->error = 'No se pudo enviar el email. Verifica la configuración SMTP en Integraciones → Email & SMTP.';
            Cache::forget('pwd_reset_' . md5($this->email));
            return;
        }

        $this->success = 'Código enviado. Revisa tu bandeja de entrada.';
        $this->step    = 'code';
    }

    public function verifyCode(): void
    {
        $this->error = '';

        // Rate limit: max 5 intentos de código por IP
        $rateLimitKey = 'pwd_code_ip_' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $this->error = 'Demasiados intentos incorrectos. Solicita un nuevo código.';
            $this->step  = 'email';
            return;
        }

        $stored = Cache::get('pwd_reset_' . md5($this->email));

        if (! $stored || strtoupper(trim($this->code)) !== $stored) {
            RateLimiter::hit($rateLimitKey, 900);
            $this->error = 'Código incorrecto o expirado.';
            return;
        }

        RateLimiter::clear($rateLimitKey);
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

        $user = User::where('email', $this->email)->whereNotNull('organization_id')->first();
        if ($user) {
            $user->update(['password' => Hash::make($this->newPass)]);
            Cache::forget('pwd_reset_' . md5($this->email));
        }

        $this->step    = 'done';
        $this->success = 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.';
    }

    private function buildEmailHtml(string $code, string $orgName, string $userName): string
    {
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#0d1117;font-family:'Inter',Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0d1117;padding:40px 20px">
    <tr><td align="center">
      <table width="100%" style="max-width:480px;background:#111827;border-radius:14px;overflow:hidden;border:1px solid rgba(255,255,255,.08)">
        <!-- Header -->
        <tr><td style="padding:28px 32px 20px;border-bottom:1px solid rgba(255,255,255,.06)">
          <p style="margin:0;font-size:15px;font-weight:700;color:#fff;letter-spacing:-.02em">{$orgName}</p>
          <p style="margin:4px 0 0;font-size:11px;color:#22c55e;font-weight:600;letter-spacing:.08em;text-transform:uppercase">by Nexova Desk Edge</p>
        </td></tr>
        <!-- Body -->
        <tr><td style="padding:32px">
          <p style="margin:0 0 8px;font-size:20px;font-weight:700;color:#fff;letter-spacing:-.02em">Recuperar contraseña</p>
          <p style="margin:0 0 28px;font-size:13px;color:rgba(255,255,255,.5);line-height:1.6">
            Hola {$userName}, recibiste este correo porque solicitaste restablecer tu contraseña.
          </p>
          <!-- Code box -->
          <div style="background:#0d1117;border:1px solid rgba(34,197,94,.25);border-radius:10px;padding:22px;text-align:center;margin-bottom:28px">
            <p style="margin:0 0 6px;font-size:10.5px;font-weight:700;color:rgba(255,255,255,.4);letter-spacing:.1em;text-transform:uppercase">Tu código de verificación</p>
            <p style="margin:0;font-size:34px;font-weight:800;color:#22c55e;letter-spacing:.18em;font-family:monospace">{$code}</p>
            <p style="margin:10px 0 0;font-size:11.5px;color:rgba(255,255,255,.35)">Expira en 15 minutos</p>
          </div>
          <p style="margin:0;font-size:12px;color:rgba(255,255,255,.3);line-height:1.6">
            Si no solicitaste este código, puedes ignorar este correo con seguridad. Nadie puede acceder a tu cuenta sin él.
          </p>
        </td></tr>
        <!-- Footer -->
        <tr><td style="padding:16px 32px;border-top:1px solid rgba(255,255,255,.06)">
          <p style="margin:0;font-size:11px;color:rgba(255,255,255,.25);text-align:center">
            © {$year} {$orgName} · Nexova Desk Edge · Este es un mensaje automático, no respondas.
          </p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('layouts.auth');
    }
}
