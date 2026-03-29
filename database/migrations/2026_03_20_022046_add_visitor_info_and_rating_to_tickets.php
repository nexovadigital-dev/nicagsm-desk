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
        Schema::table('tickets', function (Blueprint $table) {
            // Datos del visitante
            $table->string('client_email')->nullable()->after('client_name');
            $table->string('client_phone')->nullable()->after('client_email');
            $table->string('visitor_ip')->nullable()->after('client_phone');
            $table->string('visitor_country')->nullable()->after('visitor_ip');
            $table->string('visitor_city')->nullable()->after('visitor_country');
            $table->string('visitor_device')->nullable()->after('visitor_city');
            $table->string('visitor_os')->nullable()->after('visitor_device');
            $table->string('visitor_browser')->nullable()->after('visitor_os');
            $table->text('visitor_referrer')->nullable()->after('visitor_browser');
            $table->string('visitor_page')->nullable()->after('visitor_referrer');
            // Calificación del chat
            $table->tinyInteger('rating')->nullable()->after('internal_notes');
            $table->text('rating_comment')->nullable()->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'client_email','client_phone',
                'visitor_ip','visitor_country','visitor_city',
                'visitor_device','visitor_os','visitor_browser',
                'visitor_referrer','visitor_page',
                'rating','rating_comment',
            ]);
        });
    }
};
