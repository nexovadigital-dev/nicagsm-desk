{{--
    _email-footer.blade.php
    Footer estándar para todos los emails de soporte.
    NOTA: SVG no funciona en Gmail/Outlook. Se usan imágenes PNG de CDN.

    Variables esperadas:
      $org       — Organization model
      $orgName   — string
    Variables opcionales:
      $ticket    — Ticket model
      $footerNote — string
--}}
@php
    $social      = is_array($org->social_links ?? null) ? ($org->social_links ?? []) : [];
    $accentColor = $org->accent_color ?? '#7c3aed';
    $footNote    = $footerNote ?? 'Gestión de Soporte';
    $hasSocial   = !empty(array_filter($social, fn($v) => !empty(trim((string)$v))));

    // Iconos PNG via Simple Icons CDN (32x32, compatible con todos los clientes de email)
    $icons = [
        'facebook'  => ['img' => 'https://cdn.simpleicons.org/facebook/1877F2',   'label' => 'Facebook',  'color' => '#1877F2'],
        'instagram' => ['img' => 'https://cdn.simpleicons.org/instagram/E4405F',  'label' => 'Instagram', 'color' => '#E4405F'],
        'x'         => ['img' => 'https://cdn.simpleicons.org/x/000000',          'label' => 'X',         'color' => '#000000'],
        'whatsapp'  => ['img' => 'https://cdn.simpleicons.org/whatsapp/25D366',   'label' => 'WhatsApp',  'color' => '#25D366'],
        'telegram'  => ['img' => 'https://cdn.simpleicons.org/telegram/2AABEE',   'label' => 'Telegram',  'color' => '#2AABEE'],
        'youtube'   => ['img' => 'https://cdn.simpleicons.org/youtube/FF0000',    'label' => 'YouTube',   'color' => '#FF0000'],
    ];
@endphp
<div style="padding:20px 32px 16px;background:#f9fafb;border-top:1px solid #f3f4f6;text-align:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif">

    {{-- Nombre org --}}
    <p style="margin:0 0 2px;font-size:12px;font-weight:700;color:#374151;letter-spacing:.02em">
        {{ $orgName }}
    </p>
    <p style="margin:0 0 14px;font-size:11px;color:#9ca3af">
        {{ $footNote }}
    </p>

    {{-- Social icons --}}
    @if($hasSocial)
    <p style="margin:0 0 10px;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em">
        Síguenos
    </p>
    <div style="margin:0 0 14px">
        <!--[if mso]><table><tr><td><![endif]-->
        @if(!empty(trim((string)($social['facebook'] ?? ''))))
        <a href="{{ $social['facebook'] }}" target="_blank" style="display:inline-block;margin:0 6px;text-decoration:none" title="Facebook">
            <img src="{{ $icons['facebook']['img'] }}" width="24" height="24" alt="Facebook" style="display:inline-block;border:0;width:24px;height:24px;border-radius:4px">
        </a>
        @endif
        @if(!empty(trim((string)($social['instagram'] ?? ''))))
        <a href="{{ $social['instagram'] }}" target="_blank" style="display:inline-block;margin:0 6px;text-decoration:none" title="Instagram">
            <img src="{{ $icons['instagram']['img'] }}" width="24" height="24" alt="Instagram" style="display:inline-block;border:0;width:24px;height:24px;border-radius:4px">
        </a>
        @endif
        @if(!empty(trim((string)($social['x'] ?? ''))))
        <a href="{{ $social['x'] }}" target="_blank" style="display:inline-block;margin:0 6px;text-decoration:none" title="X / Twitter">
            <img src="{{ $icons['x']['img'] }}" width="24" height="24" alt="X" style="display:inline-block;border:0;width:24px;height:24px;border-radius:4px">
        </a>
        @endif
        @if(!empty(trim((string)($social['whatsapp'] ?? ''))))
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $social['whatsapp']) }}" target="_blank" style="display:inline-block;margin:0 6px;text-decoration:none" title="WhatsApp">
            <img src="{{ $icons['whatsapp']['img'] }}" width="24" height="24" alt="WhatsApp" style="display:inline-block;border:0;width:24px;height:24px;border-radius:4px">
        </a>
        @endif
        @if(!empty(trim((string)($social['telegram'] ?? ''))))
        <a href="{{ $social['telegram'] }}" target="_blank" style="display:inline-block;margin:0 6px;text-decoration:none" title="Telegram">
            <img src="{{ $icons['telegram']['img'] }}" width="24" height="24" alt="Telegram" style="display:inline-block;border:0;width:24px;height:24px;border-radius:4px">
        </a>
        @endif
        @if(!empty(trim((string)($social['youtube'] ?? ''))))
        <a href="{{ $social['youtube'] }}" target="_blank" style="display:inline-block;margin:0 6px;text-decoration:none" title="YouTube">
            <img src="{{ $icons['youtube']['img'] }}" width="24" height="24" alt="YouTube" style="display:inline-block;border:0;width:24px;height:24px;border-radius:4px">
        </a>
        @endif
        <!--[if mso]></td></tr></table><![endif]-->
    </div>
    @endif

    {{-- Ticket ref --}}
    @isset($ticket)
    <p style="margin:0 0 2px;font-size:10px;color:#d1d5db">
        Ref: {{ $ticket->ticket_number }} · Este mensaje es automático
    </p>
    @endisset
    @if(!isset($ticket))
    <p style="margin:0;font-size:10px;color:#d1d5db">
        Este mensaje fue enviado automáticamente
    </p>
    @endif
</div>
