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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();          // trial, starter, pro, enterprise
            $table->string('name');                    // Nombre visible
            $table->string('description')->nullable();
            $table->decimal('price_usd', 10, 2)->default(0);  // Precio mensual en USD
            $table->unsignedSmallInteger('max_agents')->default(1);
            $table->unsignedSmallInteger('max_widgets')->default(1);
            $table->unsignedInteger('max_sessions_per_day')->default(50);
            $table->unsignedSmallInteger('max_messages_per_session')->default(20);
            $table->json('features')->nullable();      // Lista de features incluidas
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
