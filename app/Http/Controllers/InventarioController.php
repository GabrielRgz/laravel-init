<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Catalogo;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Mostrar el listado de inventarios.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages-inventarios');
    }

    /**
     * Mostrar el formulario para crear un nuevo inventario.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function create()
    {
        $catalogos = Catalogo::all(); // Obtener todas las categorías de herramientas/consumibles
        return view('inventarios.create', compact('catalogos'));
    }*/

    public function getInventario()
    {
        // Obtenemos los datos del inventario junto con la relación catalogo
        $inventarios = Inventario::with('catalogo')  // 'catalogo' es el nombre de la relación en el modelo
            ->select(['id', 'catalogo_id', 'cantidad_stock', 'ubicacion', 'created_at', 'updated_at'])
            ->get();

        // Modificamos el array de datos para incluir el nombre del catálogo
        $inventarios->transform(function ($inventario) {
            $inventario->catalogo_name = $inventario->catalogo->name; 
            $inventario->descripcion = $inventario->catalogo->descripcion; // Accedemos al nombre de la categoría (catalogo)
            return $inventario;
        });

        // Retornamos la respuesta con los datos
        return response()->json([
            'data' => $inventarios,
            'recordsTotal' => $inventarios->count(),
            'recordsFiltered' => $inventarios->count(),
        ]);
    }


    /**
     * Almacenar un nuevo inventario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos del formulario
        $validated = $request->validate([
            'catalogo_id' => 'required|exists:catalogos,id', // Verificar que el ID del catálogo existe
            'cantidad_stock' => 'required|integer|min:0', // Asegurarse de que la cantidad es un número entero positivo
            'ubicacion' => 'required|string|max:255', // Verificar que la ubicación es una cadena de texto válida
        ]);

        // Crear un nuevo registro de inventario
        $inventario = new Inventario();
        $inventario->catalogo_id = $validated['catalogo_id']; // Asignar el ID del catálogo
        $inventario->cantidad_stock = $validated['cantidad_stock']; // Asignar la cantidad de stock
        $inventario->ubicacion = $validated['ubicacion']; // Asignar la ubicación

        // Guardar el inventario en la base de datos
        $inventario->save();

        // Responder con un mensaje de éxito en formato JSON
        return response()->json([
            'success' => 'Nuevo inventario creado con éxito.',
        ]);
    }



    /**
     * Mostrar el formulario para editar el inventario.
     *
     * @param  \App\Models\Inventario  $inventario
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventario $inventario)
    {
        $catalogos = Catalogo::all(); // Obtener todas las categorías de herramientas/consumibles
        return view('inventarios.edit', compact('inventario', 'catalogos'));
    }

    /**
     * Actualizar un inventario existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventario  $inventario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'catalogoId' => 'required|exists:catalogos,id',
            'cantidadStock' => 'required|integer|min:0',
            'ubicacion' => 'required|string|max:255',
        ]);
    
        $registro = Inventario::findOrFail($id);
        $registro->catalogo_id = $request->catalogoId;
        $registro->cantidad_stock = $request->cantidadStock;
        $registro->ubicacion = $request->ubicacion;
        $registro->save();
    
        return response()->json(['success' => 'Registro actualizado correctamente.']);
    }
    


    /**
     * Eliminar un inventario.
     *
     * @param  \App\Models\Inventario  $inventario
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->delete();

        return response()->json(['message' => 'Registro eliminado correctamente']);
    }
}
