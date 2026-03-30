<x-filament-panels::page>
<style>
.sub-wrap    { display:flex; flex-direction:column; gap:20px; max-width:900px; }
.sub-card    { background:var(--c-surface,#fff); border:1px solid var(--c-border,#e3e6ea); border-radius:12px; overflow:hidden; }
.sub-head    { padding:15px 20px; border-bottom:1px solid var(--c-border,#e3e6ea); display:flex; align-items:center; justify-content:space-between; }
.sub-head-title { font-size:14px; font-weight:800; color:var(--c-text,#111827); }
.sub-body    { padding:20px; }
.sub-badge   { display:inline-flex; align-items:center; gap:5px; padding:3px 12px; border-radius:99px; font-size:12px; font-weight:700; }
.sub-btn     { display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; border:none; transition:opacity .15s; text-decoration:none; }
.sub-btn:hover { opacity:.85; }
.sub-btn-sm  { padding:6px 14px; font-size:12px; }
.sub-method  { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border:1px solid var(--c-border,#e3e6ea); border-radius:10px; margin-bottom:8px; cursor:pointer; transition:border-color .15s; }
.sub-method:hover { border-color:#3b82f6; }
.sub-tbl     { width:100%; border-collapse:collapse; font-size:12px; }
.sub-tbl th  { padding:8px 12px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--c-sub,#6b7280); border-bottom:1px solid var(--c-border,#e3e6ea); }
.sub-tbl td  { padding:10px 12px; border-bottom:1px solid var(--c-border,#e3e6ea); color:var(--c-text,#111827); }
.sub-tbl tr:last-child td { border-bottom:none; }
.sub-stat    { display:flex; flex-direction:column; gap:2px; }
.sub-stat-val { font-size:18px; font-weight:800; color:var(--c-text,#111827); }
.sub-stat-lbl { font-size:11px; color:var(--c-sub,#6b7280); }
.sub-grid4   { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:12px; }
.sub-limit   { background:var(--c-bg,#f9fafb); border:1px solid var(--c-border,#e3e6ea); border-radius:8px; padding:12px 14px; }
.sub-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:50; display:flex; align-items:center; justify-content:center; }
.sub-modal   { background:var(--c-surface,#fff); border-radius:14px; width:460px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.sub-modal-head { padding:18px 22px; border-bottom:1px solid var(--c-border,#e3e6ea); font-size:15px; font-weight:800; color:var(--c-text,#111827); }
.sub-modal-body { padding:22px; }
.sub-modal-foot { padding:14px 22px; border-top:1px solid var(--c-border,#e3e6ea); display:flex; gap:10px; justify-content:flex-end; }
.sub-input   { width:100%; padding:9px 12px; border:1px solid var(--c-border,#e3e6ea); border-radius:8px; font-size:13px; font-family:monospace; background:var(--c-surface,#fff); color:var(--c-text,#111827); outline:none; box-sizing:border-box; }
.sub-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.12); }
.sub-divider { height:1px; background:var(--c-border,#e3e6ea); margin:16px 0; }
</style>

@php
$org             = $this->org;
$plan            = $this->plan;
$proPlan         = $this->proPlan;
$availablePlans  = $this->availablePlans;
$sub             = $this->activeSubscription;
$pendingTx       = $this->activePendingTx;
$isPaid          = $org?->plan !== 'free' && $org?->plan !== null;
$isFree          = $org?->plan === 'free' || $org?->plan === null;
$isCancelled     = $sub?->status === 'cancelled';
$daysLeft        = $sub?->ends_at ? (int) now()->diffInDays($sub->ends_at, false) : null;
$nearExpiry      = $daysLeft !== null && $daysLeft <= 7;
$cryptoMethods   = $this->activeCryptoMethods;
$isMpActive      = $this->isMpActive;
$methodLabels = ['usdt_trc20'=>'USDT · TRC20','usdt_bep20'=>'USDT · BEP20','usdt_polygon'=>'USDT · Polygon','usdc_trc20'=>'USDC · TRC20','usdc_bep20'=>'USDC · BEP20','usdc_polygon'=>'USDC · Polygon'];
$txColors  = ['pending'=>['bg'=>'#fef9c3','color'=>'#854d0e','l'=>'Pendiente'],'confirmed'=>['bg'=>'#dcfce7','color'=>'#15803d','l'=>'Confirmado'],'failed'=>['bg'=>'#fee2e2','color'=>'#b91c1c','l'=>'Fallido'],'expired'=>['bg'=>'#f3f4f6','color'=>'#6b7280','l'=>'Expirado'],'cancelled'=>['bg'=>'#fef2f2','color'=>'#b91c1c','l'=>'Cancelado']];
@endphp

<div class="sub-wrap"
     x-data="{
        cryptoModal: false,
        cancelModal: false,
        countdown: 0,
        timer: null,
        expiryTs: 0,
        startCountdown(iso) {
            this.expiryTs = new Date(iso).getTime();
            clearInterval(this.timer);
            this.timer = setInterval(() => {
                const diff = Math.max(0, Math.floor((this.expiryTs - Date.now()) / 1000));
                this.countdown = diff;
                if (diff === 0) clearInterval(this.timer);
            }, 1000);
        },
        get countdownStr() {
            const m = Math.floor(this.countdown / 60).toString().padStart(2,'0');
            const s = (this.countdown % 60).toString().padStart(2,'0');
            return m + ':' + s;
        }
     }"
     @open-crypto-modal.window="cryptoModal=true; startCountdown($wire.cryptoExpiry)"
     @close-crypto-modal.window="cryptoModal=false; clearInterval(timer)"
     @close-cancel-modal.window="cancelModal=false">

    {{-- Page header --}}
    <div>
        <h1 style="font-size:20px;font-weight:800;color:var(--c-text,#111827);margin:0">Facturación y Suscripción</h1>
        <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:4px 0 0">Información de tu plan, historial de pagos y opciones de renovación</p>
    </div>

    {{-- ─── PLAN ACTUAL ─── --}}
    <div class="sub-card">
        <div class="sub-head">
            <span class="sub-head-title">Plan actual</span>
            @if($isPaid && !$isCancelled)
                <div style="display:flex;gap:8px">
                    <button class="sub-btn sub-btn-sm"
                            style="background:#1e293b;color:#f8fafc"
                            onclick="document.getElementById('upgrade-section').scrollIntoView({behavior:'smooth'})">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Renovar
                    </button>
                    <button @click="cancelModal=true" class="sub-btn sub-btn-sm" style="background:#fee2e2;color:#b91c1c">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        Cancelar plan
                    </button>
                </div>
            @elseif($isCancelled)
                <span class="sub-badge" style="background:#fee2e2;color:#b91c1c">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01"/></svg>
                    Cancelado
                </span>
            @elseif($isFree)
                <span class="sub-badge" style="background:#f3f4f6;color:#6b7280">Gratuito</span>
            @endif
        </div>
        <div class="sub-body">

            {{-- Plan name + price + dates --}}
            <div style="display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap;margin-bottom:20px">
                {{-- Plan badge --}}
                <div>
                    @if($isPaid)
                        <div style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);border-radius:10px;padding:12px 20px;text-align:center">
                            <div style="font-size:11px;font-weight:700;color:#22c55e;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Plan</div>
                            <div style="font-size:24px;font-weight:900;color:var(--c-text,#111827);letter-spacing:-.02em">{{ ucfirst($plan?->name ?? $org?->plan) }}</div>
                            <div style="font-size:13px;font-weight:700;color:#22c55e">${{ number_format($plan?->price_usd ?? 0, 2) }}/mes</div>
                        </div>
                    @else
                        <div style="background:#f3f4f6;border:1px solid var(--c-border,#e3e6ea);border-radius:10px;padding:12px 20px;text-align:center">
                            <div style="font-size:11px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Plan</div>
                            <div style="font-size:24px;font-weight:900;color:var(--c-text,#111827);letter-spacing:-.02em">Free</div>
                            <div style="font-size:13px;font-weight:700;color:var(--c-sub,#6b7280)">$0/mes</div>
                        </div>
                    @endif
                </div>

                {{-- Subscription dates --}}
                <div style="display:flex;flex-direction:column;gap:10px;flex:1;min-width:200px">
                    @if($sub && $sub->starts_at)
                    <div style="display:flex;justify-content:space-between;font-size:13px">
                        <span style="color:var(--c-sub,#6b7280)">Fecha de inicio</span>
                        <span style="font-weight:600">{{ $sub->starts_at->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($sub && $sub->ends_at)
                    <div style="display:flex;justify-content:space-between;font-size:13px">
                        <span style="color:var(--c-sub,#6b7280)">
                            {{ $isCancelled ? 'Acceso hasta' : 'Próxima renovación' }}
                        </span>
                        <span style="font-weight:600;color:{{ $nearExpiry ? '#ef4444' : 'inherit' }}">
                            {{ $sub->ends_at->format('d/m/Y') }}
                            ({{ $sub->ends_at->diffForHumans() }})
                        </span>
                    </div>
                    @if($daysLeft !== null && $daysLeft >= 0)
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--c-sub,#6b7280);margin-bottom:4px">
                            <span>Tiempo restante</span>
                            <span style="font-weight:600;color:{{ $nearExpiry ? '#ef4444' : '#22c55e' }}">{{ $daysLeft }} día(s)</span>
                        </div>
                        <div style="height:5px;background:var(--c-border,#e3e6ea);border-radius:99px;overflow:hidden">
                            @php
                                $totalDays = $sub->starts_at ? (int)$sub->starts_at->diffInDays($sub->ends_at) : 30;
                                $pct = $totalDays > 0 ? round(($daysLeft / $totalDays) * 100) : 0;
                            @endphp
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $nearExpiry ? '#ef4444' : '#22c55e' }};border-radius:99px;transition:width .4s"></div>
                        </div>
                    </div>
                    @endif
                    @elseif($isFree)
                    <div style="font-size:13px;color:var(--c-sub,#6b7280)">Sin vencimiento — plan gratuito</div>
                    @endif
                </div>
            </div>

            <div class="sub-divider"></div>

            {{-- Plan limits from DB --}}
            <div style="font-size:12px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">
                Límites de tu plan
            </div>
            <div class="sub-grid4">
                <div class="sub-limit">
                    <div class="sub-stat-val">{{ ($plan?->max_agents ?? 3) >= 999 ? '∞' : ($plan?->max_agents ?? 3) }}</div>
                    <div class="sub-stat-lbl">Agentes</div>
                </div>
                <div class="sub-limit">
                    <div class="sub-stat-val">{{ ($plan?->max_widgets ?? 1) >= 999 ? '∞' : ($plan?->max_widgets ?? 1) }}</div>
                    <div class="sub-stat-lbl">Widgets</div>
                </div>
                <div class="sub-limit">
                    <div class="sub-stat-val">{{ ($plan?->max_sessions_per_day ?? 50) >= 999999 ? '∞' : number_format($plan?->max_sessions_per_day ?? 50) }}</div>
                    <div class="sub-stat-lbl">Sesiones / día</div>
                </div>
                <div class="sub-limit">
                    <div class="sub-stat-val">{{ ($plan?->max_messages_per_session ?? 20) >= 999 ? '∞' : ($plan?->max_messages_per_session ?? 20) }}</div>
                    <div class="sub-stat-lbl">Mensajes / sesión</div>
                </div>
                <div class="sub-limit">
                    <div class="sub-stat-val" style="color:{{ ($plan?->ai_blocked ?? true) ? '#ef4444' : '#22c55e' }}">
                        {{ ($plan?->ai_blocked ?? true) ? 'No' : 'Sí' }}
                    </div>
                    <div class="sub-stat-lbl">IA habilitada</div>
                </div>
                <div class="sub-limit">
                    <div class="sub-stat-val" style="color:#22c55e">{{ $isPro ? 'Sí' : 'No' }}</div>
                    <div class="sub-stat-lbl">Telegram</div>
                </div>
            </div>

            {{-- Cancelled notice --}}
            @if($isCancelled)
            <div style="margin-top:16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;font-size:13px;color:#991b1b">
                <strong>Suscripción cancelada.</strong> Tendrás acceso Pro hasta el {{ $sub->ends_at->format('d/m/Y') }}.
                Después, tu cuenta pasará automáticamente al plan Free.
                <button onclick="document.getElementById('upgrade-section').scrollIntoView({behavior:'smooth'})"
                        style="background:none;border:none;cursor:pointer;color:#991b1b;font-weight:700;text-decoration:underline;font-size:13px;margin-left:4px">
                    Reactivar →
                </button>
            </div>
            @endif

            {{-- Near expiry warning --}}
            @if($isPaid && !$isCancelled && $nearExpiry)
            <div style="margin-top:16px;background:#fef9c3;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;font-size:13px;color:#92400e">
                <strong>Tu plan vence en {{ $daysLeft }} día(s).</strong> Renueva para no perder el acceso Pro.
            </div>
            @endif

        </div>
    </div>

    {{-- ─── SECCIÓN UPGRADE/RENOVAR ─── --}}
    <div class="sub-card" id="upgrade-section"
         x-data="{ selectedPlan: '{{ $availablePlans->first()?->slug ?? 'pro' }}' }">
        <div class="sub-head">
            <span class="sub-head-title">
                @if($isPaid && !$isCancelled) Renovar suscripción
                @elseif($isCancelled) Reactivar suscripción
                @else Actualizar plan
                @endif
            </span>
        </div>
        <div class="sub-body">

            {{-- Payment return notice --}}
            @if(request('payment') === 'success')
            <div style="margin-bottom:16px;background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px 16px;font-size:13px;font-weight:700;color:#15803d">
                ✓ Pago recibido. Tu plan se activará en breve.
            </div>
            @elseif(request('payment') === 'failed')
            <div style="margin-bottom:16px;background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;font-size:13px;font-weight:700;color:#b91c1c">
                El pago fue rechazado. Intenta de nuevo o usa otro método.
            </div>
            @endif

            @if($availablePlans->isEmpty())
            <div style="background:var(--c-bg,#f9fafb);border-radius:8px;padding:16px;font-size:13px;color:var(--c-sub,#6b7280);text-align:center">
                No hay planes disponibles. Contacta al administrador.
            </div>
            @else

            {{-- Plan selector --}}
            @if($availablePlans->count() > 1)
            <div style="margin-bottom:20px">
                <div style="font-size:12px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">Elige tu plan</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px">
                    @foreach($availablePlans as $p)
                    <button @click="selectedPlan = '{{ $p->slug }}'"
                            :style="selectedPlan === '{{ $p->slug }}' ? 'border-color:#22c55e;background:rgba(34,197,94,.06)' : ''"
                            style="border:2px solid var(--c-border,#e3e6ea);border-radius:12px;padding:14px 16px;cursor:pointer;background:transparent;text-align:left;transition:all .15s">
                        <div style="font-size:16px;font-weight:900;color:var(--c-text,#111827)">{{ $p->name }}</div>
                        <div style="font-size:18px;font-weight:800;color:#22c55e;margin:4px 0">${{ number_format($p->price_usd, 2) }}<span style="font-size:12px;font-weight:500;color:var(--c-sub,#6b7280)">/mes</span></div>
                        @if($p->features)
                        @php $feats = is_array($p->features) ? array_slice($p->features, 0, 3) : []; @endphp
                        @foreach($feats as $f)
                        <div style="font-size:11px;color:var(--c-sub,#6b7280);margin-top:2px">✓ {{ $f }}</div>
                        @endforeach
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>
            <div class="sub-divider"></div>
            @else
            {{-- Single plan: show features if free/cancelled --}}
            @if($isFree || $isCancelled)
            @php $fp = $availablePlans->first(); @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:12px">
                <div>
                    <div style="font-size:15px;font-weight:900;color:var(--c-text,#111827)">{{ $fp->name }}</div>
                    @if($fp->features)
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px">
                        @foreach((is_array($fp->features) ? $fp->features : []) as $f)
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:var(--c-text,#111827)">
                            <svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>{{ $f }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div style="font-size:24px;font-weight:900;color:#22c55e">${{ number_format($fp->price_usd, 2) }}<span style="font-size:13px;font-weight:500;color:var(--c-sub,#6b7280)">/mes</span></div>
            </div>
            <div class="sub-divider"></div>
            @endif
            @endif

            {{-- Pending TX notice --}}
            @if($pendingTx && !$pendingTx->tx_hash)
            <div style="margin-bottom:16px;background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:12px 16px;font-size:13px;color:#92400e">
                Tienes un pago cripto pendiente (<strong>{{ $methodLabels[$pendingTx->method] ?? $pendingTx->method }}</strong>,
                {{ number_format($pendingTx->amount_crypto, 2) }} {{ $pendingTx->currency }}).
                Expira {{ $pendingTx->expires_at->diffForHumans() }}.
                <button wire:click="initCryptoPay('{{ $pendingTx->method }}', '{{ $pendingTx->plan?->slug ?? 'pro' }}')"
                        style="background:none;border:none;cursor:pointer;color:#92400e;text-decoration:underline;font-size:13px">
                    Ver detalles
                </button>
            </div>
            @endif

            @if(count($cryptoMethods) > 0 || $isMpActive)
            <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:0 0 14px">Selecciona tu método de pago:</p>

            {{-- Crypto methods --}}
            @if(count($cryptoMethods) > 0)
            <div style="margin-bottom:16px">
                <div style="font-size:12px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">Criptomonedas (USDT / USDC)</div>
                @foreach($cryptoMethods as $method => $cfg)
                @foreach($availablePlans as $p)
                <button x-show="selectedPlan === '{{ $p->slug }}'"
                        wire:click="initCryptoPay('{{ $method }}', '{{ $p->slug }}')"
                        wire:loading.attr="disabled"
                        class="sub-method" style="width:100%;text-align:left;background:transparent">
                    <div>
                        <div style="font-weight:700;font-size:13px;color:var(--c-text,#111827)">{{ $methodLabels[$method] ?? $method }}</div>
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">Pago manual — confirma el TX hash · Verificación blockchain</div>
                    </div>
                    <div style="font-size:13px;color:#22c55e;font-weight:700">${{ number_format($p->price_usd, 2) }} →</div>
                </button>
                @endforeach
                @endforeach
            </div>
            @endif

            {{-- MercadoPago --}}
            @if($isMpActive)
            <div style="margin-bottom:16px">
                <div style="font-size:12px;font-weight:700;color:var(--c-sub,#6b7280);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">Tarjeta / PSE / Efectivo</div>
                @foreach($availablePlans as $p)
                <button x-show="selectedPlan === '{{ $p->slug }}'"
                        wire:click="initMpPay('{{ $p->slug }}')"
                        wire:loading.attr="disabled"
                        class="sub-method" style="width:100%;text-align:left;background:transparent">
                    <div>
                        <div style="font-weight:700;font-size:13px;color:var(--c-text,#111827)">MercadoPago — Plan {{ $p->name }}</div>
                        <div style="font-size:11px;color:var(--c-sub,#6b7280)">Tarjeta, PSE, Efecty, Nequi y más · Acreditación automática</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="background:#009ee3;color:#fff;border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700">MP</span>
                        <span style="font-size:12px;color:#22c55e;font-weight:700">${{ number_format($p->price_usd, 2) }} →</span>
                    </div>
                </button>
                @endforeach
            </div>
            @endif

            @else
            <div style="background:var(--c-bg,#f9fafb);border-radius:8px;padding:16px;font-size:13px;color:var(--c-sub,#6b7280);text-align:center">
                No hay métodos de pago disponibles actualmente. Contacta al administrador.
            </div>
            @endif

            @endif {{-- end availablePlans not empty --}}
        </div>
    </div>

    {{-- ─── HISTORIAL DE PAGOS ─── --}}
    <div class="sub-card">
        <div class="sub-head">
            <span class="sub-head-title">Historial de pagos</span>
        </div>
        <div style="overflow-x:auto">
            <table class="sub-tbl">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Plan</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>TX Hash</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->recentTransactions as $tx)
                    @php $tc = $txColors[$tx->status] ?? $txColors['expired']; @endphp
                    <tr>
                        <td style="color:var(--c-sub,#6b7280);white-space:nowrap">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                        <td style="font-weight:600">{{ $tx->plan?->name ?? '—' }}</td>
                        <td>{{ $methodLabels[$tx->method] ?? $tx->method }}</td>
                        <td style="font-weight:700">${{ number_format($tx->amount_usd, 2) }}</td>
                        <td>
                            @if($tx->tx_hash)
                            <span style="font-family:monospace;font-size:11px;color:var(--c-sub,#6b7280)">
                                {{ substr($tx->tx_hash, 0, 8) }}…{{ substr($tx->tx_hash, -4) }}
                            </span>
                            @else
                            <span style="color:var(--c-sub,#6b7280)">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="sub-badge" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }}">{{ $tc['l'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--c-sub,#6b7280);padding:28px">Sin transacciones aún</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── MODAL: Pago Crypto ─── --}}
    <div class="sub-overlay" x-show="cryptoModal" x-cloak @click.self="cryptoModal=false" style="display:none">
        <div class="sub-modal">
            <div class="sub-modal-head">
                Pago con Criptomonedas
                <div style="font-size:12px;font-weight:500;color:var(--c-sub,#6b7280);margin-top:2px">
                    {{ $cryptoNetwork }} · {{ $cryptoCurrency }}
                </div>
            </div>
            <div class="sub-modal-body" style="display:flex;flex-direction:column;gap:16px">

                {{-- Countdown --}}
                <div style="text-align:center">
                    <div style="font-size:11px;color:var(--c-sub,#6b7280);margin-bottom:4px">Tiempo para enviar el pago</div>
                    <div style="font-size:28px;font-weight:900;color:var(--c-text,#111827);font-variant-numeric:tabular-nums"
                         :style="countdown < 300 ? 'color:#ef4444' : ''"
                         x-text="countdownStr"></div>
                </div>

                {{-- Amount --}}
                <div style="background:#f0fdf4;border:2px solid #22c55e;border-radius:10px;padding:16px;text-align:center">
                    <div style="font-size:11px;color:var(--c-sub,#6b7280);margin-bottom:4px">Monto exacto a enviar</div>
                    <div style="font-size:26px;font-weight:900;color:#15803d">
                        {{ $cryptoAmount }} <span style="font-size:16px">{{ $cryptoCurrency }}</span>
                    </div>
                    <div style="font-size:12px;color:var(--c-sub,#6b7280);margin-top:4px">Red: {{ $cryptoNetwork }}</div>
                </div>

                {{-- QR + wallet --}}
                <div style="display:flex;flex-direction:column;align-items:center;gap:10px">
                    @if($cryptoQrSvg)
                    <img src="data:image/svg+xml;base64,{{ $cryptoQrSvg }}" width="180" height="180"
                         style="border:1px solid var(--c-border,#e3e6ea);border-radius:8px">
                    @endif
                    <div style="background:var(--c-bg,#f9fafb);border:1px solid var(--c-border,#e3e6ea);border-radius:8px;padding:10px 14px;font-family:monospace;font-size:12px;word-break:break-all;text-align:center;color:var(--c-text,#111827)">
                        {{ $cryptoWallet }}
                    </div>
                    <button onclick="navigator.clipboard.writeText('{{ $cryptoWallet }}').then(()=>alert('Dirección copiada'))"
                            style="background:none;border:none;cursor:pointer;font-size:12px;color:#3b82f6;font-weight:700">
                        Copiar dirección
                    </button>
                </div>

                {{-- Hash --}}
                <div>
                    <div style="font-size:12px;font-weight:700;color:var(--c-sub,#6b7280);margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em">
                        Una vez enviado, pega el TX Hash aquí
                    </div>
                    <input wire:model="txHash" class="sub-input" placeholder="0x... o T...">
                </div>

                <div style="background:#fef9c3;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;line-height:1.5">
                    Verificamos automáticamente en la blockchain. Si el hash es correcto, el plan se activa al instante. De lo contrario, puede tomar hasta 24h.
                </div>
            </div>
            <div class="sub-modal-foot">
                <button @click="cryptoModal=false" class="sub-btn" style="background:var(--c-bg,#f3f4f6);color:var(--c-text,#374151)">Cancelar</button>
                <button wire:click="submitTxHash" class="sub-btn" style="background:#1e293b;color:#f8fafc">Enviar hash</button>
            </div>
        </div>
    </div>

    {{-- ─── MODAL: Cancelar suscripción ─── --}}
    <div class="sub-overlay" x-show="cancelModal" x-cloak @click.self="cancelModal=false" style="display:none">
        <div class="sub-modal" style="width:420px">
            <div class="sub-modal-head">¿Cancelar suscripción Pro?</div>
            <div class="sub-modal-body" style="display:flex;flex-direction:column;gap:14px">
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 16px;font-size:13px;color:#991b1b;line-height:1.6">
                    Si cancelas tu suscripción:
                    <ul style="margin:8px 0 0;padding-left:18px;line-height:1.8">
                        <li>Seguirás con acceso Pro hasta <strong>{{ $sub?->ends_at?->format('d/m/Y') ?? 'el vencimiento' }}</strong></li>
                        <li>Al vencer, tu cuenta pasará automáticamente al plan <strong>Free</strong></li>
                        <li>La IA, widgets extra y agentes adicionales serán deshabilitados</li>
                    </ul>
                </div>
                <p style="font-size:13px;color:var(--c-sub,#6b7280)">¿Estás seguro que deseas cancelar?</p>
            </div>
            <div class="sub-modal-foot">
                <button @click="cancelModal=false" class="sub-btn" style="background:var(--c-bg,#f3f4f6);color:var(--c-text,#374151)">No, mantener Pro</button>
                <button wire:click="cancelSubscription" @click="cancelModal=false" class="sub-btn" style="background:#ef4444;color:#fff">
                    Sí, cancelar
                </button>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
