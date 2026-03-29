<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('max_bot_messages_monthly')->default(0)->after('max_messages_per_session');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->unsignedInteger('bot_messages_this_month')->default(0)->after('bot_sessions_today');
            $table->date('bot_messages_month_reset')->nullable()->after('bot_messages_this_month');
        });

        // Set limits for existing plans (0 = unlimited)
        DB::table('plans')->where('slug', 'free')->update(['max_bot_messages_monthly' => 1000]);
        DB::table('plans')->where('slug', 'trial')->update(['max_bot_messages_monthly' => 3000]);
        DB::table('plans')->where('slug', 'pro')->update(['max_bot_messages_monthly' => 0]);
        DB::table('plans')->where('slug', 'enterprise')->update(['max_bot_messages_monthly' => 0]);
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('max_bot_messages_monthly');
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['bot_messages_this_month', 'bot_messages_month_reset']);
        });
    }
};
