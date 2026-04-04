<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('active_visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_key', 64)->index();
            $table->string('widget_token', 64)->nullable();

            // Page tracking
            $table->text('current_url')->nullable();
            $table->string('page_title', 255)->nullable();
            $table->text('referrer')->nullable();
            $table->json('pages_visited')->nullable(); // [{url, title, at}, ...]

            // Visitor info
            $table->string('ip', 45)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('device', 50)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('browser', 100)->nullable();

            // Status
            $table->boolean('is_idle')->default(false);
            $table->boolean('tab_visible')->default(true);

            // Chat link
            $table->string('session_id', 100)->nullable();

            // Proactive chat (agent triggers)
            $table->boolean('proactive_open')->default(false);
            $table->text('proactive_message')->nullable();

            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_ping_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'visitor_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('active_visitors');
    }
};
