<?php

namespace App\Http\Controllers;
use App\Models\Cena;
use App\Models\Reserva;
use App\Models\Reseña;
use Illuminate\Http\Request;


class ResenaController extends Controller
{

    
public function create(Cena $cena, Reserva $reserva)
{
    return view('comensal.resenas.create', compact('cena', 'reserva'));
}

public function store(Request $request)
{
    $data = $request->validate([
        'id_cena' => 'required|exists:cenas,id',
        'id_reserva' => 'required|exists:reservas,id',
        'id_user' => 'required|exists:users,id',
        'rating' => 'required|integer|min:1|max:5',
        'comentario' => 'nullable|string|max:500',
    ]);

    \App\Models\Reseña::create($data);

    return redirect()->route('comensal.dashboard')
        ->with('success', '¡Gracias por tu reseña!');
}


public function index()
{
    $chef = auth()->user(); // Chef autenticado
    
    // Obtener reseñas de las cenas del chef
    $resenas = Reseña::whereHas('cena', function($query) use ($chef) {
        $query->where('user_id', $chef->id); // Cambiar chef_id por user_id
    })
    ->with(['cena', 'user', 'reserva'])
    ->orderBy('created_at', 'desc')
    ->get();
    
    return view('chef.resenas', compact('resenas'));
}

}