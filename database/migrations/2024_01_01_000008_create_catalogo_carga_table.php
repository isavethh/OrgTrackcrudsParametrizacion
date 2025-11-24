<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_carga', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50)->notNullable();
            $table->string('variedad', 50)->nullable();
            $table->string('empaque', 50)->nullable();
            $table->string('descripcion', 200)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_carga');
    }
};
