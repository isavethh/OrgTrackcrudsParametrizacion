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
        Schema::create('asignacionmultiple', function (Blueprint $table) {
            $table->id();
            $table->foreignId('envio_id')->constrained('envio')->onDelete('cascade');
            $table->foreignId('qrtoken_id')->nullable()->constrained('qrtoken');
            $table->foreignId('transportista_id')->nullable()->constrained('transportista');
            $table->foreignId('tipo_transporte_id')->nullable()->constrained('tipo_transporte');
            $table->integer('recogida_entrega_id')->nullable();
            $table->string('estado', 50)->nullable();
            $table->timestampTz('fecha_asignacion')->useCurrent();
            $table->timestampTz('fecha_inicio')->nullable();
            $table->timestampTz('fecha_fin')->nullable();
        });
        
        // Crear índices
        Schema::table('asignacionmultiple', function (Blueprint $table) {
            $table->index('envio_id', 'idx_asignacion_envio');
            $table->index('transportista_id', 'idx_asignacion_transportista');
            $table->index('qrtoken_id', 'idx_asignacion_qrtoken');
            $table->index('estado', 'idx_asignacion_estado');
            $table->index('fecha_asignacion', 'idx_asignacion_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacionmultiple');
    }
};
