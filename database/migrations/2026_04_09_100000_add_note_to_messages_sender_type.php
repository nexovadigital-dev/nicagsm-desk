<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        \DB::statement("ALTER TABLE messages MODIFY COLUMN sender_type ENUM('user','bot','agent','system','note') NOT NULL");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE messages MODIFY COLUMN sender_type ENUM('user','bot','agent','system') NOT NULL");
    }
};
