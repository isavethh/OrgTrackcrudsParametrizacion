<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->unique()->constrained('usuarios')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
