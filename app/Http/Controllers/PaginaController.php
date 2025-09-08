<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Pagina;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaginaController extends Controller
{
    public function editExperiencias(): View
    {
        // Traer todos los contenidos de la página con ID 1 (Experiencias)
        $contenidos = Pagina::porPagina('experiencias')->get();
        
        return view('admin.paginas.experiencias', compact('contenidos'));
    }

    public function editSerChef(): View
    {
        // Traer todos los contenidos de la página con ID 2 (Ser Chef Anfitrión)
        $contenidos = Pagina::porPagina('ser-chef')->get();
        
        return view('admin.paginas.ser-chef', compact('contenidos'));
    }

    public function editComoFunciona(): View
    {
        // Traer todos los contenidos de la página con ID 3 (Cómo Funciona)
        $contenidos = Pagina::porPagina('como-funciona')->get();
        
        return view('admin.paginas.como-funciona', compact('contenidos'));
    }

   public function updateExperiencias(Request $request)
{
    // Validación para la imagen
    $request->validate([
        'hero_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $pagina_id = 'experiencias';
    
    // Lista de campos de texto (SIN hero_imagen)
    $campos = [
        'hero_titulo', 'hero_subtitulo', 'hero_boton1', 'hero_boton2',
        'elegir_titulo', 'elegir_subtitulo',
        'feature1_icono', 'feature1_titulo', 'feature1_descripcion',
        'feature2_icono', 'feature2_titulo', 'feature2_descripcion',
        'feature3_icono', 'feature3_titulo', 'feature3_descripcion',
        'funciona_titulo', 'funciona_subtitulo',
        'paso1_icono', 'paso1_titulo', 'paso1_descripcion',
        'paso2_icono', 'paso2_titulo', 'paso2_descripcion',
        'paso3_icono', 'paso3_titulo', 'paso3_descripcion',
        'paso4_icono', 'paso4_titulo', 'paso4_descripcion',
        'cta_titulo', 'cta_descripcion', 'cta_boton1', 'cta_boton2'
    ];
    
    // Guardar campos de texto
    foreach ($campos as $campo) {
        if ($request->has($campo)) {
            Pagina::updateOrCreate([
                'pagina_id' => $pagina_id,
                'clave' => $campo
            ], [
                'valor' => $request->input($campo)
            ]);
        }
    }
    
    // Manejar imagen hero por separado
    if ($request->hasFile('hero_imagen')) {
        // Obtener imagen anterior para eliminarla
        $imagenAnterior = Pagina::where('pagina_id', $pagina_id)
                               ->where('clave', 'hero_imagen')
                               ->first();
        
        // Eliminar imagen anterior si existe y no es URL externa
        if ($imagenAnterior && $imagenAnterior->valor && 
            !Str::startsWith($imagenAnterior->valor, ['http://', 'https://'])) {
            Storage::disk('public')->delete($imagenAnterior->valor);
        }
        
        // Guardar nueva imagen en storage/app/public/paginas/hero/
        $rutaImagen = $request->file('hero_imagen')->store('paginas/hero', 'public');
        
        // Actualizar en base de datos
        Pagina::updateOrCreate([
            'pagina_id' => $pagina_id,
            'clave' => 'hero_imagen'
        ], [
            'valor' => $rutaImagen
        ]);
    }
    
    return redirect()->back()->with('success', 'Contenido actualizado correctamente');
}


public function updateSerChef(Request $request)
{
    $pagina_id = 'ser-chef';
    
    // Lista de todos los campos editables
    $campos = [
        'hero_titulo', 'hero_descripcion', 'hero_boton',
        'beneficios_titulo',
        'beneficio1_icono', 'beneficio1_titulo', 'beneficio1_descripcion',
        'beneficio2_icono', 'beneficio2_titulo', 'beneficio2_descripcion',
        'beneficio3_icono', 'beneficio3_titulo', 'beneficio3_descripcion',
        'beneficio4_icono', 'beneficio4_titulo', 'beneficio4_descripcion',
        'pasos_titulo', 'pasos_subtitulo',
        'paso1_titulo', 'paso1_descripcion',
        'paso2_titulo', 'paso2_descripcion',
        'paso3_titulo', 'paso3_descripcion',
        'paso4_titulo', 'paso4_descripcion',
        'faq_titulo',
        'faq1_pregunta', 'faq1_respuesta',
        'faq2_pregunta', 'faq2_respuesta',
        'faq3_pregunta', 'faq3_respuesta',
        'faq4_pregunta', 'faq4_respuesta',
        'cta_boton_final'
    ];
    
    foreach ($campos as $campo) {
        if ($request->has($campo)) {
            Pagina::updateOrCreate([
                'pagina_id' => $pagina_id,
                'clave' => $campo
            ], [
                'valor' => $request->input($campo)
            ]);
        }
    }
    
    return redirect()->back()->with('success', 'Contenido actualizado correctamente');
}
}