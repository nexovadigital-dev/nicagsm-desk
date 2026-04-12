<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            if (! Schema::hasColumn('chat_widgets', 'ai_enabled')) {
                $table->boolean('ai_enabled')->default(true)->after('bot_enabled');
            }
            if (! Schema::hasColumn('chat_widgets', 'faq_direct')) {
                $table->boolean('faq_direct')->default(true)->after('ai_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->dropColumnIfExists('ai_enabled');
            $table->dropColumnIfExists('faq_direct');
        });
    }
};
