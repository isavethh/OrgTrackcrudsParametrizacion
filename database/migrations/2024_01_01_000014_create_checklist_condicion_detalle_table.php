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
        Schema::create('checklist_condicion_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_checklist')->constrained('checklist_condicion');
            $table->foreignId('id_condicion')->constrained('condiciones_transporte');
            $table->boolean('valor');
            $table->string('comentario', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_condicion_detalle');
    }
};

