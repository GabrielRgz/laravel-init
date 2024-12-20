<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Inventario;
use App\Models\Catalogo;
use Illuminate\Http\Request;

class PrestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages-prestamos');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrestamos()
    {
        $prest = Prestamo::where('fecha_limite', '<', now())->where('status', '!=', 'atrasado')->update(['status' => 'atrasado']);

        $prestamos = Prestamo::with(['herramienta', 'emisor', 'receptor'])->get();

        return response()->json([
            'data' => $prestamos,
            'recordsTotal' => $prestamos->count(),
            'recordsFiltered' => $prestamos->count(), // Esto puede variar si aplicas filtros en el futuro
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emisorId' => 'required|exists:users,id',
            'receptorId' => 'required|exists:users,id',
            'herramientaId' => 'required|exists:inventarios,id',
            'cantidad' => 'required|integer',
            'fecha_inicio' => 'required|date',
            'fecha_limite' => 'required|date',
        ]);

        // Crear el préstamo
        Prestamo::create([
            'emisor_id' => $validated['emisorId'],
            'receptor_id' => $validated['receptorId'],
            'herramienta_id' => $validated['herramientaId'],
            'cantidad' => $validated['cantidad'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_limite' => $validated['fecha_limite'],
            'comentarios' => $request->comentarios,
        ]);

        $inventario = Inventario::find($validated['herramientaId']);

        if (!$inventario->restarStock($validated['cantidad'])) {
            return response()->json(['error' => 'Stock insuficiente para realizar el préstamo'], 400);
        }

        return response()->json([
            'success' => 'Nuevo prestamo creado con éxito.',
        ]);
        //return redirect()->route('prestamos.index')->with('success', 'Préstamo creado con éxito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $prestamo = Prestamo::findOrFail($id);  // Busca el préstamo por ID
        return response()->json($prestamo);  // Devuelve los detalles en formato JSON
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Encuentra el préstamo por ID
        $prestamo = Prestamo::findOrFail($id);

        // Validación de los campos
        $validated = $request->validate([
            'emisorId' => 'required|exists:users,id',
            'receptorId' => 'required|exists:users,id',
            'herramientaId' => 'required|exists:inventarios,id',
            'cantidad' => 'required|integer',
            'fecha_inicio' => 'required|date',
            'fecha_limite' => 'required|date',
            'status' => 'required|in:atrasado,devuelto,activo',
        ]);

        // Restaurar stock si el estado cambia a "devuelto"
        if ($validated['status'] === 'devuelto' && $prestamo->status !== 'devuelto') {
            $inventario = Inventario::find($prestamo->herramienta_id);

            if ($inventario) {
                $inventario->cantidad_stock += $prestamo->cantidad; // Incrementar el stock
                $inventario->save(); // Guardar los cambios en el inventario
            } else {
                return response()->json(['error' => 'Inventario no encontrado para la herramienta'], 404);
            }
        }

        // Restar stock si el estado cambia a "activo"
        if ($validated['status'] === 'activo' && $prestamo->status === 'devuelto') {
            $inventario = Inventario::find($prestamo->herramienta_id);

            if ($inventario) {
                $inventario->cantidad_stock -= $prestamo->cantidad; // Restar el stock
                $inventario->save(); // Guardar los cambios en el inventario
            } else {
                return response()->json(['error' => 'Inventario no encontrado para la herramienta'], 404);
            }
        }

        // Actualizar el préstamo
        $prestamo->update([
            'emisor_id' => $validated['emisorId'],
            'receptor_id' => $validated['receptorId'],
            'herramienta_id' => $validated['herramientaId'],
            'cantidad' => $validated['cantidad'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_limite' => $validated['fecha_limite'],
            'comentarios' => $request->comentarios,
            'status' => $validated['status'],
        ]);

        return response()->json(['message' => 'Préstamo actualizado correctamente'], 200);
    }
}
