<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qrtoken', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->unique()->constrained('asignacionmultiple')->onDelete('cascade');
            $table->foreignId('id_estado_qrtoken')->constrained('estados_qrtoken');
            $table->string('token', 500)->unique();
            $table->text('imagenqr');
            $table->timestampTz('fecha_creacion')->default(DB::raw('now()'));
            $table->timestampTz('fecha_expiracion');
        });
        
        Schema::table('qrtoken', function (Blueprint $table) {
            $table->index('fecha_expiracion', 'ix_qrtoken_exp');
            $table->index('id_estado_qrtoken', 'ix_qrtoken_estado');
        });
        
        // Agregar el check constraint para fecha_expiracion > fecha_creacion
        DB::statement('ALTER TABLE qrtoken ADD CONSTRAINT ck_qrtoken_fecha CHECK (fecha_expiracion > fecha_creacion)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qrtoken');
    }
};

