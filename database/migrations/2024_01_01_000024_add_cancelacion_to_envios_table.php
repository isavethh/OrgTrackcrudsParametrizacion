<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->boolean('cancelado')->default(false)->after('id_direccion');
            $table->foreignId('id_motivo_cancelacion')->nullable()->after('cancelado')->constrained('motivos_cancelacion', 'id');
            $table->timestamp('fecha_cancelacion')->nullable()->after('id_motivo_cancelacion');
            $table->string('observacion_cancelacion', 500)->nullable()->after('fecha_cancelacion');
        });
    }

    public function down(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->dropForeign(['id_motivo_cancelacion']);
            $table->dropColumn(['cancelado', 'id_motivo_cancelacion', 'fecha_cancelacion', 'observacion_cancelacion']);
        });
    }
};
