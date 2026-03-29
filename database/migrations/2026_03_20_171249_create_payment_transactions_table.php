<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('plan_id')->constrained();
            // Payment method
            $table->string('method');                 // usdt_trc20, mercadopago, etc.
            $table->string('network')->nullable();    // trc20, bep20, polygon
            $table->string('currency')->nullable();   // USDT, USDC, COP
            $table->decimal('amount_usd', 10, 2);    // Amount in USD
            $table->decimal('amount_crypto', 18, 8)->nullable(); // Amount in crypto
            $table->decimal('amount_local', 14, 2)->nullable();  // Amount in local currency (COP)
            $table->string('exchange_rate')->nullable(); // Rate at time of transaction
            // Crypto fields
            $table->string('wallet_to')->nullable();  // Our wallet
            $table->string('wallet_from')->nullable(); // Client's wallet (optional)
            $table->string('tx_hash')->nullable()->unique(); // Blockchain tx hash
            // MercadoPago fields
            $table->string('mp_preference_id')->nullable();
            $table->string('mp_payment_id')->nullable();
            $table->string('mp_payment_status')->nullable();
            // Status tracking
            $table->enum('status', ['pending', 'confirming', 'confirmed', 'failed', 'expired', 'refunded'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // 24h for crypto
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index('tx_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
