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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();

            // Identity
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar_url')->nullable();

            // Source
            $table->enum('source', ['woocommerce', 'pre_chat', 'widget', 'manual'])->default('widget');

            // WooCommerce external link
            $table->unsignedBigInteger('woo_customer_id')->nullable();

            // Activity
            $table->timestamp('last_seen_at')->nullable();
            $table->unsignedInteger('total_conversations')->default(0);

            // Extra metadata (JSON) and agent notes
            $table->json('meta')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // One email per org, one woo_id per org
            $table->unique(['organization_id', 'email']);
            $table->unique(['organization_id', 'woo_customer_id']);
            $table->index(['organization_id', 'last_seen_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
