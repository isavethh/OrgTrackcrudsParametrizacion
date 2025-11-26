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
        Schema::table('transportistas', function (Blueprint $table) {
            $table->dropUnique(['ci']); // Eliminar unique constraint de ci primero
            $table->dropColumn(['ci', 'telefono']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transportistas', function (Blueprint $table) {
            $table->string('ci', 20)->unique()->after('id_usuario');
            $table->string('telefono', 20)->nullable()->after('ci');
        });
    }
};

