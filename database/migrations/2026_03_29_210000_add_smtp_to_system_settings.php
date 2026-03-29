<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('smtp_host', 255)->nullable()->after('registration_closed_message');
            $table->string('smtp_port', 10)->default('587')->after('smtp_host');
            $table->string('smtp_username', 255)->nullable()->after('smtp_port');
            $table->string('smtp_password', 500)->nullable()->after('smtp_username');
            $table->string('smtp_encryption', 10)->default('tls')->after('smtp_password');
            $table->string('smtp_from_address', 255)->nullable()->after('smtp_encryption');
            $table->string('smtp_from_name', 100)->default('Nexova Desk')->after('smtp_from_address');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['smtp_host','smtp_port','smtp_username','smtp_password','smtp_encryption','smtp_from_address','smtp_from_name']);
        });
    }
};
