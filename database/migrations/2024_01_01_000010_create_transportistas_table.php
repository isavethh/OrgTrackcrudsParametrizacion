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
        Schema::create('transportistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->nullable()->unique()->constrained('usuarios');
            $table->string('ci', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('estado', 20);
            $table->timestampTz('fecha_registro')->default(DB::raw('now()'));
        });
        
        DB::statement("ALTER TABLE transportistas ADD CONSTRAINT chk_transportistas_estado CHECK (estado IN ('Inactivo','No Disponible','En ruta','Disponible'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transportistas');
    }
};

