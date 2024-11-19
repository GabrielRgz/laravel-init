<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use Illuminate\Http\Request;

class inventarioPage extends Controller
{
  public function index()
  {
    $catalogos = Catalogo::all(); // Obtener todas las categorías de herramientas/consumibles
        return view('content.pages.pages-inventarios', compact('catalogos'));
    //return view('content.pages.pages-inventarios');
  }
}
