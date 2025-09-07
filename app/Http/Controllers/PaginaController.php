<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PaginaController extends Controller
{
    public function editExperiencias(): View
    {
        return view('admin.paginas.experiencias');
    }

    public function editSerChef(): View
    {
        return view('admin.paginas.ser-chef');
    }

    public function editComoFunciona(): View
    {
        return view('admin.paginas.como-funciona');
    }
}