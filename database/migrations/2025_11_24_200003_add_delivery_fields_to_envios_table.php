<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->date('fecha_entrega_aproximada')->nullable()->after('fecha_entrega');
            $table->time('hora_entrega_aproximada')->nullable()->after('fecha_entrega_aproximada');
            $table->decimal('peso_total_envio', 10, 2)->default(0)->after('hora_entrega_aproximada');
            $table->decimal('costo_total_envio', 10, 2)->default(0)->after('peso_total_envio');
        });
    }

    public function down(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->dropColumn(['fecha_entrega_aproximada', 'hora_entrega_aproximada', 'peso_total_envio', 'costo_total_envio']);
        });
    }
};
