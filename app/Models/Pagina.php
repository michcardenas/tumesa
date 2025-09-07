<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagina extends Model
{
    use HasFactory;

    protected $table = 'paginas';

    protected $fillable = [
        'pagina_id',
        'clave',
        'valor',
    ];

    // Scope para obtener contenido de una página específica
    public function scopePorPagina($query, $paginaId)
    {
        return $query->where('pagina_id', $paginaId);
    }

    // Método estático para obtener un valor específico
    public static function obtenerValor($paginaId, $clave, $default = null)
    {
        return static::where('pagina_id', $paginaId)
                    ->where('clave', $clave)
                    ->value('valor') ?? $default;
    }
}