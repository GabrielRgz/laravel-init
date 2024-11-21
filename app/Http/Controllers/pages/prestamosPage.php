<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Prestamo;
use App\Models\User;
use App\Models\Inventario;
use Illuminate\Http\Request;

class prestamosPage extends Controller
{
  public function index()
  {
        $prestamos = Prestamo::with(['herramienta', 'emisor', 'receptor'])->get(); // Relacionar tablas
        $usuarios = User::all();
        $herramientas = Inventario::all();
        return view('content.pages.pages-prestamos', compact('prestamos','usuarios', 'herramientas'));
    //return view('content.pages.pages-prestamos');
  }
}
