<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only insert if it doesn't already exist
        if (DB::table('plans')->where('slug', 'partner')->exists()) return;

        DB::table('plans')->insert([
            'slug'                     => 'partner',
            'name'                     => 'Partner',
            'description'              => 'Acceso completo para aliados estratégicos e inversores del proyecto.',
            'price_usd'                => 0.00,
            'max_agents'               => 9999,
            'max_widgets'              => 9999,
            'max_sessions_per_day'     => 9999,
            'max_messages_per_session' => 9999,
            'max_bot_messages_monthly' => 0,     // 0 = ilimitado
            'features'                 => json_encode([
                'kb_manual', 'kb_scrape', 'kb_wordpress',
                'ai_enabled', 'own_api_keys', 'unlimited_agents',
                'telegram', 'woocommerce', 'priority_support',
            ]),
            'is_active'                => true,
            'is_public'                => false,  // no aparece en landing ni pricing
            'ai_blocked'               => false,
            'sort'                     => 99,
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('plans')->where('slug', 'partner')->delete();
    }
};
