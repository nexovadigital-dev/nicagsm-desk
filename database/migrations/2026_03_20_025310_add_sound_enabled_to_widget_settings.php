<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('widget_settings', 'sound_enabled')) {
                $table->boolean('sound_enabled')->default(true)->after('rating_message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            if (Schema::hasColumn('widget_settings', 'sound_enabled')) {
                $table->dropColumn('sound_enabled');
            }
        });
    }
};
