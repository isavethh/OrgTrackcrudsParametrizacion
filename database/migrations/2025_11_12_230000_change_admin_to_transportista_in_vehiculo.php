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
        Schema::table('vehiculo', function (Blueprint $table) {
            // Eliminar la foreign key y columna admin_id si existe
            if (Schema::hasColumn('vehiculo', 'admin_id')) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            }
            
            // Agregar transportista_id
            if (!Schema::hasColumn('vehiculo', 'transportista_id')) {
                $table->foreignId('transportista_id')->nullable()->constrained('transportista')->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculo', function (Blueprint $table) {
            // Eliminar transportista_id
            if (Schema::hasColumn('vehiculo', 'transportista_id')) {
                $table->dropForeign(['transportista_id']);
                $table->dropColumn('transportista_id');
            }
            
            // Restaurar admin_id
            if (!Schema::hasColumn('vehiculo', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->constrained('admin')->after('id');
            }
        });
    }
};
