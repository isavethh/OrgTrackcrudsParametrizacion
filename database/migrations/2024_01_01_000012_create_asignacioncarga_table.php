<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asignacioncarga', function (Blueprint $table) {
            $table->foreignId('id_asignacion')->constrained('asignacionmultiple');
            $table->foreignId('id_carga')->constrained('carga');
            $table->primary(['id_asignacion', 'id_carga']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacioncarga');
    }
};



