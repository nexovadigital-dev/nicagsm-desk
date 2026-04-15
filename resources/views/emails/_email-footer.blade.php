{{--
    _email-footer.blade.php
    Footer estándar para todos los emails de soporte.

    Variables esperadas:
      $org       — Organization model (puede ser null en agent-invitation)
      $orgName   — string (siempre disponible)
    Variables opcionales:
      $ticket    — Ticket model (para mostrar número de referencia)
      $footerNote — string (texto extra por defecto "Gestión de Soporte")
--}}
@php
    $social       = $org->social_links ?? [];
    $accentColor  = $org->accent_color ?? '#7c3aed';
    $footNote     = $footerNote ?? 'Gestión de Soporte';
    $hasSocial    = !empty(array_filter($social));
    // SVG icons inline (24×24 viewBox, compatible con email)
    $icons = [
        'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.07C24 5.41 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.04V9.41c0-3.02 1.8-4.7 4.54-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.95.93-1.95 1.88v2.27h3.32l-.53 3.49h-2.79V24C19.61 23.1 24 18.1 24 12.07z"/></svg>',
        'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="url(#ig)"><defs><linearGradient id="ig" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" stop-color="#f09433"/><stop offset="25%" stop-color="#e6683c"/><stop offset="50%" stop-color="#dc2743"/><stop offset="75%" stop-color="#cc2366"/><stop offset="100%" stop-color="#bc1888"/></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
        'x'         => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#000"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.259 5.631zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        'whatsapp'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
        'telegram'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#2AABEE"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
        'youtube'   => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#FF0000"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>',
    ];
@endphp
<div style="padding:20px 32px 16px;background:#f9fafb;border-top:1px solid #f3f4f6;text-align:center">
    {{-- Org name + separator --}}
    <p style="margin:0 0 4px;font-size:12px;font-weight:700;color:#374151;letter-spacing:.02em">
        {{ $orgName }}
    </p>
    <p style="margin:0 0 12px;font-size:11px;color:#9ca3af">
        {{ $footNote }}
    </p>

    {{-- Social icons (solo si hay alguno configurado) --}}
    @if($hasSocial)
    <p style="margin:0 0 8px;font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">
        Síguenos
    </p>
    <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
        @if(!empty($social['facebook']))
        <a href="{{ $social['facebook'] }}" target="_blank" style="display:inline-block;line-height:0">
            {!! $icons['facebook'] !!}
        </a>
        @endif
        @if(!empty($social['instagram']))
        <a href="{{ $social['instagram'] }}" target="_blank" style="display:inline-block;line-height:0">
            {!! $icons['instagram'] !!}
        </a>
        @endif
        @if(!empty($social['x']))
        <a href="{{ $social['x'] }}" target="_blank" style="display:inline-block;line-height:0">
            {!! $icons['x'] !!}
        </a>
        @endif
        @if(!empty($social['whatsapp']))
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $social['whatsapp']) }}" target="_blank" style="display:inline-block;line-height:0">
            {!! $icons['whatsapp'] !!}
        </a>
        @endif
        @if(!empty($social['telegram']))
        <a href="{{ $social['telegram'] }}" target="_blank" style="display:inline-block;line-height:0">
            {!! $icons['telegram'] !!}
        </a>
        @endif
        @if(!empty($social['youtube']))
        <a href="{{ $social['youtube'] }}" target="_blank" style="display:inline-block;line-height:0">
            {!! $icons['youtube'] !!}
        </a>
        @endif
    </div>
    @endif

    {{-- Ticket ref (opcional) --}}
    @isset($ticket)
    <p style="margin:0 0 4px;font-size:10px;color:#d1d5db">
        Ref: {{ $ticket->ticket_number }} · Este mensaje es automático
    </p>
    @endisset
    @if(!isset($ticket))
    <p style="margin:0;font-size:10px;color:#d1d5db">
        Este mensaje fue enviado automáticamente, no respondas a este correo
    </p>
    @endif
</div>
