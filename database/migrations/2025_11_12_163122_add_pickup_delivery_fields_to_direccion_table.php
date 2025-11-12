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
        Schema::table('direccion', function (Blueprint $table) {
            $table->decimal('punto_recogida_lat', 10, 8)->nullable()->after('longitud');
            $table->decimal('punto_recogida_lng', 11, 8)->nullable()->after('punto_recogida_lat');
            $table->string('nombre_punto_recogida', 200)->nullable()->after('punto_recogida_lng');
            $table->decimal('punto_entrega_lat', 10, 8)->nullable()->after('nombre_punto_recogida');
            $table->decimal('punto_entrega_lng', 11, 8)->nullable()->after('punto_entrega_lat');
            $table->string('nombre_punto_entrega', 200)->nullable()->after('punto_entrega_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direccion', function (Blueprint $table) {
            $table->dropColumn([
                'punto_recogida_lat',
                'punto_recogida_lng',
                'nombre_punto_recogida',
                'punto_entrega_lat',
                'punto_entrega_lng',
                'nombre_punto_entrega'
            ]);
        });
    }
};
