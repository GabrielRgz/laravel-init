<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return view('pages-page2'); 
    }

    public function getUsers()
    {
        $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at'])->get();

    return response()->json([
        'data' => $users,
        'recordsTotal' => $users->count(),
        'recordsFiltered' => $users->count(), // Esto puede cambiar si estás aplicando filtros
    ]);
    }
}
