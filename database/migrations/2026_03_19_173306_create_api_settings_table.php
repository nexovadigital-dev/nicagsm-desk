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
        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // Ej: groq, gemini, meta_whatsapp, telegram
            $table->string('api_key');
            $table->string('webhook_verify_token')->nullable(); // Para Meta
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1); // Para rotación en caso de fallo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_settings');
    }
};
