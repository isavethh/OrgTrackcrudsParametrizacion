<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('carga', function (Blueprint $table) {
            $table->dropForeign(['id_catalogo_carga']);
            $table->dropColumn('id_catalogo_carga');
        });
    }

    public function down(): void
    {
        Schema::table('carga', function (Blueprint $table) {
            $table->foreignId('id_catalogo_carga')->nullable()->constrained('catalogo_carga')->onDelete('set null');
        });
    }
};
