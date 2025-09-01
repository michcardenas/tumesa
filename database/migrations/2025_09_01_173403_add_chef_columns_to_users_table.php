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
        Schema::table('users', function (Blueprint $table) {
            // Verificar si las columnas no existen antes de agregarlas
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('users', 'especialidad')) {
                $table->string('especialidad')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'experiencia_anos')) {
                $table->integer('experiencia_anos')->nullable()->after('especialidad');
            }
            if (!Schema::hasColumn('users', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0.00)->after('experiencia_anos');
            }
            if (!Schema::hasColumn('users', 'instagram')) {
                $table->string('instagram')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('users', 'facebook')) {
                $table->string('facebook')->nullable()->after('instagram');
            }
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('facebook');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Solo eliminar las columnas que agregamos
            $columnsToRemove = [];
            
            if (Schema::hasColumn('users', 'website')) {
                $columnsToRemove[] = 'website';
            }
            if (Schema::hasColumn('users', 'facebook')) {
                $columnsToRemove[] = 'facebook';
            }
            if (Schema::hasColumn('users', 'instagram')) {
                $columnsToRemove[] = 'instagram';
            }
            if (Schema::hasColumn('users', 'rating')) {
                $columnsToRemove[] = 'rating';
            }
            if (Schema::hasColumn('users', 'experiencia_anos')) {
                $columnsToRemove[] = 'experiencia_anos';
            }
            if (Schema::hasColumn('users', 'especialidad')) {
                $columnsToRemove[] = 'especialidad';
            }
            if (Schema::hasColumn('users', 'bio')) {
                $columnsToRemove[] = 'bio';
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};