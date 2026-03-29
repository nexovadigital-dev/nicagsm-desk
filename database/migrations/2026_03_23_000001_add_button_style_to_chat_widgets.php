<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->string('button_style')->default('icon')->after('widget_size'); // icon | text | image
            $table->string('button_text')->default('Chat')->after('button_style');
            $table->string('button_image')->nullable()->after('button_text');
        });
    }

    public function down(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->dropColumn(['button_style', 'button_text', 'button_image']);
        });
    }
};
