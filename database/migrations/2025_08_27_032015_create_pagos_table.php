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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            
            // Referencias principales
            $table->foreignId('reserva_id')->constrained('reservas')->onDelete('cascade');
            $table->foreignId('cena_id')->constrained('cenas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Datos de MercadoPago
            $table->string('payment_id')->nullable(); // payment_id de MP
            $table->string('collection_id')->nullable(); // collection_id de MP
            $table->string('preference_id')->nullable(); // preference_id de MP
            $table->string('merchant_order_id')->nullable(); // merchant_order_id de MP
            $table->string('external_reference')->nullable(); // código de reserva
            
            // Estado y tipo de pago
            $table->string('status'); // approved, pending, rejected, etc.
            $table->string('collection_status')->nullable(); // approved, pending, etc.
            $table->string('payment_type')->nullable(); // credit_card, debit_card, etc.
            $table->string('payment_method')->nullable(); // visa, mastercard, etc.
            
            // Montos
            $table->decimal('monto_total', 10, 2);
            $table->string('currency_id', 3)->default('ARS'); // ARS, COP, etc.
            
            // Datos adicionales de MP
            $table->json('datos_completos')->nullable(); // Toda la respuesta de MP
            $table->string('processing_mode')->nullable(); // aggregator, gateway
            $table->string('site_id')->nullable(); // MLA, MCO, etc.
            
            // Fechas importantes
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamp('fecha_rechazo')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['payment_id']);
            $table->index(['collection_id']);
            $table->index(['external_reference']);
            $table->index(['status']);
            $table->unique(['payment_id', 'collection_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};