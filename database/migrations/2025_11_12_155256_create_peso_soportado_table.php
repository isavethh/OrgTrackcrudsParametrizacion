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
        Schema::create('peso_soportado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculo')->onDelete('cascade');
            $table->decimal('valor', 10, 2);
            $table->string('unidad', 20);
            $table->string('descripcion', 100)->nullable();
        });
        
        // Crear índice
        Schema::table('peso_soportado', function (Blueprint $table) {
            $table->index('vehiculo_id', 'idx_peso_vehiculo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peso_soportado');
    }
};
