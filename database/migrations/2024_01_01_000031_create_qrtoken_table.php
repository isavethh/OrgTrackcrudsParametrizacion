<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qrtoken', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->unique()->constrained('asignacionmultiple')->cascadeOnDelete();
            $table->foreignId('id_estado_qrtoken')->constrained('estados_qrtoken')->cascadeOnDelete();
            $table->string('token', 255)->unique()->notNullable();
            $table->text('imagenqr')->nullable();
            $table->timestampTz('fecha_creacion')->notNullable()->default(now());
            $table->timestampTz('fecha_expiracion')->notNullable();
        });

        DB::statement('ALTER TABLE qrtoken ADD CONSTRAINT check_fecha_expiracion CHECK (fecha_expiracion > fecha_creacion)');
        DB::statement('CREATE INDEX ix_qrtoken_exp ON qrtoken(fecha_expiracion)');
        DB::statement('CREATE INDEX ix_qrtoken_estado ON qrtoken(id_estado_qrtoken)');
    }

    public function down(): void
    {
        Schema::dropIfExists('qrtoken');
    }
};
