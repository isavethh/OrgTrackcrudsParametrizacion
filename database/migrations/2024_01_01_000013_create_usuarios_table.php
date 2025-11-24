<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('correo', 100)->unique()->notNullable();
            $table->string('contrasena', 255)->notNullable();
            $table->foreignId('id_rol')->constrained('roles_usuario')->cascadeOnDelete();
            $table->timestampTz('fecha_registro')->notNullable()->default(now());
            $table->foreignId('id_persona')->constrained('persona')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
