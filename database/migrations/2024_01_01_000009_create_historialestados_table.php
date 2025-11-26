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
        Schema::create('historialestados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_envio')->constrained('envios');
            $table->foreignId('id_estado_envio')->constrained('estados_envio');
            $table->timestampTz('fecha')->default(DB::raw('now()'));
        });
        
        Schema::table('historialestados', function (Blueprint $table) {
            $table->index(['id_envio', 'fecha'], 'ix_historial_envio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historialestados');
    }
};

