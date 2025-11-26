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
        Schema::create('incidentes_transporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->constrained('asignacionmultiple');
            $table->foreignId('id_tipo_incidente')->constrained('tipos_incidente_transporte');
            $table->string('descripcion_incidente', 255)->nullable();
            $table->timestampTz('fecha')->default(DB::raw('now()'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidentes_transporte');
    }
};

