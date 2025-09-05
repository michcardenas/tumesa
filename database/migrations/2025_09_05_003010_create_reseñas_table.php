<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reseñas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cena')->constrained('cenas')->onDelete('cascade');
            $table->foreignId('id_reserva')->constrained('reservas')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->comment('1 a 5 estrellas');
            $table->text('comentario')->nullable();
            $table->timestamps();

            // Evitar que un mismo usuario califique más de una vez la misma cena
            $table->unique(['id_cena', 'id_user']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseñas');
    }
};
