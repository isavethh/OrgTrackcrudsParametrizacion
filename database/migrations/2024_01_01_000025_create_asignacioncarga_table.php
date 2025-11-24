<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignacioncarga', function (Blueprint $table) {
            $table->foreignId('id_asignacion')->constrained('asignacionmultiple')->cascadeOnDelete();
            $table->foreignId('id_carga')->constrained('carga')->cascadeOnDelete();
            $table->primary(['id_asignacion', 'id_carga']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacioncarga');
    }
};
