<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nexova Desk — Demo</title>
    @viteReactRefresh
    @vite(['resources/css/widget.css'])
</head>
<body class="bg-gradient-to-br from-slate-800 to-slate-900 min-h-screen flex items-center justify-center">

    <div class="text-center text-white">
        <h1 class="text-3xl font-bold mb-2" style="color:#22c55e">Nexova<span style="color:#fff">Desk</span></h1>
        <p class="text-slate-400">Widget flotante activo · haz clic en el botón inferior derecho</p>
        @php
            $firstWidget = \App\Models\ChatWidget::first();
        @endphp
        @if($firstWidget)
            <p style="margin-top:8px;font-size:11px;color:#64748b">
                Token: <code style="color:#22c55e">{{ $firstWidget->token }}</code>
            </p>
        @endif
    </div>

    {{-- Configura la URL base y el token del widget --}}
    <script>
        window.NexovaChatConfig = {
            apiUrl: '{{ url('/') }}',
            @if($firstWidget)
            token: '{{ $firstWidget->token }}',
            @endif
        };
    </script>

    @vite(['resources/js/widget/widget.jsx'])
</body>
</html>
