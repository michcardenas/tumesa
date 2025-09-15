<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cena;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminCenasController extends Controller
{
    /**
     * Display a listing of all cenas for admin management.
     */
    public function index()
    {
        $cenas = Cena::with(['user'])
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('admin.cenas.index', compact('cenas'));
    }

    /**
     * Show the form for creating a new cena.
     */
    public function create()
    {
        $chefs = User::role('chef_anfitrion')->orderBy('name')->get();
        
        return view('admin.cenas.create', compact('chefs'));
    }

    /**
     * Store a newly created cena in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'datetime' => 'required|date|after:now',
            'guests_max' => 'required|integer|min:1|max:50',
            'price' => 'required|numeric|min:0',
            'menu' => 'required|string',
            'location' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:draft,published,cancelled',
            'is_active' => 'boolean',
            'special_requirements' => 'nullable|string',
            'cancellation_policy' => 'nullable|string'
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('cenas/covers', 'public');
        }

        // Handle gallery images upload
        $galleryImages = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $galleryImages[] = $image->store('cenas/gallery', 'public');
            }
        }
        $validated['gallery_images'] = $galleryImages;

        // Set default values
        $validated['guests_current'] = 0;
        $validated['is_active'] = 1; // o true, ambos funcionan


        $cena = Cena::create($validated);

return redirect()->route('admin.cenas')

                        ->with('success', 'Cena creada exitosamente.');
    }

    /**
     * Show the form for editing the specified cena.
     */
    public function edit(Cena $cena)
    {
        $chefs = User::role('chef_anfitrion')->orderBy('name')->get();
        
        return view('admin.cenas.edit', compact('cena', 'chefs'));
    }
public function update(Request $request, Cena $cena) 
{
    // LOG 1: Ver qué llega del formulario
    \Log::info('=== UPDATE CENA ID: ' . $cena->id . ' ===');
    \Log::info('Coordenadas recibidas del formulario:', [
        'latitude' => $request->input('latitude'),
        'longitude' => $request->input('longitude')
    ]);
    
    // LOG 2: Ver valores actuales
    \Log::info('Coordenadas actuales en BD:', [
        'latitude' => $cena->latitude,
        'longitude' => $cena->longitude
    ]);
    
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'title' => 'required|string|max:255',
        'datetime' => 'required|date|after:now',
        'guests_max' => 'required|integer|min:1|max:50',
        'price' => 'required|numeric|min:0',
        'menu' => 'required|string',
        'location' => 'required|string|max:500',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'status' => 'required|in:draft,published,cancelled',
        'is_active' => 'boolean',
        'special_requirements' => 'nullable|string',
        'cancellation_policy' => 'nullable|string'
    ]);

    // Handle cover image upload
    if ($request->hasFile('cover_image')) {
        if ($cena->cover_image) {
            Storage::disk('public')->delete($cena->cover_image);
        }
        $validated['cover_image'] = $request->file('cover_image')->store('cenas/covers', 'public');
    }

    // Handle gallery images upload
    if ($request->hasFile('gallery_images')) {
        if ($cena->gallery_images) {
            foreach ($cena->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $galleryImages = [];
        foreach ($request->file('gallery_images') as $image) {
            $galleryImages[] = $image->store('cenas/gallery', 'public');
        }
        $validated['gallery_images'] = $galleryImages;
    }

    // Set boolean value
    $validated['is_active'] = $request->has('is_active');

    // LOG 3: Ver datos validados
    \Log::info('Coordenadas después de validación:', [
        'latitude' => $validated['latitude'] ?? 'null',
        'longitude' => $validated['longitude'] ?? 'null'
    ]);

    // ===== SOLUCIÓN: Separar coordenadas y usar DB directo =====
    
    // Guardar coordenadas aparte
    $latitudeToUpdate = isset($validated['latitude']) ? $validated['latitude'] : null;
    $longitudeToUpdate = isset($validated['longitude']) ? $validated['longitude'] : null;
    
    // Remover coordenadas del array para evitar problemas con el cast
    unset($validated['latitude']);
    unset($validated['longitude']);
    
    // Actualizar todo excepto coordenadas
    $cena->update($validated);
    \Log::info('Otros campos actualizados correctamente');
    
    // Actualizar coordenadas con DB directo (SIEMPRE FUNCIONA)
    if ($latitudeToUpdate !== null && $longitudeToUpdate !== null) {
        $affected = DB::table('cenas')
            ->where('id', $cena->id)
            ->update([
                'latitude' => $latitudeToUpdate,
                'longitude' => $longitudeToUpdate
            ]);
        
        \Log::info('Actualización de coordenadas con DB directo:', [
            'filas_afectadas' => $affected,
            'latitude_nueva' => $latitudeToUpdate,
            'longitude_nueva' => $longitudeToUpdate
        ]);
    }
    
    // LOG 4: Verificar resultado final
    $cena->refresh();
    \Log::info('Coordenadas finales después de actualizar:', [
        'latitude' => $cena->latitude,
        'longitude' => $cena->longitude,
        'actualización_exitosa' => ($cena->latitude == $latitudeToUpdate && $cena->longitude == $longitudeToUpdate) ? 'SI' : 'NO'
    ]);
    
    \Log::info('=== FIN UPDATE CENA ===');

    return redirect()->route('admin.cenas')
                    ->with('success', 'Cena actualizada exitosamente.');
}
    /**
     * Remove the specified cena from storage.
     */
    public function destroy(Cena $cena)
    {
        // Delete associated images
        if ($cena->cover_image) {
            Storage::disk('public')->delete($cena->cover_image);
        }
        
        if ($cena->gallery_images) {
            foreach ($cena->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $cena->delete();

        return redirect()->route('admin.cenas')
                        ->with('success', 'Cena eliminada exitosamente.');
    }
}
