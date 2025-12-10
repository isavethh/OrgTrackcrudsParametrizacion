<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('catalogo_tamano_conteo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('conteo_por_empaque');
            $table->decimal('peso_promedio_unidad', 8, 3);
            $table->boolean('activo')->default(true);
            // No timestamps needed for catalog
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_tamano_conteo');
    }
};
