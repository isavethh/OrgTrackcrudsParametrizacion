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
        Schema::create('checklist_condicion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion')->unique()->constrained('asignacionmultiple');
            $table->timestampTz('fecha')->default(DB::raw('now()'));
            $table->string('observaciones', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_condicion');
    }
};

