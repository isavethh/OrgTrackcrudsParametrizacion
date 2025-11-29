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
        Schema::create('direccion', function (Blueprint $table) {
            $table->id();
            $table->string('nombreorigen', 200)->nullable();
            $table->decimal('origen_lng', 11, 8)->nullable();
            $table->decimal('origen_lat', 10, 8)->nullable();
            $table->string('nombredestino', 200)->nullable();
            $table->decimal('destino_lng', 11, 8)->nullable();
            $table->decimal('destino_lat', 10, 8)->nullable();
            $table->text('rutageojson')->nullable();
            // Sin timestamps para coincidir con OrgTrack
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direccion');
    }
};
