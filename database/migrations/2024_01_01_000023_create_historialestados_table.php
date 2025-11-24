<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historialestados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_envio')->constrained('envios')->cascadeOnDelete();
            $table->foreignId('id_estado_envio')->constrained('estados_envio')->cascadeOnDelete();
            $table->timestampTz('fecha')->notNullable()->default(now());
            $table->text('observaciones')->nullable();
        });

        DB::statement('CREATE INDEX ix_historial_envio ON historialestados(id_envio, fecha)');
    }

    public function down(): void
    {
        Schema::dropIfExists('historialestados');
    }
};
