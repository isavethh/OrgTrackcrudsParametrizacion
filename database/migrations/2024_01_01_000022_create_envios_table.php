<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios')->cascadeOnDelete();
            $table->timestampTz('fecha_creacion')->notNullable()->default(now());
            $table->timestampTz('fecha_inicio')->nullable();
            $table->timestampTz('fecha_entrega')->nullable();
            $table->foreignId('id_direccion')->constrained('direccion')->cascadeOnDelete();
        });

        DB::statement('CREATE INDEX ix_envios_id_direccion ON envios(id_direccion)');
        DB::statement('CREATE INDEX ix_envios_id_usuario ON envios(id_usuario)');
    }

    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};
