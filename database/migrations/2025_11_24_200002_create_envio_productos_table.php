<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envio_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_envio')->constrained('envios')->onDelete('cascade');
            $table->string('categoria', 50); // Verduras, Frutas
            $table->string('producto', 100);
            $table->integer('cantidad');
            $table->decimal('peso_por_unidad', 10, 2);
            $table->decimal('peso_total', 10, 2);
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('costo_total', 10, 2);
            $table->foreignId('id_tipo_empaque')->constrained('tipo_empaque')->onDelete('restrict');
            $table->foreignId('id_unidad_medida')->constrained('unidad_medida')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envio_productos');
    }
};
