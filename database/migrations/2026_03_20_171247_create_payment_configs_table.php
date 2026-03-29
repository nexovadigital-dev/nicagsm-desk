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
        Schema::create('payment_configs', function (Blueprint $table) {
            $table->id();
            // method: usdt_trc20, usdt_bep20, usdt_polygon,
            //         usdc_trc20, usdc_bep20, usdc_polygon, mercadopago
            $table->string('method')->unique();
            $table->string('label');                   // "USDT · Red TRC20 (Tron)"
            $table->string('network')->nullable();     // trc20, bep20, polygon
            $table->string('currency')->nullable();    // USDT, USDC
            // Crypto config
            $table->string('wallet_address')->nullable();
            // MercadoPago config (encrypted)
            $table->text('mp_access_token')->nullable();
            $table->text('mp_public_key')->nullable();
            $table->string('mp_country')->nullable();  // CO, AR, MX...
            // Generic metadata (extra params)
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_configs');
    }
};
