<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            if (! Schema::hasColumn('chat_widgets', 'faq_quick_reply')) {
                $table->boolean('faq_quick_reply')->default(true)->after('faq_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->dropColumnIfExists('faq_quick_reply');
        });
    }
};
