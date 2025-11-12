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
        Schema::create('envio', function (Blueprint $table) {
            $table->id();
            $table->string('estado', 50);
            $table->foreignId('tipo_empaque_id')->nullable()->constrained('tipo_empaque');
            $table->decimal('peso', 10, 2)->nullable();
            $table->decimal('volumen', 10, 2)->nullable();
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidad_medida');
            $table->foreignId('admin_id')->nullable()->constrained('admin');
            $table->timestampTz('fecha_envio')->useCurrent();
            $table->timestampTz('fecha_entrega_estimada')->nullable();
        });
        
        // Crear índices
        Schema::table('envio', function (Blueprint $table) {
            $table->index('estado', 'idx_envio_estado');
            $table->index('fecha_envio', 'idx_envio_fecha');
            $table->index('admin_id', 'idx_envio_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envio');
    }
};
