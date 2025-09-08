<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Seo;

class SeoController extends Controller
{
    public function experiencias(): View
    {
        $seoData = Seo::obtenerSeo('experiencias');
        $pageInfo = [
            'id_pagina' => 'experiencias',
            'titulo' => 'Experiencias',
            'url_publica' => route('home') // o la ruta que corresponda
        ];
        
        return view('admin.seo.edit', compact('seoData', 'pageInfo'));
    }

    public function serChef(): View
    {
        $seoData = Seo::obtenerSeo('ser-chef');
        $pageInfo = [
            'id_pagina' => 'ser-chef',
            'titulo' => 'Ser Chef Anfitrión',
            'url_publica' => route('ser-chef')
        ];
        
        return view('admin.seo.edit', compact('seoData', 'pageInfo'));
    }

    public function comoFunciona(): View
    {
        $seoData = Seo::obtenerSeo('como-funciona');
        $pageInfo = [
            'id_pagina' => 'como-funciona',
            'titulo' => 'Cómo Funciona',
            'url_publica' => route('como-funciona')
        ];
        
        return view('admin.seo.edit', compact('seoData', 'pageInfo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_pagina' => 'required|string',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'robots' => 'nullable|string|max:50',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'og_image' => 'nullable|url',
            'og_type' => 'nullable|string|max:20',
            'twitter_title' => 'nullable|string|max:60',
            'twitter_description' => 'nullable|string|max:160',
            'twitter_image' => 'nullable|url',
            'focus_keyword' => 'nullable|string',
            'schema_markup' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Convertir schema_markup de JSON string a array si no está vacío
        if (!empty($data['schema_markup'])) {
            $data['schema_markup'] = json_decode($data['schema_markup'], true);
        } else {
            $data['schema_markup'] = null;
        }

        Seo::updateOrCreate(
            ['id_pagina' => $request->id_pagina],
            $data
        );

        return redirect()->back()->with('success', 'SEO actualizado correctamente');
    }
}