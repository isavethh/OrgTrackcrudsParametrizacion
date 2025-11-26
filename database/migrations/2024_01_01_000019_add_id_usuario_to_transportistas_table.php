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
            $table->foreignId('id_usuario')->nullable()->after('id')->constrained('usuarios')->onDelete('cascade');
            $table->unique('id_usuario');
        });
        
        // NOTA: Si tienes transportistas existentes, necesitarás migrarlos manualmente
        // asociando cada transportista con su usuario correspondiente por CI
        // Ejemplo: UPDATE transportistas t SET id_usuario = u.id FROM usuarios u 
        //          INNER JOIN persona p ON u.id_persona = p.id WHERE t.ci = p.ci;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transportistas', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->dropUnique(['id_usuario']);
            $table->dropColumn('id_usuario');
        });
    }
};

