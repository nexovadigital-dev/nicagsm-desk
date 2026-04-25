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
        Schema::table('messages', function (Blueprint $table) {
            // Stores the RFC 2822 Message-ID header of the source email.
            // Used to deduplicate IMAP processing — survives cache:clear.
            $table->string('email_message_id', 512)->nullable()->after('content');
            $table->index(['ticket_id', 'email_message_id'], 'messages_ticket_email_mid_idx');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_ticket_email_mid_idx');
            $table->dropColumn('email_message_id');
        });
    }
};
