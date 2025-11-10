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
        Schema::create('recogidaentrega', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_recogida');
            $table->time('hora_recogida');
            $table->time('hora_entrega');
            $table->string('instrucciones_recogida', 255)->nullable();
            $table->string('instrucciones_entrega', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recogidaentrega');
    }
};

