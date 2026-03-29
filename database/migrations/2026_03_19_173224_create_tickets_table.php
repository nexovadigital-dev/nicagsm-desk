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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index(); // Para el widget web
            $table->string('whatsapp_number')->nullable()->index(); // Para WhatsApp
            $table->string('telegram_id')->nullable()->index(); // Para Telegram
            $table->enum('platform', ['web', 'whatsapp', 'telegram'])->default('web');
            $table->enum('status', ['bot', 'human', 'closed'])->default('bot');
            $table->string('client_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
