<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('catalogo_tamano_conteo', function (Blueprint $table) {
            $table->dropForeign(['id_categoria']);
            $table->dropColumn('id_categoria');
            $table->foreignId('id_producto')->nullable()->constrained('catalogo_productos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('catalogo_tamano_conteo', function (Blueprint $table) {
            $table->dropForeign(['id_producto']);
            $table->dropColumn('id_producto');
            $table->foreignId('id_categoria')->nullable()->constrained('catalogo_categorias')->onDelete('cascade');
        });
    }
};
