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
            $table->string('nombre', 100)->nullable();
            $table->string('apellido', 100)->nullable();
            $table->string('correo', 100)->unique();
            $table->string('contrasena', 100)->nullable();
            $table->string('rol', 20)->nullable();
            $table->timestampTz('fecha_registro')->default(DB::raw('now()'));
        });
        
        DB::statement("ALTER TABLE usuarios ADD CONSTRAINT chk_usuarios_rol CHECK (rol IN ('transportista','cliente','admin'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};

