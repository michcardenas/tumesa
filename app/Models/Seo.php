<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;

    protected $table = 'seo';

    protected $fillable = [
        'id_pagina',
        'meta_title',
        'meta_description', 
        'meta_keywords',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'focus_keyword',
        'schema_markup',
    ];

    protected $casts = [
        'schema_markup' => 'array',
    ];

    // Scope para obtener SEO de una página específica
    public function scopePorPagina($query, $paginaId)
    {
        return $query->where('id_pagina', $paginaId);
    }

    // Método estático para obtener SEO de una página
    public static function obtenerSeo($paginaId)
    {
        return static::where('id_pagina', $paginaId)->first();
    }

    // Accessors para validar longitudes
    public function getMetaTitleLengthAttribute()
    {
        return strlen($this->meta_title ?? '');
    }

    public function getMetaDescriptionLengthAttribute()
    {
        return strlen($this->meta_description ?? '');
    }
}