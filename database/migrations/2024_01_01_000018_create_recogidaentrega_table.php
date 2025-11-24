<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recogidaentrega', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_recogida')->notNullable();
            $table->time('hora_recogida')->notNullable();
            $table->time('hora_entrega')->notNullable();
            $table->text('instrucciones')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recogidaentrega');
    }
};
