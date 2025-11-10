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
            $table->foreignId('id_usuario')->constrained('usuarios');
            $table->string('nombreorigen', 200)->nullable();
            $table->double('origen_lng')->nullable();
            $table->double('origen_lat')->nullable();
            $table->string('nombredestino', 200)->nullable();
            $table->double('destino_lng')->nullable();
            $table->double('destino_lat')->nullable();
            $table->text('rutageojson')->nullable();
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

