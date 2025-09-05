<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reseña extends Model
{
    use HasFactory;

    protected $table = 'reseñas';

    protected $fillable = [
        'id_cena',
        'id_reserva',
        'id_user',
        'rating',
        'comentario',
    ];

    // Relaciones
    public function cena()
    {
        return $this->belongsTo(Cena::class, 'id_cena');
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
