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
        Schema::create('firmaenvio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->unique()->constrained('asignacionmultiple')->onDelete('cascade');
            $table->text('imagenfirma');
            $table->timestampTz('fechafirma')->default(DB::raw('now()'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firmaenvio');
    }
};

