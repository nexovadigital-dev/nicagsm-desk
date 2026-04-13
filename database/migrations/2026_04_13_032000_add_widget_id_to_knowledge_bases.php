<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega soporte de base de conocimiento por canal (widget/bot).
     *
     * widget_id = null  → artículo GLOBAL (disponible para todos los canales)
     * widget_id = X     → artículo exclusivo del widget/bot con ese ID
     *
     * Esto permite configurar una KB con:
     *  - Artículos globales compartidos entre todos
     *  - Artículos específicos para el bot de Telegram (si tiene widget asignado)
     *  - Artículos específicos para el widget de chat web
     */
    public function up(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->unsignedBigInteger('widget_id')->nullable()->after('organization_id');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->dropColumn('widget_id');
        });
    }
};
