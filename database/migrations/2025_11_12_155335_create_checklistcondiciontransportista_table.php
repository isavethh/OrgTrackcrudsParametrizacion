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
        Schema::create('checklistcondiciontransportista', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_id')->unique()->constrained('asignacionmultiple')->onDelete('cascade');
            $table->boolean('vehiculo_operativo')->default(false);
            $table->boolean('documentos_vigentes')->default(false);
            $table->boolean('equipo_seguridad')->default(false);
            $table->boolean('combustible_suficiente')->default(false);
            $table->boolean('llantas_buen_estado')->default(false);
            $table->boolean('luces_funcionales')->default(false);
            $table->boolean('frenos_operativos')->default(false);
            $table->boolean('sistema_comunicacion')->default(false);
            $table->boolean('equipo_carga_descarga')->default(false);
            $table->boolean('conductor_descansado')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestampTz('fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklistcondiciontransportista');
    }
};
