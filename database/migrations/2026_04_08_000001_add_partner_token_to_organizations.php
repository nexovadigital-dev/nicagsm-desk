<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('partner_token', 64)->nullable()->unique()->after('is_partner');
        });

        // Generate tokens for existing partner orgs
        \App\Models\Organization::where('is_partner', true)
            ->whereNull('partner_token')
            ->each(fn ($org) => $org->update(['partner_token' => Str::random(48)]));
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('partner_token');
        });
    }
};
