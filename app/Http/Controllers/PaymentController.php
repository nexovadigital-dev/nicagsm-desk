<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\PaymentConfig;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // ── MercadoPago: crear preferencia ─────────────────────────────────────────
    public function mpInitiate(Request $request): JsonResponse
    {
        $request->validate([
            'organization_id' => 'required|integer',
            'plan_slug'       => 'required|string',
        ]);

        $org  = Organization::findOrFail($request->organization_id);
        abort_if(auth()->id() && auth()->user()->organization_id !== $org->id, 403);
        $plan = Plan::where('slug', $request->plan_slug)->firstOrFail();
        $mp   = PaymentConfig::where('method', 'mercadopago')->where('is_active', true)->first();

        if (! $mp || ! $mp->mp_access_token) {
            return response()->json(['error' => 'MercadoPago no configurado'], 422);
        }

        $accessToken = decrypt($mp->mp_access_token);
        $currency    = match($mp->mp_country ?? 'CO') {
            'CO' => 'COP', 'MX' => 'MXN', 'AR' => 'ARS',
            'PE' => 'PEN', 'CL' => 'CLP', 'UY' => 'UYU', 'BR' => 'BRL',
            default => 'COP',
        };

        // Exchange rate: approximate (MP handles conversion internally)
        $unitPrice = (float) $plan->price_usd;

        $externalRef = "org_{$org->id}_plan_{$plan->slug}_" . time();

        $preference = Http::withToken($accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [[
                    'title'       => "Plan {$plan->name} — Nexova Desk",
                    'quantity'    => 1,
                    'currency_id' => 'USD',
                    'unit_price'  => $unitPrice,
                ]],
                'back_urls' => [
                    'success' => url('/payment/mp/success'),
                    'failure' => url('/payment/mp/failure'),
                    'pending' => url('/payment/mp/pending'),
                ],
                'auto_return'        => 'approved',
                'notification_url'   => url('/api/webhooks/mercadopago'),
                'external_reference' => $externalRef,
                'expires'            => true,
                'expiration_date_to' => now()->addHours(2)->toIso8601String(),
            ]);

        if (! $preference->successful()) {
            Log::error('[MP] Preference creation failed', ['body' => $preference->body()]);
            return response()->json(['error' => 'Error creando preferencia MP'], 500);
        }

        $data = $preference->json();

        // Create pending transaction
        PaymentTransaction::create([
            'organization_id'   => $org->id,
            'plan_id'           => $plan->id,
            'method'            => 'mercadopago',
            'currency'          => $currency,
            'amount_usd'        => $plan->price_usd,
            'mp_preference_id'  => $data['id'],
            'status'            => 'pending',
            'pending_at'        => now(),
            'expires_at'        => now()->addHours(2),
        ]);

        return response()->json([
            'init_point'    => $data['init_point'],
            'sandbox_point' => $data['sandbox_init_point'] ?? null,
        ]);
    }

    // ── MercadoPago: webhook de notificación ──────────────────────────────────
    public function mpWebhook(Request $request): JsonResponse
    {
        $type = $request->input('type') ?? $request->input('topic');
        $id   = $request->input('data.id') ?? $request->input('id');

        Log::info('[MP Webhook]', ['type' => $type, 'id' => $id]);

        if ($type !== 'payment' || ! $id) {
            return response()->json(['ok' => true]);
        }

        $mp = PaymentConfig::where('method', 'mercadopago')->first();
        if (! $mp?->mp_access_token) {
            return response()->json(['error' => 'MP not configured'], 500);
        }

        $accessToken = decrypt($mp->mp_access_token);
        $payment     = Http::withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$id}")
            ->json();

        $externalRef = $payment['external_reference'] ?? null;
        $status      = $payment['status'] ?? null;

        Log::info('[MP Webhook] Payment details', ['status' => $status, 'ref' => $externalRef]);

        if (! $externalRef) {
            return response()->json(['ok' => true]);
        }

        // Find transaction by external_reference pattern: org_{id}_plan_{slug}_ts
        preg_match('/^org_(\d+)_plan_([^_]+)_/', $externalRef, $m);
        $orgId    = isset($m[1]) ? (int)$m[1] : null;
        $planSlug = $m[2] ?? null;

        if (! $orgId || ! $planSlug) {
            return response()->json(['ok' => true]);
        }

        // Find the pending transaction
        $tx = PaymentTransaction::where('organization_id', $orgId)
            ->where('method', 'mercadopago')
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (! $tx) {
            return response()->json(['ok' => true]);
        }

        $tx->update([
            'mp_payment_id'     => $id,
            'mp_payment_status' => $status,
        ]);

        if ($status === 'approved') {
            $this->activateSubscription($tx);
        } elseif (in_array($status, ['rejected', 'cancelled', 'refunded', 'charged_back'])) {
            $tx->update(['status' => 'failed']);
        }

        return response()->json(['ok' => true]);
    }

    // ── MercadoPago: páginas de retorno ──────────────────────────────────────
    public function mpSuccess(Request $request)
    {
        return redirect('/app/subscription?payment=success');
    }

    public function mpFailure(Request $request)
    {
        return redirect('/app/subscription?payment=failed');
    }

    public function mpPending(Request $request)
    {
        return redirect('/app/subscription?payment=pending');
    }

    // ── Crypto: iniciar transacción con QR ────────────────────────────────────
    public function cryptoInitiate(Request $request): JsonResponse
    {
        $request->validate([
            'organization_id' => 'required|integer',
            'plan_slug'       => 'required|string',
            'method'          => 'required|string',
        ]);

        $org    = Organization::findOrFail($request->organization_id);
        abort_if(auth()->id() && auth()->user()->organization_id !== $org->id, 403);
        $plan   = Plan::where('slug', $request->plan_slug)->firstOrFail();
        $method = $request->method;

        $config = PaymentConfig::where('method', $method)->where('is_active', true)->first();
        if (! $config?->wallet_address) {
            return response()->json(['error' => 'Método de pago no disponible'], 422);
        }

        // Cancel any previous pending crypto tx for this org
        PaymentTransaction::where('organization_id', $org->id)
            ->where('status', 'pending')
            ->whereIn('method', ['usdt_trc20', 'usdt_bep20', 'usdt_polygon', 'usdc_trc20', 'usdc_bep20', 'usdc_polygon'])
            ->update(['status' => 'expired']);

        $tx = PaymentTransaction::create([
            'organization_id' => $org->id,
            'plan_id'         => $plan->id,
            'method'          => $method,
            'network'         => $config->network,
            'currency'        => $config->currency,
            'amount_usd'      => $plan->price_usd,
            'amount_crypto'   => $plan->price_usd, // 1:1 USDT/USDC ≈ USD
            'wallet_to'       => $config->wallet_address,
            'status'          => 'pending',
            'pending_at'      => now(),
            'expires_at'      => now()->addMinutes(60),
        ]);

        // Generate QR SVG
        $qrSvg = $this->generateQrSvg($config->wallet_address);

        return response()->json([
            'tx_id'          => $tx->id,
            'wallet_address' => $config->wallet_address,
            'amount_crypto'  => $tx->amount_crypto,
            'currency'       => $config->currency,
            'network'        => $config->network,
            'expires_at'     => $tx->expires_at->toIso8601String(),
            'qr_svg'         => $qrSvg,
        ]);
    }

    // ── Crypto: submit TX hash ─────────────────────────────────────────────────
    public function cryptoSubmitHash(Request $request): JsonResponse
    {
        $request->validate([
            'tx_id'   => 'required|integer',
            'tx_hash' => 'required|string|max:128',
        ]);

        $tx = PaymentTransaction::where('id', $request->tx_id)
            ->where('status', 'pending')
            ->first();

        if (! $tx) {
            return response()->json(['error' => 'Transacción no encontrada o ya procesada'], 404);
        }

        if ($tx->expires_at && $tx->expires_at->isPast()) {
            $tx->update(['status' => 'expired']);
            return response()->json(['error' => 'La transacción ha expirado'], 422);
        }

        // Check for duplicate hash
        $duplicate = PaymentTransaction::where('tx_hash', $request->tx_hash)
            ->where('id', '!=', $tx->id)
            ->exists();

        if ($duplicate) {
            return response()->json(['error' => 'Este TX hash ya fue registrado'], 422);
        }

        $tx->update(['tx_hash' => $request->tx_hash]);

        return response()->json(['ok' => true, 'message' => 'Hash registrado. El super-administrador verificará el pago en la blockchain.']);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    public function activateSubscription(PaymentTransaction $tx): void
    {
        if ($tx->status === 'confirmed') return;

        $tx->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Expire previous active subscription
        Subscription::where('organization_id', $tx->organization_id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $plan = $tx->plan;

        $sub = Subscription::create([
            'organization_id' => $tx->organization_id,
            'plan_id'         => $tx->plan_id,
            'status'          => 'active',
            'amount_usd'      => $tx->amount_usd,
            'starts_at'       => now(),
            'ends_at'         => now()->addMonth(),
            'notes'           => "Pago automático vía {$tx->method}",
        ]);

        $tx->update(['subscription_id' => $sub->id]);

        Organization::where('id', $tx->organization_id)->update([
            'plan'                     => $plan->slug,
            'is_active'                => true,
            'max_bot_sessions_per_day' => $plan->max_sessions_per_day,
            'max_messages_per_session' => $plan->max_messages_per_session,
        ]);

        Log::info("[Payment] Subscription activated for org {$tx->organization_id} — plan {$plan->slug}");
    }

    private function generateQrSvg(string $content): string
    {
        try {
            $renderer = new \BaconQrCode\Renderer\Image\SvgImageBackEnd();
            $style    = new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200);
            $image    = new \BaconQrCode\Renderer\ImageRenderer($style, $renderer);
            $writer   = new \BaconQrCode\Writer($image);
            return base64_encode($writer->writeString($content));
        } catch (\Throwable $e) {
            Log::error('[QR] Generation failed: ' . $e->getMessage());
            return '';
        }
    }
}
