<?php

namespace App\Console\Commands;

use App\Http\Controllers\PaymentController;
use App\Models\PaymentTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verifica automáticamente TX hashes en las blockchains y activa suscripciones.
 *
 * APIs usadas (gratuitas, sin key):
 *  - TRC20  → TronScan  https://apilist.tronscanapi.com
 *  - BEP20  → BSCScan   https://api.bscscan.com  (con BSCSCAN_API_KEY en .env, opcional)
 *  - Polygon → PolygonScan https://api.polygonscan.com (con POLYGONSCAN_API_KEY en .env, opcional)
 *
 * Ejecutar: php artisan nexova:verify-crypto
 * Scheduled: cada 5 minutos (ver routes/console.php)
 */
class VerifyCryptoPayments extends Command
{
    protected $signature   = 'nexova:verify-crypto';
    protected $description = 'Verifica TX hashes de pagos crypto en la blockchain y activa suscripciones';

    private PaymentController $payments;

    public function __construct(PaymentController $payments)
    {
        parent::__construct();
        $this->payments = $payments;
    }

    public function handle(): void
    {
        // Pending TXs with hash submitted but not yet confirmed
        $pending = PaymentTransaction::where('status', 'pending')
            ->whereNotNull('tx_hash')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->get();

        if ($pending->isEmpty()) {
            $this->line('No pending transactions to verify.');
            return;
        }

        $this->line("Checking {$pending->count()} transaction(s)...");

        foreach ($pending as $tx) {
            try {
                $result = $this->verifyOnChain($tx);

                if ($result === 'confirmed') {
                    $this->info("✓ TX #{$tx->id} ({$tx->tx_hash}) CONFIRMED — activating subscription");
                    $this->payments->activateSubscription($tx);
                } elseif ($result === 'failed') {
                    $this->warn("✗ TX #{$tx->id} FAILED on blockchain — marking as failed");
                    $tx->update(['status' => 'failed']);
                } else {
                    $this->line("  TX #{$tx->id} pending on blockchain...");
                }
            } catch (\Throwable $e) {
                Log::error("[VerifyCrypto] Error checking TX #{$tx->id}: " . $e->getMessage());
                $this->error("Error on TX #{$tx->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Returns: 'confirmed' | 'failed' | 'pending'
     */
    private function verifyOnChain(PaymentTransaction $tx): string
    {
        $network = $tx->network ?? $this->networkFromMethod($tx->method);

        return match ($network) {
            'trc20'   => $this->verifyTron($tx),
            'bep20'   => $this->verifyEvm($tx, 'bsc'),
            'polygon' => $this->verifyEvm($tx, 'polygon'),
            default   => 'pending',
        };
    }

    // ── TronScan (no API key needed) ────────────────────────────────────────
    private function verifyTron(PaymentTransaction $tx): string
    {
        $url  = 'https://apilist.tronscanapi.com/api/transaction-info?hash=' . $tx->tx_hash;
        $resp = Http::timeout(15)->get($url);

        if (! $resp->successful()) return 'pending';

        $data = $resp->json();

        // TX not found yet
        if (empty($data['hash'])) return 'pending';

        // Check if confirmed
        $confirmed = ($data['confirmed'] ?? false) || ($data['confirmations'] ?? 0) >= 1;
        if (! $confirmed) return 'pending';

        // Check if TX failed on chain
        if (($data['contractRet'] ?? '') === 'REVERT') return 'failed';

        // Verify destination wallet matches
        $toAddress   = $data['toAddress']   ?? $data['contractData']['to_address'] ?? null;
        $fromAddress = $data['ownerAddress'] ?? $data['contractData']['owner_address'] ?? null;

        if ($tx->wallet_to && $toAddress && strtolower($toAddress) !== strtolower($tx->wallet_to)) {
            Log::warning("[VerifyCrypto] TRC20 TX #{$tx->id}: wrong destination {$toAddress} expected {$tx->wallet_to}");
            return 'failed';
        }

        // TRC20 token transfers are in trc20TransferInfo
        $transfers = $data['trc20TransferInfo'] ?? [];
        if (! empty($transfers)) {
            $transfer = $transfers[0];
            $decimals = (int) ($transfer['decimals'] ?? 6);
            $amount   = ($transfer['amount_str'] ?? $transfer['amount'] ?? 0) / pow(10, $decimals);

            if ($amount < ((float)$tx->amount_crypto * 0.99)) { // 1% tolerance for fees
                Log::warning("[VerifyCrypto] TRC20 TX #{$tx->id}: amount {$amount} < expected {$tx->amount_crypto}");
                return 'failed';
            }

            // Store sender wallet for records
            if ($fromAddress && ! $tx->wallet_from) {
                $tx->update(['wallet_from' => $fromAddress]);
            }

            return 'confirmed';
        }

        // Native TRX transfer (shouldn't be used for USDT but fallback)
        return $confirmed ? 'confirmed' : 'pending';
    }

    // ── BSCScan / PolygonScan ───────────────────────────────────────────────
    private function verifyEvm(PaymentTransaction $tx, string $chain): string
    {
        [$baseUrl, $envKey] = match ($chain) {
            'bsc'     => ['https://api.bscscan.com/api',     'BSCSCAN_API_KEY'],
            'polygon' => ['https://api.polygonscan.com/api', 'POLYGONSCAN_API_KEY'],
            default   => [null, null],
        };

        if (! $baseUrl) return 'pending';

        $apiKey = env($envKey, 'YourApiKeyToken'); // free tier works without key but is rate-limited

        // Step 1: check receipt status (success/fail)
        $receipt = Http::timeout(15)->get($baseUrl, [
            'module'  => 'transaction',
            'action'  => 'gettxreceiptstatus',
            'txhash'  => $tx->tx_hash,
            'apikey'  => $apiKey,
        ])->json();

        $status = $receipt['result']['status'] ?? null;

        if ($status === null || $receipt['status'] === '0') {
            // Not found yet or API error
            return 'pending';
        }

        if ($status === '0') return 'failed'; // reverted

        // Step 2: get full TX details to verify amount and destination
        $txData = Http::timeout(15)->get($baseUrl, [
            'module'  => 'proxy',
            'action'  => 'eth_getTransactionByHash',
            'txhash'  => $tx->tx_hash,
            'apikey'  => $apiKey,
        ])->json();

        $txResult = $txData['result'] ?? null;
        if (! $txResult) return 'pending';

        // For ERC-20 transfers, 'to' is the token contract, not the wallet
        // We verify via token transfer events (Transfer topic)
        $logs = Http::timeout(15)->get($baseUrl, [
            'module'  => 'logs',
            'action'  => 'getLogs',
            'txhash'  => $tx->tx_hash,
            'apikey'  => $apiKey,
        ])->json();

        $transfers = $logs['result'] ?? [];

        // ERC-20 Transfer event topic
        $transferTopic = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';

        foreach ($transfers as $log) {
            $topics = $log['topics'] ?? [];
            if (($topics[0] ?? '') !== $transferTopic) continue;

            // topics[2] = to address (padded)
            $toAddr = '0x' . substr($topics[2] ?? '', 26);

            if ($tx->wallet_to && strtolower($toAddr) !== strtolower($tx->wallet_to)) continue;

            // data = amount in hex
            $amountHex = ltrim($log['data'] ?? '0x0', '0x') ?: '0';
            $amountRaw = hexdec($amountHex);
            $amount    = $amountRaw / 1e6; // USDT/USDC use 6 decimals

            if ($amount >= ((float)$tx->amount_crypto * 0.99)) {
                // Store sender
                $fromAddr = '0x' . substr($topics[1] ?? '', 26);
                if ($fromAddr && ! $tx->wallet_from) {
                    $tx->update(['wallet_from' => $fromAddr]);
                }
                return 'confirmed';
            }

            Log::warning("[VerifyCrypto] EVM TX #{$tx->id}: amount {$amount} < expected {$tx->amount_crypto}");
            return 'failed';
        }

        // No matching transfer found
        return 'pending';
    }

    private function networkFromMethod(string $method): string
    {
        return match (true) {
            str_contains($method, 'trc20')   => 'trc20',
            str_contains($method, 'bep20')   => 'bep20',
            str_contains($method, 'polygon') => 'polygon',
            default                           => 'unknown',
        };
    }
}
