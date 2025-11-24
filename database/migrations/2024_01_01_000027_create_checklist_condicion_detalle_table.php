<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_condicion_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_checklist')->constrained('checklist_condicion')->cascadeOnDelete();
            $table->foreignId('id_condicion')->constrained('condiciones_transporte')->cascadeOnDelete();
            $table->boolean('valor')->notNullable();
            $table->text('comentario')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_condicion_detalle');
    }
};
