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
            $table->string('tipo', 50);
            $table->string('placa', 20)->unique();
            $table->decimal('capacidad', 10, 2);
            $table->string('estado', 20);
            $table->timestampTz('fecha_registro')->default(DB::raw('now()'));
        });
        
        DB::statement("ALTER TABLE vehiculos ADD CONSTRAINT chk_vehiculos_estado CHECK (estado IN ('Mantenimiento','No Disponible','En ruta','Disponible'))");
        DB::statement("ALTER TABLE vehiculos ADD CONSTRAINT ck_vehiculos_tipo_detallado CHECK (tipo IN ('Pesado - Ventilado','Pesado - Aislado','Pesado - Refrigerado','Mediano - Ventilado','Mediano - Aislado','Mediano - Refrigerado','Ligero - Ventilado','Ligero - Aislado','Ligero - Refrigerado'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};

