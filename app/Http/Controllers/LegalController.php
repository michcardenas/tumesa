<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function terminos()
    {
        return view('legal.terminos');
    }
    public function privacidad()
{
    return view('legal.privacidad');
}
}