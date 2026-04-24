<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            if (! Schema::hasColumn('chat_widgets', 'woo_orders_enabled')) {
                $table->boolean('woo_orders_enabled')->default(false)->after('woo_integration_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->dropColumn('woo_orders_enabled');
        });
    }
};
