<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tipo_vehiculo')->constrained('tipos_vehiculo')->cascadeOnDelete();
            $table->string('placa', 20)->unique()->notNullable();
            $table->decimal('capacidad', 10, 2)->notNullable();
            $table->foreignId('id_estado_vehiculo')->constrained('estados_vehiculo')->cascadeOnDelete();
            $table->timestampTz('fecha_registro')->notNullable()->default(now());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
