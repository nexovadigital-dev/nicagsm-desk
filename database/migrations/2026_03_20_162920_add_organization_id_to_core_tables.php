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
        $tables = ['tickets', 'chat_widgets', 'knowledge_bases', 'canned_responses'];

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && ! Schema::hasColumn($tbl, 'organization_id')) {
                Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                    $table->foreignId('organization_id')
                          ->nullable()
                          ->after('id')
                          ->constrained()
                          ->nullOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['tickets', 'chat_widgets', 'knowledge_bases', 'canned_responses'];

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'organization_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropForeign(['organization_id']);
                    $table->dropColumn('organization_id');
                });
            }
        }
    }
};
