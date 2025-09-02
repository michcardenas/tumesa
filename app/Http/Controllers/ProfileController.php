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
        $hasChefRole = false;
        
        if ($user->role === 'chef_anfitrion') {
            $hasChefRole = true;
        } elseif (method_exists($user, 'hasRole')) {
            if ($user->hasRole('chef') || $user->hasRole('chef_anfitrion')) {
                $hasChefRole = true;
            }
        }
        
        if (!$hasChefRole) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Validación
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ]);

        // Datos a actualizar
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'user' => $user
            ]);
        }

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

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'nullable|string|max:255',
        'bio' => 'nullable|string',
    ]);

    $user->update($data);

    return redirect()->route('perfil.comensal')->with('success', 'Perfil actualizado correctamente.');
}

}
