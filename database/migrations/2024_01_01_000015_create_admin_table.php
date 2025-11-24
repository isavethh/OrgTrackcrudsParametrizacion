<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->unique()->constrained('usuarios')->cascadeOnDelete();
            $table->integer('nivel_acceso')->notNullable()->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
