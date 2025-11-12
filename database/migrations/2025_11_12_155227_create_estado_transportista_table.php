<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estado_transportista', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
        });

        // Insertar datos iniciales
        DB::table('estado_transportista')->insert([
            ['nombre' => 'Disponible'],
            ['nombre' => 'En ruta'],
            ['nombre' => 'Inactivo'],
            ['nombre' => 'En camino'],
            ['nombre' => 'Ocurrió un accidente'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_transportista');
    }
};
