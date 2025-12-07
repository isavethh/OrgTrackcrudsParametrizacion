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
        Schema::table('envios', function (Blueprint $table) {
            $table->string('numero_solicitud', 50)->nullable()->after('email_remitente');
            $table->date('fecha_requerida')->nullable()->after('numero_solicitud');
            $table->integer('prioridad')->default(1)->after('fecha_requerida');
            $table->text('observaciones_solicitud')->nullable()->after('prioridad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->dropColumn(['numero_solicitud', 'fecha_requerida', 'prioridad', 'observaciones_solicitud']);
        });
    }
};
