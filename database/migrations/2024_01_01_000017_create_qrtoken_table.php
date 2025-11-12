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
            $table->foreignId('id_usuario_cliente')->constrained('usuarios')->onDelete('cascade');
            $table->text('token')->unique();
            $table->text('imagenqr');
            $table->boolean('usado')->default(false);
            $table->timestampTz('fecha_creacion')->default(DB::raw('now()'));
            $table->timestampTz('fecha_expiracion');
            
            $table->index('usado');
            $table->index('fecha_expiracion');
        });
        
        // Agregar el check constraint para fecha_expiracion > fecha_creacion
        DB::statement('ALTER TABLE qrtoken ADD CONSTRAINT chk_qrtoken_fecha CHECK (fecha_expiracion > fecha_creacion)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qrtoken');
    }
};

