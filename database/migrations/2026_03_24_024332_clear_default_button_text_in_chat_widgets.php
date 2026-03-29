<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Clear the 'Chat' default button_text set by previous migration.
     * The JSX renders a pill button whenever button_text is non-empty,
     * so the old default caused all widgets to appear as pill buttons.
     */
    public function up(): void
    {
        // Only clear if it still has the auto-default 'Chat' value
        \DB::table('chat_widgets')
            ->where('button_text', 'Chat')
            ->update(['button_text' => '']);

        // Also change the column default to empty string
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->string('button_text')->default('')->change();
        });
    }

    public function down(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->string('button_text')->default('Chat')->change();
        });
    }
};
