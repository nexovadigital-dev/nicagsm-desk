<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smtp_settings', function (Blueprint $table) {
            // ── IMAP — recepción de respuestas por email ──────────────────────
            $table->boolean('imap_enabled')->default(false)->after('notifications_enabled');
            $table->string('imap_host')->nullable()->after('imap_enabled');
            $table->integer('imap_port')->default(993)->after('imap_host');
            $table->string('imap_encryption')->default('ssl')->after('imap_port'); // ssl | tls | none
            $table->string('imap_username')->nullable()->after('imap_encryption');
            $table->string('imap_password')->nullable()->after('imap_username');
            $table->string('imap_folder')->default('INBOX')->after('imap_password');
        });
    }

    public function down(): void
    {
        Schema::table('smtp_settings', function (Blueprint $table) {
            $table->dropColumn([
                'imap_enabled', 'imap_host', 'imap_port',
                'imap_encryption', 'imap_username', 'imap_password', 'imap_folder',
            ]);
        });
    }
};
