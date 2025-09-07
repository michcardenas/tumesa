<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paginas', function (Blueprint $table) {
            $table->id();
            $table->string('pagina_id')->index(); // Para agrupar contenido por página
            $table->string('clave')->index(); // Identificador del contenido
            $table->text('valor')->nullable(); // El contenido en sí
            $table->timestamps();
            
            // Índice único para evitar claves duplicadas en la misma página
            $table->unique(['pagina_id', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paginas');
    }
};