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
        Schema::create('direccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('envio_id')->constrained('envio')->onDelete('cascade');
            $table->string('nombre_ruta', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->integer('orden')->default(1);
            $table->text('ruta_geojson')->nullable();
            $table->timestampTz('fecha_creacion')->useCurrent();
        });
        
        // Crear índices
        Schema::table('direccion', function (Blueprint $table) {
            $table->index('envio_id', 'idx_direccion_envio');
            $table->index('orden', 'idx_direccion_orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direccion');
    }
};
