<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_envio')->constrained('envios', 'id');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id'); // quien califica (cliente)
            $table->foreignId('id_transportista')->constrained('transportistas', 'id'); // quien es calificado
            $table->integer('puntuacion')->check('puntuacion >= 1 AND puntuacion <= 5');
            $table->string('comentario', 500)->nullable();
            $table->timestamp('fecha')->useCurrent();
            
            $table->index('id_envio');
            $table->index('id_transportista');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
