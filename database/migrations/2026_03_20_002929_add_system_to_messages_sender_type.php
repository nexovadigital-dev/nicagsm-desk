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
        // MySQL no permite ALTER ENUM via Blueprint directamente; usamos raw SQL
        \DB::statement("ALTER TABLE messages MODIFY COLUMN sender_type ENUM('user','bot','agent','system') NOT NULL");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE messages MODIFY COLUMN sender_type ENUM('user','bot','agent') NOT NULL");
    }
};
