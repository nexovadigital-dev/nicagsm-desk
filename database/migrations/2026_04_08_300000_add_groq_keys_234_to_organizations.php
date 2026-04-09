<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->text('ai_groq_key_2')->nullable()->after('ai_groq_key');
            $table->text('ai_groq_key_3')->nullable()->after('ai_groq_key_2');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['ai_groq_key_2', 'ai_groq_key_3']);
        });
    }
};
