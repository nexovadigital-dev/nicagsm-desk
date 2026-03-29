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
            $table->boolean('ai_blocked')->default(false)->after('is_active');
        });

        // Restructure to Free + Pro only
        DB::table('plans')->delete();

        DB::table('plans')->insert([
            [
                'slug'                     => 'free',
                'name'                     => 'Free',
                'description'              => 'Plan gratuito. IA deshabilitada — el bot responde solo desde tu base de conocimiento.',
                'price_usd'                => 0,
                'max_agents'               => 3,
                'max_widgets'              => 1,
                'max_sessions_per_day'     => 200,
                'max_messages_per_session' => 10,
                'ai_blocked'               => true,
                'features'                 => json_encode(['kb_manual', 'kb_scrape', 'kb_wordpress']),
                'is_active'                => true,
                'sort'                     => 1,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
            [
                'slug'                     => 'pro',
                'name'                     => 'Pro',
                'description'              => 'Todo incluido. IA habilitada con política de uso justo en las claves de Nexova.',
                'price_usd'                => 49,
                'max_agents'               => 999,
                'max_widgets'              => 999,
                'max_sessions_per_day'     => 999999,
                'max_messages_per_session' => 999,
                'ai_blocked'               => false,
                'features'                 => json_encode(['kb_manual', 'kb_scrape', 'kb_wordpress', 'ai_enabled', 'own_api_keys', 'unlimited_agents']),
                'is_active'                => true,
                'sort'                     => 2,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
        ]);

        // Migrate existing orgs to free (trial/starter/enterprise → free)
        DB::table('organizations')->whereNotIn('plan', ['free', 'pro'])
            ->update(['plan' => 'free']);
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('ai_blocked');
        });
    }
};
