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
        Schema::create('producto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->enum('categoria', ['Frutas', 'Verduras'])->comment('Categorías hardcodeadas');
            $table->decimal('peso_por_unidad', 10, 3)->comment('Peso en kg por unidad');
            $table->text('descripcion')->nullable();
            $table->timestamps();
            
            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
