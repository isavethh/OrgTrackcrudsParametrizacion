<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transportistas', function (Blueprint $table) {
            $table->id();
            $table->string('ci', 20)->unique()->notNullable();
            $table->string('telefono', 20)->nullable();
            $table->string('licencia', 50)->notNullable();
            $table->foreignId('id_estado_transportista')->constrained('estados_transportista')->cascadeOnDelete();
            $table->timestampTz('fecha_registro')->notNullable()->default(now());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transportistas');
    }
};
