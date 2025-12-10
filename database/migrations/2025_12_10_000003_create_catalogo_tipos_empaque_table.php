<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalogo_tipos_empaque', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('largo', 8, 2)->nullable()->comment('Largo en cm');
            $table->decimal('ancho', 8, 2)->nullable()->comment('Ancho en cm');
            $table->decimal('alto', 8, 2)->nullable()->comment('Alto en cm');
            $table->decimal('tara', 8, 2)->nullable()->comment('Peso del empaque vacío en kg');
            $table->integer('capacidad')->nullable()->comment('Capacidad estándar del empaque (unidades que caben)');
            $table->integer('unidades_por_pallet')->nullable()->comment('Cuántas unidades caben en un pallet');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_tipos_empaque');
    }
};
