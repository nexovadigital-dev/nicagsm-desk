<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->string('button_icon')->default('chat')->after('button_style');
            $table->string('button_text_color')->default('#ffffff')->after('button_text');
        });

        // Remove 'text' style — now icon handles optional text
        \DB::table('chat_widgets')->where('button_style', 'text')->update(['button_style' => 'icon']);
    }

    public function down(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->dropColumn(['button_icon', 'button_text_color']);
        });
    }
};
