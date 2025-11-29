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
        Schema::create('envios', function (Blueprint $table) {
            $table->id();
            $table->string('usuario_nombre'); // Nombre del usuario que envía
            $table->string('sistema_origen'); // 'agronexus' o 'orgtrack'
            $table->foreignId('direccion_id')->constrained('direccion')->cascadeOnDelete();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->date('fecha_entrega_aproximada')->nullable();
            $table->time('hora_entrega_aproximada')->nullable();
            $table->decimal('peso_total_envio', 10, 2)->default(0);
            $table->decimal('costo_total_envio', 10, 2)->default(0);
            $table->string('estado')->default('pendiente'); // pendiente, en_transito, entregado
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};
