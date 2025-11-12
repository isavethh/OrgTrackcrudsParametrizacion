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
        Schema::create('checklistincidentetransporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_id')->unique()->constrained('asignacionmultiple')->onDelete('cascade');
            $table->boolean('accidente_trafico')->default(false);
            $table->boolean('falla_mecanica')->default(false);
            $table->boolean('condiciones_climaticas')->default(false);
            $table->boolean('robo_intento_robo')->default(false);
            $table->boolean('dano_mercancia')->default(false);
            $table->boolean('retraso_entrega')->default(false);
            $table->boolean('problema_documentacion')->default(false);
            $table->boolean('acceso_bloqueado')->default(false);
            $table->boolean('cliente_ausente')->default(false);
            $table->boolean('otros')->default(false);
            $table->text('descripcion_incidente')->nullable();
            $table->timestampTz('fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklistincidentetransporte');
    }
};
