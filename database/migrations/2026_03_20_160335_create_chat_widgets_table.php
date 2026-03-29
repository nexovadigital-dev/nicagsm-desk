<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Nombre interno (ej. "Widget Principal")
            $table->string('token', 64)->unique();           // Token público para el embed
            $table->boolean('is_active')->default(true);

            // Identity
            $table->string('bot_name')->default('Nexova IA');
            $table->string('welcome_message')->default('Hola, ¿en qué te puedo ayudar?');
            $table->string('accent_color', 20)->default('#7c3aed');

            // Appearance & Position
            $table->string('widget_position', 10)->default('right');
            $table->string('widget_size', 5)->default('md');
            $table->string('attention_effect', 20)->default('none');
            $table->string('default_screen', 20)->default('home');
            $table->string('show_on', 20)->default('both');

            // Preview bubble
            $table->boolean('preview_message_enabled')->default(false);
            $table->string('preview_message')->default('');

            // FAQ
            $table->boolean('faq_enabled')->default(false);
            $table->json('faq_items')->nullable();

            // Social channels
            $table->json('social_channels')->nullable();

            // Working hours
            $table->boolean('working_hours_enabled')->default(false);
            $table->json('working_hours')->nullable();
            $table->string('offline_message')->default('Estamos fuera de horario. Te responderemos pronto.');

            // Behaviour
            $table->boolean('show_branding')->default(true);
            $table->boolean('sound_enabled')->default(true);
            $table->boolean('require_rating')->default(false);
            $table->string('rating_message')->default('¿Cómo fue tu experiencia?');

            // Pre-chat form
            $table->boolean('pre_chat_enabled')->default(false);
            $table->json('pre_chat_fields')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_widgets');
    }
};
