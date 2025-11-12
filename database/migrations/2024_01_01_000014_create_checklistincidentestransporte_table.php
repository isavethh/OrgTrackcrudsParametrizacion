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
        Schema::create('checklistincidentestransporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->unique()->constrained('asignacionmultiple');
            $table->boolean('retraso');
            $table->boolean('problema_mecanico');
            $table->boolean('accidente');
            $table->boolean('perdida_carga');
            $table->boolean('condiciones_climaticas_adversas');
            $table->boolean('ruta_alternativa_usada');
            $table->boolean('contacto_cliente_dificultoso');
            $table->boolean('parada_imprevista');
            $table->boolean('problemas_documentacion');
            $table->boolean('otros_incidentes');
            $table->string('descripcion_incidente', 255)->nullable();
            $table->timestampTz('fecha')->default(DB::raw('now()'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklistincidentestransporte');
    }
};

