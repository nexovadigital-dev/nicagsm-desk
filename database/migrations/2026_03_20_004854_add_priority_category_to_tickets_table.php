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
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('priority', ['low','normal','high','urgent'])->default('normal')->after('assigned_agent');
            $table->enum('category', ['general','sales','support','billing','other'])->default('general')->after('priority');
            $table->text('internal_notes')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['priority', 'category', 'internal_notes']);
        });
    }
};
