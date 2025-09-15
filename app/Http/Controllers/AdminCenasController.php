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

    // ========================================================
    // SOLUCIÓN ROBUSTA PARA COORDENADAS
    // ========================================================
    
    // Paso 1: Extraer las coordenadas
    $latitude = null;
    $longitude = null;
    
    if (isset($validated['latitude']) && $validated['latitude'] !== '') {
        $latitude = (float) $validated['latitude'];
    }
    if (isset($validated['longitude']) && $validated['longitude'] !== '') {
        $longitude = (float) $validated['longitude'];
    }
    
    // Paso 2: Remover coordenadas del array principal
    unset($validated['latitude']);
    unset($validated['longitude']);
    
    // Paso 3: Actualizar todos los campos EXCEPTO las coordenadas
    $cena->update($validated);
    
    // Paso 4: Actualizar las coordenadas usando DB::table (MÉTODO MÁS CONFIABLE)
    if ($latitude !== null && $longitude !== null) {
        try {
            // Este método SIEMPRE funciona, independientemente del cast en el modelo
            DB::table('cenas')
                ->where('id', $cena->id)
                ->update([
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]);
                
            // Log de éxito (puedes quitarlo después de verificar que funciona)
            Log::info('Coordenadas actualizadas exitosamente', [
                'cena_id' => $cena->id,
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            
        } catch (\Exception $e) {
            // Si hay algún error, lo registramos
            Log::error('Error actualizando coordenadas', [
                'cena_id' => $cena->id,
                'error' => $e->getMessage()
            ]);
            
            // Opcionalmente, puedes mostrar el error al usuario
            return redirect()->route('admin.cenas')
                            ->with('error', 'La cena se actualizó pero hubo un problema con las coordenadas.');
        }
    }

    return redirect()->route('admin.cenas')
                    ->with('success', 'Cena actualizada exitosamente.');
}

// ========================================================
// MÉTODO AUXILIAR PARA VERIFICAR (OPCIONAL)
// Puedes agregar este método a tu controlador para verificar
// ========================================================

public function verifyCoordinates($cenaId)
{
    $cena = Cena::find($cenaId);
    $dbValues = DB::table('cenas')
                  ->where('id', $cenaId)
                  ->select('latitude', 'longitude')
                  ->first();
    
    return [
        'model_values' => [
            'latitude' => $cena->latitude,
            'longitude' => $cena->longitude
        ],
        'db_values' => [
            'latitude' => $dbValues->latitude,
            'longitude' => $dbValues->longitude
        ],
        'match' => $cena->latitude == $dbValues->latitude && 
                   $cena->longitude == $dbValues->longitude
    ];
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
