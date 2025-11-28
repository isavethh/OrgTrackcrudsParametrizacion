<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_incidente_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_checklist')->constrained('checklist_incidente', 'id');
            $table->foreignId('id_tipo_incidente')->constrained('tipos_incidente_transporte', 'id');
            $table->boolean('ocurrio'); // true si ocurrió el incidente
            $table->string('descripcion', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_incidente_detalle');
    }
};
