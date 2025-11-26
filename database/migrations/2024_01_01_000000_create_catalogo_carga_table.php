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
        Schema::create('catalogo_carga', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50);
            $table->string('variedad', 50);
            $table->string('empaque', 50);
            $table->string('descripcion', 150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_carga');
    }
};

