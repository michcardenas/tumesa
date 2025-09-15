<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cena;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


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
    try {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'datetime' => 'required|date|after:now',
            'guests_max' => 'required|integer|min:1|max:50',
            'price' => 'required|numeric|min:0',
            'menu' => 'required|string',
            'location' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',  // Cambiado a required
            'longitude' => 'required|numeric|between:-180,180', // Cambiado a required
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:draft,published,cancelled',
            'special_requirements' => 'nullable|string',
            'cancellation_policy' => 'nullable|string'
        ]);

        // Manejar is_active correctamente
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Manejar imagen de portada
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('cenas/covers', 'public');
        }

        // Manejar galería de imágenes
        $galleryImages = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $galleryImages[] = $image->store('cenas/gallery', 'public');
            }
            $validated['gallery_images'] = $galleryImages;
        } else {
            $validated['gallery_images'] = []; // Array vacío si no hay imágenes
        }

        // Establecer valores por defecto
        $validated['guests_current'] = 0;

        // Crear la cena
        $cena = Cena::create($validated);

        return redirect()->route('admin.cenas')
                        ->with('success', 'Cena creada exitosamente.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
                        ->withErrors($e->validator)
                        ->withInput();
    } catch (\Exception $e) {
        // Log del error para debugging
        \Log::error('Error creando cena: ' . $e->getMessage());
        
        return redirect()->back()
                        ->with('error', 'Error al crear la cena: ' . $e->getMessage())
                        ->withInput();
    }
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
    \Log::info('=== UPDATE CENA ID: '.$cena->id.' ===', $request->only('latitude','longitude','datetime','status'));

    // Regla base
    $rules = [
        'user_id'    => 'required|exists:users,id',
        'title'      => 'required|string|max:255',
        'datetime'   => ['required','date'], // base sin "after:now"
        'guests_max' => 'required|integer|min:1|max:50',
        'price'      => 'required|numeric|min:0',
        'menu'       => 'required|string',
        'location'   => 'required|string|max:500',
        'latitude'   => 'required|numeric|between:-90,90',
        'longitude'  => 'required|numeric|between:-180,180',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'status'     => ['required', Rule::in(['draft','published','cancelled'])],
        'special_requirements' => 'nullable|string',
        'cancellation_policy'  => 'nullable|string',
    ];

    // Solo exigir futuro si se va a PUBLICAR (o si quieres: cuando cambian la fecha)
    $willPublish = $request->input('status') === 'published';
    if ($willPublish) {
        $rules['datetime'][] = 'after:now';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        \Log::warning('Validación falló en UPDATE CENA', [
            'errors' => $validator->errors()->toArray(),
        ]);
        return back()->withErrors($validator)->withInput();
    }

    $validated = $validator->validated();

    // Normaliza EXACTO a 7 decimales (coherente con DECIMAL(10,7))
    $validated['latitude']  = number_format((float)$validated['latitude'],  7, '.', '');
    $validated['longitude'] = number_format((float)$validated['longitude'], 7, '.', '');

    // Imágenes
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


    $cena->fill($validated);
    \Log::info('Dirty antes de save():', $cena->getDirty());

    $cena->save();

    $cena->refresh();
    \Log::info('Guardado OK', [
        'lat_final' => $cena->latitude,
        'lng_final' => $cena->longitude,
        'datetime'  => (string) $cena->datetime,
        'status'    => $cena->status,
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
