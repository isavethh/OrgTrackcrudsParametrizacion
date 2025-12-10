<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('especificacion_forma_pedido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_carga')->unique()->constrained('carga')->onDelete('cascade');
            $table->string('forma_pedido', 50)->nullable(); // 'empaques', 'cajas', 'bolsas', 'pallets'
            $table->integer('cantidad_pedido')->nullable();
            $table->integer('empaques_calculados')->nullable();
            $table->integer('unidades_por_pallet')->nullable();
            $table->integer('numero_pallets')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especificacion_forma_pedido');
    }
};
