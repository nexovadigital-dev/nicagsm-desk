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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('website')->nullable()->after('slug');
            $table->string('support_email')->nullable()->after('website');
            $table->string('support_name')->nullable()->after('support_email');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['website', 'support_email', 'support_name']);
        });
    }
};
