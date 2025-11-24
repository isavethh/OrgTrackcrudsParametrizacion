<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->notNullable();
            $table->string('apellido', 100)->notNullable();
            $table->string('ci', 20)->unique()->notNullable();
            $table->string('telefono', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};
