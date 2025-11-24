<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidentes_transporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->constrained('asignacionmultiple')->cascadeOnDelete();
            $table->foreignId('id_tipo_incidente')->constrained('tipos_incidente_transporte')->cascadeOnDelete();
            $table->text('descripcion_incidente')->nullable();
            $table->timestampTz('fecha')->notNullable()->default(now());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidentes_transporte');
    }
};
