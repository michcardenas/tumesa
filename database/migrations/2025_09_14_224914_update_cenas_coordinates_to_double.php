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
        Schema::table('cenas', function (Blueprint $table) {
            // Cambiar de DECIMAL a DOUBLE para mejor precisión
            // DOUBLE permite hasta 15 dígitos de precisión
            $table->double('latitude', 10, 7)->nullable()->change();
            $table->double('longitude', 10, 7)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cenas', function (Blueprint $table) {
            // Revertir a DECIMAL si es necesario
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
        });
    }
};