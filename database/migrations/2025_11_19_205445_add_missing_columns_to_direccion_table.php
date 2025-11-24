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
            if (!Schema::hasColumn('direccion', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre_ruta');
            }
            if (!Schema::hasColumn('direccion', 'latitud')) {
                $table->decimal('latitud', 10, 8)->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('direccion', 'longitud')) {
                $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
            }
            if (!Schema::hasColumn('direccion', 'orden')) {
                $table->integer('orden')->default(1)->after('nombre_punto_entrega');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direccion', function (Blueprint $table) {
            if (Schema::hasColumn('direccion', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
            if (Schema::hasColumn('direccion', 'latitud')) {
                $table->dropColumn('latitud');
            }
            if (Schema::hasColumn('direccion', 'longitud')) {
                $table->dropColumn('longitud');
            }
            if (Schema::hasColumn('direccion', 'orden')) {
                $table->dropColumn('orden');
            }
        });
    }
};
