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
            $table->foreignId('id_tipo_vehiculo')->nullable()->after('direccion_id')->constrained('tipos_vehiculo')->nullOnDelete();
            $table->foreignId('id_transportista_asignado')->nullable()->after('id_tipo_vehiculo')->constrained('usuarios')->nullOnDelete();
            $table->string('estado_aprobacion')->default('pendiente')->after('estado'); // pendiente, aprobado, rechazado
            $table->text('motivo_rechazo')->nullable()->after('estado_aprobacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->dropForeign(['id_tipo_vehiculo']);
            $table->dropForeign(['id_transportista_asignado']);
            $table->dropColumn(['id_tipo_vehiculo', 'id_transportista_asignado', 'estado_aprobacion', 'motivo_rechazo']);
        });
    }
};

