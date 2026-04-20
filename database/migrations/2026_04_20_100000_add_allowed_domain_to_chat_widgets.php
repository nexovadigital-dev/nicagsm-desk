<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            // Domain restriction — only requests from this domain are accepted.
            // Null = no restriction (any origin allowed).
            $table->string('allowed_domain')->nullable()->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('chat_widgets', function (Blueprint $table) {
            $table->dropColumn('allowed_domain');
        });
    }
};
