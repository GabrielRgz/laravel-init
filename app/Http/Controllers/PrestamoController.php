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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
