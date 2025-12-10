<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalogo_productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('peso_promedio', 8, 3)->nullable()->comment('Peso promedio por unidad en kg');
            $table->foreignId('id_categoria')->constrained('catalogo_categorias')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_productos');
    }
};
