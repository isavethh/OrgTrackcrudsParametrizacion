<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('especificacion_tamano_conteo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_carga')->unique()->constrained('carga')->onDelete('cascade');
            $table->integer('conteo_por_empaque')->nullable(); // Calibre
            $table->decimal('peso_promedio_unidad', 10, 3)->nullable(); // kg
            $table->integer('capacidad_por_empaque')->nullable(); // manzanas
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especificacion_tamano_conteo');
    }
};
