<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            // 'widget' (default, usa widget_id) | 'telegram' (bot Telegram, widget_id=null)
            $table->string('channel', 20)->nullable()->default(null)->after('widget_id');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->dropColumn('channel');
        });
    }
};
