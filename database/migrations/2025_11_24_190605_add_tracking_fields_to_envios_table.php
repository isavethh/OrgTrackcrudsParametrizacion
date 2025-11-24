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
        Schema::table('envios', function (Blueprint $table) {
            $table->decimal('ubicacion_actual_lng', 10, 8)->nullable();
            $table->decimal('ubicacion_actual_lat', 10, 8)->nullable();
            $table->string('estado_tracking', 20)->default('pendiente'); // pendiente, en_ruta, completada
            $table->timestamp('fecha_inicio_tracking')->nullable();
            $table->timestamp('fecha_fin_tracking')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->dropColumn(['ubicacion_actual_lng', 'ubicacion_actual_lat', 'estado_tracking', 'fecha_inicio_tracking', 'fecha_fin_tracking']);
        });
    }
};
