<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->tinyInteger('survey_rating')->unsigned()->nullable()->after('ticket_opened_at');
            $table->text('survey_comment')->nullable()->after('survey_rating');
            $table->timestamp('survey_responded_at')->nullable()->after('survey_comment');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['survey_rating', 'survey_comment', 'survey_responded_at']);
        });
    }
};
