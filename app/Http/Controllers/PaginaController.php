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

public function updateComoFunciona(Request $request)
{
    $pagina_id = 'como-funciona';
    
    // Lista de todos los campos editables
    $campos = [
        'hero_titulo', 'hero_descripcion', 'hero_boton1', 'hero_boton2',
        'tabs_titulo', 'tabs_subtitulo', 'tab1_texto', 'tab2_texto',
        
        // Invitados
        'guest_paso1_titulo', 'guest_paso1_descripcion', 'guest_paso2_titulo', 'guest_paso2_descripcion',
        'guest_paso3_titulo', 'guest_paso3_descripcion', 'guest_paso4_titulo', 'guest_paso4_descripcion',
        'guest_timeline_titulo', 'guest_timeline_subtitulo',
        'guest_timeline1_titulo', 'guest_timeline1_descripcion', 'guest_timeline2_titulo', 'guest_timeline2_descripcion',
        'guest_timeline3_titulo', 'guest_timeline3_descripcion', 'guest_timeline4_titulo', 'guest_timeline4_descripcion',
        'guest_timeline5_titulo', 'guest_timeline5_descripcion',
        
        // Chefs
        'chef_paso1_titulo', 'chef_paso1_descripcion', 'chef_paso2_titulo', 'chef_paso2_descripcion',
        'chef_paso3_titulo', 'chef_paso3_descripcion', 'chef_paso4_titulo', 'chef_paso4_descripcion',
        'chef_timeline_titulo', 'chef_timeline_subtitulo',
        'chef_timeline1_titulo', 'chef_timeline1_descripcion', 'chef_timeline2_titulo', 'chef_timeline2_descripcion',
        'chef_timeline3_titulo', 'chef_timeline3_descripcion', 'chef_timeline4_titulo', 'chef_timeline4_descripcion',
        'chef_timeline5_titulo', 'chef_timeline5_descripcion',
        
        // Pagos
        'pagos_titulo', 'pagos_subtitulo',
        'pagos_card1_titulo', 'pagos_card1_descripcion', 'pagos_card1_badge', 'pagos_card1_badge_texto',
        'pagos_card2_titulo', 'pagos_card2_descripcion', 'pagos_card2_badge', 'pagos_card2_badge_texto',
        'pagos_card3_titulo', 'pagos_card3_descripcion', 'pagos_card3_badge', 'pagos_card3_badge_texto',
        
        // FAQ
        'faq_titulo', 'faq1_pregunta', 'faq1_respuesta', 'faq2_pregunta', 'faq2_respuesta',
        'faq3_pregunta', 'faq3_respuesta', 'faq4_pregunta', 'faq4_respuesta',
        
        // CTA
        'cta_boton1', 'cta_boton2', 'cta_texto_ayuda'
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