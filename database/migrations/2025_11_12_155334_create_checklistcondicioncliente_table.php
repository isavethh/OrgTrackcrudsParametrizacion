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
        Schema::create('checklistcondicioncliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_id')->unique()->constrained('asignacionmultiple')->onDelete('cascade');
            $table->boolean('direccion_correcta')->default(false);
            $table->boolean('contacto_disponible')->default(false);
            $table->boolean('acceso_al_lugar')->default(false);
            $table->boolean('documentacion_lista')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestampTz('fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklistcondicioncliente');
    }
};
