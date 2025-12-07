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
            $table->string('nombre_remitente', 100)->nullable();
            $table->string('telefono_remitente', 20)->nullable();
            $table->string('email_remitente', 100)->nullable();
            $table->boolean('es_publico')->default(false)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->dropColumn(['nombre_remitente', 'telefono_remitente', 'email_remitente', 'es_publico']);
        });
    }
};
