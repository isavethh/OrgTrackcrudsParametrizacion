<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios');
            $table->string('estado', 50);
            $table->timestampTz('fecha_creacion')->default(DB::raw('now()'));
            $table->timestampTz('fecha_inicio')->nullable();
            $table->timestampTz('fecha_entrega')->nullable();
            $table->foreignId('id_direccion')->constrained('direccion');
            
            $table->index('id_direccion');
        });
        
        DB::statement("ALTER TABLE envios ADD CONSTRAINT chk_envios_estado CHECK (estado IN ('Parcialmente entregado','Entregado','En curso','Asignado','Pendiente'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};

