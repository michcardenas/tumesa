<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cena;

class AdminCenasController extends Controller
{
    /**
     * Display a listing of all cenas for admin management.
     */
    public function index()
    {
        // Obtener todas las cenas con relaciones
        $cenas = Cena::with(['user'])
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('admin.cenas.index', compact('cenas'));
    }
}