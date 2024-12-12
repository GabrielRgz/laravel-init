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
        $catalogos = Catalogo::select(['id', 'name', 'partida', 'created_at', 'updated_at'])->get();

        return response()->json([
            'data' => $catalogos,
            'recordsTotal' => $catalogos->count(),
            'recordsFiltered' => $catalogos->count(), // Esto puede cambiar si estás aplicando filtros
        ]);
    }

    // Mostrar un catálogo específico (para editar)
    public function show($id)
    {
        $catalogo = Catalogo::findOrFail($id); // Busca el registro por ID

        return response()->json($catalogo); // Devuelve los datos como JSON
    }

    // Actualizar un catálogo específico
    public function update(Request $request, $id)
    {
        $request->validate([
            'categoryName' => 'required|string|max:255',
            'partida' => 'required|string|max:50',
        ]);

        $catalogo = Catalogo::findOrFail($id); // Encuentra el catálogo por su ID
        $catalogo->name = $request->categoryName; // Actualiza el nombre
        $catalogo->partida = $request->partida; // Actualiza la partida
        $catalogo->save(); // Guarda los cambios

        return response()->json(['success' => 'Registro actualizado correctamente']);
    }

    public function store(Request $request)
    {

        // Validación de los datos entrantes
        $request->validate([
            'categoryName' => 'required|string|max:255',
            'partida' => 'required|string|max:255',
        ]);
        $this_partida = (int) $request->partida;
        // Crear un nuevo registro en la tabla catalogos
        Catalogo::create([
            'name' => $request->categoryName,
            'partida' => $this_partida,
            'created_at' => now(), // También puedes usar $request->createdAt si deseas establecerlo
            'updated_at' => now(), // Igual que arriba
        ]);

        return response()->json(['success' => 'Registro guardado correctamente']);
    }

    public function destroy($id)
    {
        $user = Catalogo::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Registro eliminado correctamente']);
    }
}
