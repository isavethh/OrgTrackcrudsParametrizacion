<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignacionmultiple', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_envio')->constrained('envios')->cascadeOnDelete();
            $table->foreignId('id_transportista')->nullable()->constrained('transportistas')->cascadeOnDelete();
            $table->foreignId('id_vehiculo')->nullable()->constrained('vehiculos')->cascadeOnDelete();
            $table->foreignId('id_recogida_entrega')->constrained('recogidaentrega')->cascadeOnDelete();
            $table->foreignId('id_tipo_transporte')->constrained('tipotransporte')->cascadeOnDelete();
            $table->foreignId('id_estado_asignacion')->constrained('estados_asignacion_multiple')->cascadeOnDelete();
            $table->timestampTz('fecha_asignacion')->notNullable()->default(now());
            $table->timestampTz('fecha_inicio')->nullable();
            $table->timestampTz('fecha_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacionmultiple');
    }
};
