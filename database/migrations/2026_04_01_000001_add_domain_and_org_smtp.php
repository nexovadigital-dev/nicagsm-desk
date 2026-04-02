<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Domain ownership verification per organization
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('domain')->nullable()->after('website');
            $table->boolean('domain_verified')->default(false)->after('domain');
            $table->string('domain_verify_token', 64)->nullable()->after('domain_verified');
        });

        // Per-org SMTP config — add organization_id to existing smtp_settings
        Schema::table('smtp_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('organization_id')->nullable()->after('id');
            $table->boolean('enabled')->default(false)->after('notifications_enabled');
            $table->index('organization_id');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['domain', 'domain_verified', 'domain_verify_token']);
        });

        Schema::table('smtp_settings', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
            $table->dropColumn(['organization_id', 'enabled']);
        });
    }
};
