<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('correo', 100)->unique();
            $table->string('contrasena', 100);
            $table->foreignId('id_rol')->constrained('roles_usuario');
            $table->timestampTz('fecha_registro')->default(DB::raw('now()'))->nullable(false);
            $table->foreignId('id_persona')->constrained('persona');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};

