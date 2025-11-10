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
        Schema::create('asignacionmultiple', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_envio')->constrained('envios');
            $table->foreignId('id_transportista')->nullable()->constrained('transportistas');
            $table->foreignId('id_vehiculo')->nullable()->constrained('vehiculos');
            $table->foreignId('id_recogida_entrega')->constrained('recogidaentrega');
            $table->foreignId('id_tipo_transporte')->constrained('tipotransporte');
            $table->string('estado', 50)->default('Pendiente');
            $table->timestampTz('fecha_asignacion')->default(DB::raw('now()'));
            $table->timestampTz('fecha_inicio')->nullable();
            $table->timestampTz('fecha_fin')->nullable();
        });
        
        DB::statement("ALTER TABLE asignacionmultiple ADD CONSTRAINT chk_asignacion_multiple_estado CHECK (estado IN ('Entregado','En curso','Pendiente'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacionmultiple');
    }
};

