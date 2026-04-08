<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Models\ChatWidget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeds the initial partner organization, owner user, and chat widget.
 * Run once after: php artisan migrate --seed
 * Or manually: php artisan db:seed --class=PartnerSetupSeeder
 */
class PartnerSetupSeeder extends Seeder
{
    public function run(): void
    {
        // ── Organization ──────────────────────────────────────────────────────
        $org = Organization::firstOrCreate(
            ['slug' => 'nicagsm'],
            [
                'name'                     => 'NicaGSM',
                'website'                  => 'https://nicagsm.com',
                'support_email'            => 'info@webxdev.pro',
                'support_name'             => 'NicaGSM Soporte',
                'plan'                     => 'partner',
                'is_partner'               => true,
                'is_active'                => true,
                'max_bot_sessions_per_day' => 9999,
                'max_messages_per_session' => 9999,
                'ai_use_own_keys'          => false, // uses global Groq key until owner sets own
            ]
        );

        // ── Owner user ────────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'info@webxdev.pro'],
            [
                'name'            => 'NicaGSM Admin',
                'password'        => Hash::make('00000000'),
                'role'            => 'owner',
                'organization_id' => $org->id,
                'email_verified_at' => now(),
            ]
        );

        // ── Default chat widget ───────────────────────────────────────────────
        if (! ChatWidget::where('organization_id', $org->id)->exists()) {
            ChatWidget::create([
                'organization_id' => $org->id,
                'name'            => 'Widget NicaGSM',
                'token'           => Str::random(32),
                'is_active'       => true,
                'welcome_message' => '¡Hola! ¿En qué podemos ayudarte hoy?',
                'bot_name'        => 'NicaGSM Bot',
                'accent_color'    => '#22c55e',
                'widget_position' => 'right',
            ]);
        }

        $this->command->info('✓ NicaGSM org + owner user + widget created.');
        $this->command->info('  Email: info@webxdev.pro | Password: 00000000');
        $this->command->info('  Login: https://nicagsm.nexovadesk.com/login');
    }
}
