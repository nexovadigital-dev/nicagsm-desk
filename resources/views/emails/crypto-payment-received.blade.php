<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pago cripto recibido</title>
<style>
  body { margin: 0; padding: 0; background: #f5f6f8; font-family: 'Inter', -apple-system, sans-serif; color: #1f2937; }
  .wrap { max-width: 580px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
  .header { background: #111827; padding: 28px 32px; }
  .header-logo { font-size: 18px; font-weight: 800; color: #fff; letter-spacing: -.5px; }
  .header-logo span { color: #22c55e; }
  .body { padding: 32px; }
  .alert { background: #fef9c3; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #92400e; font-weight: 700; }
  h2 { margin: 0 0 16px; font-size: 20px; color: #111827; }
  .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
  .detail-row:last-child { border-bottom: none; }
  .detail-label { color: #6b7280; }
  .detail-value { font-weight: 700; color: #111827; }
  .hash-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; font-family: monospace; font-size: 12px; color: #374151; word-break: break-all; margin: 20px 0; }
  .hash-box strong { display: block; font-family: sans-serif; font-size: 11px; color: #6b7280; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .04em; }
  .cta-btn { display: inline-block; background: #22c55e; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; margin-top: 20px; }
  .footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #f3f4f6; font-size: 11px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="header-logo">Nexova<span>Desk</span> HQ</div>
  </div>
  <div class="body">
    <div class="alert">
      ⚠ Acción requerida — verifica este pago en la blockchain antes de confirmar
    </div>

    <h2>Nuevo pago cripto recibido</h2>

    <div>
      <div class="detail-row">
        <span class="detail-label">Organización</span>
        <span class="detail-value">{{ $tx->organization?->name ?? '—' }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Plan</span>
        <span class="detail-value">{{ $tx->plan?->name ?? '—' }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Monto</span>
        <span class="detail-value">${{ number_format($tx->amount_usd, 2) }} USD</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Monto cripto</span>
        <span class="detail-value">{{ number_format($tx->amount_crypto, 2) }} {{ $tx->currency }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Red</span>
        <span class="detail-value">{{ strtoupper($tx->network ?? $tx->method) }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Dirección destino</span>
        <span class="detail-value" style="font-family:monospace;font-size:12px">{{ $tx->wallet_to }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Fecha</span>
        <span class="detail-value">{{ $tx->updated_at->format('d/m/Y H:i') }}</span>
      </div>
    </div>

    <div class="hash-box">
      <strong>TX Hash enviado por el cliente</strong>
      {{ $tx->tx_hash }}
    </div>

    @php
      $explorerUrl = $tx->explorerUrl();
    @endphp
    @if($explorerUrl)
    <p style="font-size:13px;color:#4b5563;margin:0 0 8px">
      Verifica el hash en el explorador de bloques antes de confirmar:
    </p>
    <a href="{{ $explorerUrl }}" style="display:inline-block;background:#f0fdf4;border:1px solid #86efac;color:#15803d;text-decoration:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;margin-bottom:20px">
      Ver en blockchain ↗
    </a>
    @endif

    <br>
    <a href="{{ url('/nx-hq/transactions-page') }}" class="cta-btn">
      Ir al panel de transacciones →
    </a>
  </div>
  <div class="footer">
    Nexova Digital Solutions &nbsp;·&nbsp; Este mensaje fue enviado automáticamente, no respondas a este correo.
  </div>
</div>
</body>
</html>
