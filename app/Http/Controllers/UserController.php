<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('pages-page2');
    }

    /*public function getUsers()
    {
        $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at'])->get();

        return response()->json([
            'data' => $users,
            'recordsTotal' => $users->count(),
            'recordsFiltered' => $users->count(), // Esto puede cambiar si estás aplicando filtros
        ]);
    }*/
    public function getUsers()
    {
        // Cargar los usuarios con los roles asociados
        $users = User::with('roles')->select(['id', 'clave', 'name', 'email', 'created_at', 'updated_at'])->get();

        // Mapear los usuarios y agregar el nombre del rol
        $users = $users->map(function ($user) {
            // Extraer el nombre del rol (suponiendo que cada usuario tiene un solo rol)
            $roleName = $user->roles->pluck('name')->first();  // Obtiene el nombre del primer rol asignado

            return [
                'id' => $user->id,
                'clave' => $user->clave,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'rol' => $roleName  // Aquí asignamos el nombre del rol
            ];
        });
        return response()->json([
            'data' => $users,
            'recordsTotal' => $users->count(),
            'recordsFiltered' => $users->count(), // Esto puede cambiar si estás aplicando filtros
        ]);
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id); 
        return response()->json($user);  // Devuelve los datos del usuario en formato JSON
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave' => 'required|integer|min:0',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|string|exists:roles,name', // Validar que el rol exista
        ]);

        $user = User::create([
            'clave' => $request->clave,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Asignar rol al usuario
        $user->assignRole($request->rol);

        return response()->json(['success' => true, 'message' => 'Usuario creado correctamente']);
    }

    // En UserController.php
    public function update(Request $request, $id)
    {
        // Encuentra el usuario por ID
        $user = User::findOrFail($id);

        // Validación de los campos
        $request->validate([
            'clave' => 'required|integer|min:0',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rol' => 'required|in:admin,writer', // Validación del rol
        ]);

        // Actualizar los datos
        $user->clave = $request->clave;
        $user->name = $request->name;
        $user->email = $request->email;

        // Sincronizar el rol (puedes usar assignRole si solo quieres asignar un rol único)
        $user->syncRoles([$request->rol]);

        $user->save();

        return response()->json(['message' => 'Usuario actualizado correctamente'], 201);
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
