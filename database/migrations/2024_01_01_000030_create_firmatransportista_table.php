<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firmatransportista', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->unique()->constrained('asignacionmultiple')->cascadeOnDelete();
            $table->text('imagenfirma')->notNullable();
            $table->timestampTz('fechafirma')->notNullable()->default(now());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firmatransportista');
    }
};
