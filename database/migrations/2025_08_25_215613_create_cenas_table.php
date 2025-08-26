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
        Schema::create('cenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Información básica de la cena
            $table->string('title');
            $table->dateTime('datetime');
            $table->integer('guests_max'); // Máximo de comensales
            $table->integer('guests_current')->default(0); // Comensales actuales registrados
            $table->decimal('price', 10, 2); // Precio por persona
            $table->text('menu'); // Descripción del menú
            
            // Ubicación
            $table->string('location');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            
            // Imágenes
            $table->string('cover_image')->nullable();
            $table->json('gallery_images')->nullable(); // Array de imágenes adicionales
            
            // Estado de la cena
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('published');
            
            // Configuración adicional
            $table->boolean('is_active')->default(true);
            $table->text('special_requirements')->nullable(); // Requisitos especiales
            $table->text('cancellation_policy')->nullable(); // Política de cancelación
            
            $table->timestamps();
            
            // Índices
            $table->index('datetime');
            $table->index('status');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cenas');
    }
};