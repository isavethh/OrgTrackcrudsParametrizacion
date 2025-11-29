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
        Schema::create('envio_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('envio_id')->constrained('envios')->cascadeOnDelete();
            $table->string('nombre_insumo'); // Nombre del insumo/producto
            $table->string('tipo_insumo')->nullable(); // Tipo del insumo
            $table->integer('cantidad');
            $table->decimal('peso_por_unidad', 10, 2);
            $table->decimal('peso_total', 10, 2);
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('costo_total', 10, 2);
            $table->string('tipo_empaque')->nullable();
            $table->string('unidad_medida')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envio_insumos');
    }
};
