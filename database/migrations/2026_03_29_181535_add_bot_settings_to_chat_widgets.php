<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->boolean('bot_enabled')->default(true)->after('bot_name');
            $table->string('bot_avatar')->nullable()->after('bot_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->dropColumn(['bot_enabled', 'bot_avatar']);
        });
    }
};
