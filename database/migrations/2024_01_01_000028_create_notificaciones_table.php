<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios', 'id')->onDelete('cascade');
            $table->string('tipo', 50); // estado_envio, asignacion, incidente, etc
            $table->string('titulo', 150);
            $table->string('mensaje', 500);
            $table->boolean('leida')->default(false);
            $table->foreignId('id_envio')->nullable()->constrained('envios', 'id')->onDelete('cascade');
            $table->timestamp('fecha')->useCurrent();
            
            $table->index(['id_usuario', 'fecha']);
            $table->index('leida');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
