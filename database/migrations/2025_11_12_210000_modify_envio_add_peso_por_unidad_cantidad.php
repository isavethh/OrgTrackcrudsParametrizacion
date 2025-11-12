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
            // Agregar campos para peso por unidad y cantidad
            $table->decimal('peso_por_unidad', 10, 2)->nullable()->after('peso');
            $table->integer('cantidad_productos')->nullable()->after('peso_por_unidad');
            
            // Hacer admin_id y estado opcionales/eliminar required
            $table->string('estado', 50)->nullable()->change();
            $table->foreignId('admin_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envio', function (Blueprint $table) {
            $table->dropColumn(['peso_por_unidad', 'cantidad_productos']);
            
            // Restaurar estado como requerido
            $table->string('estado', 50)->nullable(false)->change();
        });
    }
};
