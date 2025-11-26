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
        Schema::create('envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios');
            $table->timestampTz('fecha_creacion')->default(DB::raw('now()'));
            $table->timestampTz('fecha_inicio')->nullable();
            $table->timestampTz('fecha_entrega')->nullable();
            $table->foreignId('id_direccion')->constrained('direccion');
        });
        
        Schema::table('envios', function (Blueprint $table) {
            $table->index('id_direccion', 'ix_envios_id_direccion');
            $table->index('id_usuario', 'ix_envios_id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};

