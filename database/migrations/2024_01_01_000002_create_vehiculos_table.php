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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tipo_vehiculo')->constrained('tipos_vehiculo');
            $table->string('placa', 20)->unique();
            $table->decimal('capacidad', 10, 2);
            $table->foreignId('id_estado_vehiculo')->constrained('estados_vehiculo');
            $table->timestampTz('fecha_registro')->default(DB::raw('now()'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};

