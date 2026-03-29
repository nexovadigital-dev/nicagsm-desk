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
        Schema::create('widget_settings', function (Blueprint $table) {
            $table->id();
            // Identidad del bot
            $table->string('bot_name')->default('Nexova IA');
            $table->string('welcome_message')->default('Hola, ¿en qué te puedo ayudar?');
            $table->string('accent_color')->default('#7c3aed');
            // Pre-chat form
            $table->boolean('pre_chat_enabled')->default(false);
            $table->json('pre_chat_fields')->nullable(); // [{key,label,type,required}]
            // Comportamiento
            $table->boolean('show_branding')->default(true);
            $table->boolean('require_rating')->default(false);
            $table->string('rating_message')->default('¿Cómo fue tu experiencia?');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_settings');
    }
};
