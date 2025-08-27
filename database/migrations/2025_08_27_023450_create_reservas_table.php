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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            
            // Referencias principales
            $table->foreignId('cena_id')->constrained('cenas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Información de la reserva
            $table->integer('cantidad_comensales')->default(1);
            $table->decimal('precio_total', 10, 2);
            $table->decimal('precio_por_persona', 8, 2);
            
            // Estado de la reserva
            $table->enum('estado', ['pendiente', 'confirmada', 'pagada', 'cancelada', 'completada'])->default('pendiente');
            $table->enum('estado_pago', ['pendiente', 'pagado', 'reembolsado', 'fallido'])->default('pendiente');
            
            // Información del checkout
            $table->string('nombre_contacto');
            $table->string('telefono_contacto');
            $table->string('email_contacto');
            
            // Detalles adicionales
            $table->text('comentarios_especiales')->nullable();
            $table->text('restricciones_alimentarias')->nullable();
            $table->text('solicitudes_especiales')->nullable();
            
            // Información de pago
            $table->string('metodo_pago')->nullable();
            $table->string('transaccion_id')->nullable();
            $table->json('datos_pago')->nullable(); // Para guardar respuesta del gateway
            $table->timestamp('fecha_pago')->nullable();
            
            // Fechas importantes
            $table->timestamp('fecha_reserva')->useCurrent();
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();
            
            // Términos y condiciones
            $table->boolean('acepta_terminos')->default(false);
            $table->boolean('acepta_politica_cancelacion')->default(false);
            
            // Códigos únicos
            $table->string('codigo_reserva')->unique();
            $table->string('codigo_qr')->nullable(); // Para check-in en la cena
            
            // Calificación post-cena
            $table->integer('calificacion')->nullable(); // 1-5 estrellas
            $table->text('resena')->nullable();
            $table->timestamp('fecha_resena')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['cena_id', 'estado']);
            $table->index(['user_id', 'estado']);
            $table->index('codigo_reserva');
            $table->index('fecha_reserva');
            $table->index(['estado', 'estado_pago']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};