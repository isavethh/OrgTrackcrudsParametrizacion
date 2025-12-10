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
            $table->foreignId('id_categoria')->nullable()->constrained('catalogo_categorias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogo_tamano_conteo', function (Blueprint $table) {
            //
        });
    }
};
