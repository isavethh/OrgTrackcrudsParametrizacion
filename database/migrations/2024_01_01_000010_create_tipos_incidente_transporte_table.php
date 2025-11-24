<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_incidente_transporte', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->notNullable();
            $table->string('titulo', 100)->notNullable();
            $table->string('descripcion', 200)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_incidente_transporte');
    }
};
