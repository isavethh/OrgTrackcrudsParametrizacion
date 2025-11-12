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
        Schema::create('qrtoken', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('cliente')->onDelete('cascade');
            $table->text('token')->unique();
            $table->text('imagenqr');
            $table->boolean('usado')->default(false);
            $table->timestampTz('fecha_creacion')->useCurrent();
            $table->timestampTz('fecha_expiracion');
            
            // Check constraint (se implementa a nivel de aplicación en Laravel)
        });
        
        // Crear índices
        Schema::table('qrtoken', function (Blueprint $table) {
            $table->index('cliente_id', 'idx_qrtoken_cliente');
            $table->index('token', 'idx_qrtoken_token');
            $table->index('usado', 'idx_qrtoken_usado');
            $table->index('fecha_expiracion', 'idx_qrtoken_expiracion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qrtoken');
    }
};
