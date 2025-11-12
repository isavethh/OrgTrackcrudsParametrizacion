<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checklistcondicionestransporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->unique()->constrained('asignacionmultiple');
            $table->boolean('temperatura_controlada');
            $table->boolean('embalaje_adecuado');
            $table->boolean('carga_segura');
            $table->boolean('vehiculo_limpio');
            $table->boolean('documentos_presentes');
            $table->boolean('ruta_conocida');
            $table->boolean('combustible_completo');
            $table->boolean('gps_operativo');
            $table->boolean('comunicacion_funcional');
            $table->boolean('estado_general_aceptable');
            $table->string('observaciones', 255)->nullable();
            $table->timestampTz('fecha')->default(DB::raw('now()'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklistcondicionestransporte');
    }
};

