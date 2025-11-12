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
        Schema::table('envio', function (Blueprint $table) {
            // Eliminar campos antiguos
            $table->dropColumn(['fecha_envio', 'fecha_entrega_estimada']);
        });
        
        Schema::table('envio', function (Blueprint $table) {
            // Agregar nuevos campos de fecha/hora
            $table->date('fecha_entrega')->nullable()->after('admin_id');
            $table->time('hora_entrega_estimada')->nullable()->after('fecha_entrega');
            
            // Campos para productos (JSON para guardar array de productos con cantidades)
            $table->json('productos')->nullable()->comment('Array de productos con cantidades: [{nombre, categoria, cantidad, peso_unitario, peso_total}]');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envio', function (Blueprint $table) {
            $table->dropColumn(['fecha_entrega', 'hora_entrega_estimada', 'productos']);
        });
        
        Schema::table('envio', function (Blueprint $table) {
            $table->timestampTz('fecha_envio')->useCurrent();
            $table->timestampTz('fecha_entrega_estimada')->nullable();
        });
    }
};
