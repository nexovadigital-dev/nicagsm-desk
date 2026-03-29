<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('is_support_ticket')->default(false)->after('internal_notes');
            $table->string('ticket_number', 20)->nullable()->unique()->after('is_support_ticket');
            $table->string('ticket_subject', 255)->nullable()->after('ticket_number');
            $table->string('ticket_reply_token', 64)->nullable()->unique()->after('ticket_subject');
            $table->timestamp('ticket_opened_at')->nullable()->after('ticket_reply_token');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'is_support_ticket', 'ticket_number', 'ticket_subject',
                'ticket_reply_token', 'ticket_opened_at',
            ]);
        });
    }
};
