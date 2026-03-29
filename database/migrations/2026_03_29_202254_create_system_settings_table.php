<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('allow_registrations')->default(true);
            $table->string('registration_closed_message', 500)->default('No estamos admitiendo registros nuevos en este momento por labores de mantenimiento. Por favor, inténtalo más tarde.');
            $table->timestamps();
        });

        DB::table('system_settings')->insert([
            'allow_registrations'         => true,
            'registration_closed_message' => 'No estamos admitiendo registros nuevos en este momento por labores de mantenimiento. Por favor, inténtalo más tarde.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
