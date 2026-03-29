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
        Schema::table('organizations', function (Blueprint $table) {
            // Per-org AI API keys (optional — override platform defaults)
            $table->text('ai_groq_key')->nullable()->after('support_name');
            $table->text('ai_gemini_key')->nullable()->after('ai_groq_key');
            $table->boolean('ai_use_own_keys')->default(false)->after('ai_gemini_key');

            // Token & conversation limits
            $table->unsignedSmallInteger('max_messages_per_session')->default(30)->after('ai_use_own_keys');
            $table->unsignedSmallInteger('max_bot_sessions_per_day')->default(100)->after('max_messages_per_session');

            // Usage tracking (resets daily via scheduler)
            $table->unsignedInteger('bot_sessions_today')->default(0)->after('max_bot_sessions_per_day');
            $table->date('usage_date')->nullable()->after('bot_sessions_today');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'ai_groq_key', 'ai_gemini_key', 'ai_use_own_keys',
                'max_messages_per_session', 'max_bot_sessions_per_day',
                'bot_sessions_today', 'usage_date',
            ]);
        });
    }
};
