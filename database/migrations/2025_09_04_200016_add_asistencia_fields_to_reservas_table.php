<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAsistenciaFieldsToReservasTable extends Migration
{
    public function up()
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->boolean('asistencia_marcada')->default(false)->after('estado');
            $table->enum('estado_asistencia', ['presente', 'ausente', 'pendiente'])->default('pendiente')->after('asistencia_marcada');
            $table->timestamp('fecha_asistencia_marcada')->nullable()->after('estado_asistencia');
            $table->text('comentarios_asistencia')->nullable()->after('fecha_asistencia_marcada');
        });
    }

    public function down()
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn([
                'asistencia_marcada',
                'estado_asistencia', 
                'fecha_asistencia_marcada',
                'comentarios_asistencia'
            ]);
        });
    }
}