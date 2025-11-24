<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direccion', function (Blueprint $table) {
            $table->id();
            $table->string('nombreorigen', 200)->notNullable();
            $table->decimal('origen_lng', 11, 8)->notNullable();
            $table->decimal('origen_lat', 10, 8)->notNullable();
            $table->string('nombredestino', 200)->notNullable();
            $table->decimal('destino_lng', 11, 8)->notNullable();
            $table->decimal('destino_lat', 10, 8)->notNullable();
            $table->text('rutageojson')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direccion');
    }
};
