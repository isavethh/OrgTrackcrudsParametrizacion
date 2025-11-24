<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direccionsegmento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direccion_id')->constrained('direccion')->cascadeOnDelete();
            $table->text('segmentogeojson')->notNullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direccionsegmento');
    }
};
