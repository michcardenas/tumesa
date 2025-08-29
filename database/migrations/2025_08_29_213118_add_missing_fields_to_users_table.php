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
            // Campo role - para complementar Spatie Permissions
            
            // Campos adicionales del perfil
            $table->string('telefono')->nullable()->after('avatar');
            $table->text('direccion')->nullable()->after('telefono');
            
            // Provider - parece que ya lo tienes, pero por si acaso
            // $table->string('provider')->nullable()->default('manual')->after('direccion');
            
            // Índices para optimizar consultas
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['provider']); // Si ya tienes el campo
            
            $table->dropColumn([
                'role',
                'telefono', 
                'direccion',
                // 'provider', // No borrar si ya lo tienes
            ]);
        });
    }
};