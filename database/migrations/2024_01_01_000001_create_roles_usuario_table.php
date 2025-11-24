<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique()->notNullable();
            $table->string('nombre', 50)->notNullable();
            $table->string('descripcion', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles_usuario');
    }
};
