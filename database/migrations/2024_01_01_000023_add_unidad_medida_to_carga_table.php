<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carga', function (Blueprint $table) {
            $table->foreignId('id_unidad_medida')->nullable()->after('peso')->constrained('unidades_medida', 'id');
        });
    }

    public function down(): void
    {
        Schema::table('carga', function (Blueprint $table) {
            $table->dropForeign(['id_unidad_medida']);
            $table->dropColumn('id_unidad_medida');
        });
    }
};
