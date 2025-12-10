<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('especificacion_medidas_peso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_carga')->unique()->constrained('carga')->onDelete('cascade');
            $table->decimal('largo_cm', 10, 2)->nullable();
            $table->decimal('ancho_cm', 10, 2)->nullable();
            $table->decimal('alto_cm', 10, 2)->nullable();
            $table->decimal('peso_neto_kg', 10, 2)->nullable();
            $table->decimal('tara_kg', 10, 2)->nullable();
            $table->decimal('peso_bruto_kg', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especificacion_medidas_peso');
    }
};
