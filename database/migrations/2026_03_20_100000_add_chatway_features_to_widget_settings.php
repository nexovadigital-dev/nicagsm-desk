<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            // Apariencia
            if (!Schema::hasColumn('widget_settings', 'widget_position'))
                $table->string('widget_position')->default('right')->after('sound_enabled');
            if (!Schema::hasColumn('widget_settings', 'widget_size'))
                $table->string('widget_size')->default('md')->after('widget_position');
            // Preview bubble
            if (!Schema::hasColumn('widget_settings', 'preview_message_enabled'))
                $table->boolean('preview_message_enabled')->default(false)->after('widget_size');
            if (!Schema::hasColumn('widget_settings', 'preview_message'))
                $table->string('preview_message')->nullable()->after('preview_message_enabled');
            // Efecto de atención en el FAB
            if (!Schema::hasColumn('widget_settings', 'attention_effect'))
                $table->string('attention_effect')->default('none')->after('preview_message');
            // Dispositivos
            if (!Schema::hasColumn('widget_settings', 'show_on'))
                $table->string('show_on')->default('both')->after('attention_effect');
            // Pantalla por defecto al abrir
            if (!Schema::hasColumn('widget_settings', 'default_screen'))
                $table->string('default_screen')->default('home')->after('show_on');
            // FAQ
            if (!Schema::hasColumn('widget_settings', 'faq_enabled'))
                $table->boolean('faq_enabled')->default(false)->after('default_screen');
            if (!Schema::hasColumn('widget_settings', 'faq_items'))
                $table->json('faq_items')->nullable()->after('faq_enabled');
            // Canales sociales en widget
            if (!Schema::hasColumn('widget_settings', 'social_channels'))
                $table->json('social_channels')->nullable()->after('faq_items');
            // Horario de atención
            if (!Schema::hasColumn('widget_settings', 'working_hours_enabled'))
                $table->boolean('working_hours_enabled')->default(false)->after('social_channels');
            if (!Schema::hasColumn('widget_settings', 'working_hours'))
                $table->json('working_hours')->nullable()->after('working_hours_enabled');
            if (!Schema::hasColumn('widget_settings', 'offline_message'))
                $table->string('offline_message')->nullable()->after('working_hours');
        });
    }

    public function down(): void
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            $cols = ['widget_position','widget_size','preview_message_enabled','preview_message',
                     'attention_effect','show_on','default_screen','faq_enabled','faq_items',
                     'social_channels','working_hours_enabled','working_hours','offline_message'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('widget_settings', $col))
                    $table->dropColumn($col);
            }
        });
    }
};
