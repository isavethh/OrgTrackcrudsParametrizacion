<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Solo añadir las 3 FKs a catálogos en carga
        Schema::table('carga', function (Blueprint $table) {
            $table->foreignId('id_categoria')->nullable()->constrained('catalogo_categorias')->onDelete('set null');
            $table->foreignId('id_producto')->nullable()->constrained('catalogo_productos')->onDelete('set null');
            $table->foreignId('id_tipo_empaque')->nullable()->constrained('catalogo_tipos_empaque')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('carga', function (Blueprint $table) {
            $table->dropForeign(['id_categoria']);
            $table->dropForeign(['id_producto']);
            $table->dropForeign(['id_tipo_empaque']);
            $table->dropColumn(['id_categoria', 'id_producto', 'id_tipo_empaque']);
        });
    }
};
