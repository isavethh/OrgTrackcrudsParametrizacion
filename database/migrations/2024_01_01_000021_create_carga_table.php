<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_catalogo_carga')->constrained('catalogo_carga')->cascadeOnDelete();
            $table->integer('cantidad')->notNullable();
            $table->decimal('peso', 10, 2)->notNullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carga');
    }
};
