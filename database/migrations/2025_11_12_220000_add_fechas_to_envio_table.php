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
        Schema::table('envio', function (Blueprint $table) {
            // Verificar si las columnas no existen antes de agregarlas
            if (!Schema::hasColumn('envio', 'fecha_envio')) {
                $table->timestampTz('fecha_envio')->nullable()->after('admin_id');
            }
            if (!Schema::hasColumn('envio', 'fecha_entrega_estimada')) {
                $table->timestampTz('fecha_entrega_estimada')->nullable()->after('fecha_envio');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envio', function (Blueprint $table) {
            if (Schema::hasColumn('envio', 'fecha_envio')) {
                $table->dropColumn('fecha_envio');
            }
            if (Schema::hasColumn('envio', 'fecha_entrega_estimada')) {
                $table->dropColumn('fecha_entrega_estimada');
            }
        });
    }
};
