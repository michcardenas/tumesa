<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
   public function edit()
    {
        $user = Auth::user();
        
        // Verificación de permisos para chefs
        $hasChefRole = false;
        
        if ($user->role === 'chef_anfitrion') {
            $hasChefRole = true;
        } elseif (method_exists($user, 'hasRole')) {
            if ($user->hasRole('chef') || $user->hasRole('chef_anfitrion')) {
                $hasChefRole = true;
            }
        }
        
        if (!$hasChefRole) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        return view('chef.profile.edit', compact('user'));
    }

   public function update(Request $request)
{
    $user = Auth::user();
    
    // Validar permisos
    if ($user->role !== 'chef_anfitrion') {
        return redirect()->back()->with('error', 'No autorizado');
    }

    // Validación
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'telefono' => ['nullable', 'string', 'max:20'],
        'direccion' => ['nullable', 'string', 'max:255'],
        'bio' => ['nullable', 'string', 'max:1000'],
        'especialidad' => ['nullable', 'string', 'max:255'],
        'experiencia_anos' => ['nullable', 'integer', 'min:0', 'max:50'],
        'instagram' => ['nullable', 'string', 'max:255'],
        'facebook' => ['nullable', 'string', 'max:255'],
        'website' => ['nullable', 'url', 'max:255'],
        'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
    ]);

    // Datos a actualizar
    $updateData = [
        'name' => $request->name,
        'email' => $request->email,
        'telefono' => $request->telefono,
        'direccion' => $request->direccion,
        'bio' => $request->bio,
        'especialidad' => $request->especialidad,
        'experiencia_anos' => $request->experiencia_anos,
        'instagram' => $request->instagram,
        'facebook' => $request->facebook,
        'website' => $request->website,
    ];

    // Actualizar contraseña si se proporciona
    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    // Manejar subida de avatar
    if ($request->hasFile('avatar')) {
        // Eliminar avatar anterior si existe
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Subir nuevo avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $updateData['avatar'] = $avatarPath;
    }

    // Actualizar usuario
    $user->update($updateData);

    return redirect()->route('chef.profile.edit')
        ->with('success', 'Perfil actualizado exitosamente');
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }


       public function perfilComensal()
    {
        // Trae al usuario autenticado
        $user = Auth::user();

        // Retorna la vista de edición de perfil del comensal
        return view('comensal.perfil', compact('user'));
    }
public function updatecomensal(Request $request)
{
    $user = Auth::user();
    
    // Verificar si es actualización de contraseña
    if ($request->input('update_type') === 'password') {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('perfil.comensal.edit')->with('success', 'Contraseña actualizada correctamente.');
    }
    
    // Actualización de perfil normal
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'telefono' => 'nullable|string|max:20',
        'bio' => 'nullable|string',
        // Avatar
        'avatar_url' => 'nullable|url',
        'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    // Manejar avatar
    if ($request->filled('avatar_url')) {
        $data['avatar'] = $request->avatar_url;
    } elseif ($request->hasFile('avatar_file')) {
        // Eliminar avatar anterior si existe y no es URL
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $data['avatar'] = $request->file('avatar_file')->store('avatars', 'public');
    }

    $user->update($data);

    return redirect()->route('perfil.comensal')->with('success', 'Perfil actualizado correctamente.');
}

}
