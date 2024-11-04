<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catalogo;

class CatalogoController extends Controller
{
    public function index()
    {
        return view('pages-catalogoPage'); 
    }

    public function getCatalogo()
    {
        $catalogos = Catalogo::select(['id', 'name', 'partida', 'descripcion','created_at', 'updated_at'])->get();

    return response()->json([
        'data' => $catalogos,
        'recordsTotal' => $catalogos->count(),
        'recordsFiltered' => $catalogos->count(), // Esto puede cambiar si estás aplicando filtros
    ]);
    }

    public function store(Request $request)
    {
        // Validación de los datos entrantes
        $request->validate([
            'categoryName' => 'required|string|max:255',
            'partida' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ]);

        // Crear un nuevo registro en la tabla catalogos
        Catalogo::create([
            'name' => $request->categoryName,
            'partida' => $request->partida,
            'descripcion' => $request->descripcion,
            'created_at' => now(), // También puedes usar $request->createdAt si deseas establecerlo
            'updated_at' => now(), // Igual que arriba
        ]);

        return response()->json(['success' => 'Registro guardado correctamente']);
    }
}
