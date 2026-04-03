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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 7)->default('#22c55e');
            $table->timestamps();
        });

        Schema::create('tag_ticket', function (Blueprint $table) {
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->primary(['tag_id', 'ticket_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_ticket');
        Schema::dropIfExists('tags');
    }
};
