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
    \Log::info('=== UPDATE CENA ID: '.$cena->id.' ===', $request->only('latitude','longitude'));

    $validated = $request->validate([
        'user_id'    => 'required|exists:users,id',
        'title'      => 'required|string|max:255',
        'datetime'   => 'required|date|after:now',
        'guests_max' => 'required|integer|min:1|max:50',
        'price'      => 'required|numeric|min:0',
        'menu'       => 'required|string',
        'location'   => 'required|string|max:500',
        // Fuerza coords válidas si siempre usas mapa:
        'latitude'   => 'required|numeric|between:-90,90',
        'longitude'  => 'required|numeric|between:-180,180',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'status'     => 'required|in:draft,published,cancelled',
        'is_active'  => 'boolean',
        'special_requirements' => 'nullable|string',
        'cancellation_policy'  => 'nullable|string',
    ]);

    // Imágenes (igual que ya tenías)
    if ($request->hasFile('cover_image')) {
        if ($cena->cover_image) \Storage::disk('public')->delete($cena->cover_image);
        $validated['cover_image'] = $request->file('cover_image')->store('cenas/covers', 'public');
    }
    if ($request->hasFile('gallery_images')) {
        if (is_array($cena->gallery_images)) {
            foreach ($cena->gallery_images as $img) \Storage::disk('public')->delete($img);
        }
        $validated['gallery_images'] = collect($request->file('gallery_images'))
            ->map(fn($img) => $img->store('cenas/gallery', 'public'))
            ->all();
    }

    // Booleano
    $validated['is_active'] = $request->boolean('is_active');

    // Normaliza coords a number (evita strings vacíos)
    $validated['latitude']  = (float) $validated['latitude'];
    $validated['longitude'] = (float) $validated['longitude'];

    // (Opcional) mira qué cambiará antes de guardar
    $cena->fill($validated);
    \Log::info('Dirty antes de save():', $cena->getDirty());

    $cena->save();

    \Log::info('Guardado OK', [
        'lat_final' => $cena->latitude,
        'lng_final' => $cena->longitude,
    ]);

    return redirect()->route('admin.cenas')->with('success', 'Cena actualizada exitosamente.');
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
