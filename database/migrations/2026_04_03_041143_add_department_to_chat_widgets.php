<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->foreignId('department_id')
                  ->nullable()
                  ->after('organization_id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Department::class);
            $table->dropColumn('department_id');
        });
    }
};
