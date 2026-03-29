<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->unsignedTinyInteger('agent_call_timeout')->default(10)->after('require_rating');
            // 5 = 5 min, 10 = 10 min, 15 = 15 min
            $table->string('agent_no_response', 20)->default('bot')->after('agent_call_timeout');
            // 'bot' = volver al bot, 'ticket' = dejar datos para ticket
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('agent_called_at')->nullable()->after('store_context');
        });
    }
    public function down(): void {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->dropColumn(['agent_call_timeout', 'agent_no_response']);
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('agent_called_at');
        });
    }
};