<?php

use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\InventarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$controller_path = 'App\Http\Controllers';

// Main Page Route

// pages

//Catalogo
Route::get('/catalogos', [CatalogoController::class, 'index'])->name('catalogos.index');
Route::post('/catalogosPost', [CatalogoController::class, 'store'])->name('catalogos.store');
Route::get('/catalogos/data', [CatalogoController::class, 'getCatalogo'])->name('catalogos.data');


//Inventario
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
Route::post('/inventarioPost', [InventarioController::class, 'store'])->name('inventario.store');
Route::get('/inventario/data', [InventarioController::class, 'getInventario'])->name('inventario.data');
Route::delete('/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');


//Usuarios
Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
Route::post('/usuariosPost', [UserController::class, 'store'])->name('usuarios.store');
Route::get('/usuarios/data', [UserController::class, 'getUsers'])->name('usuarios.data');
Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
$controller_path = 'App\Http\Controllers';

    Route::get('/', $controller_path . '\pages\HomePage@index')->name('pages-home');
    Route::get('/page-2', $controller_path . '\pages\Page2@index')->name('pages-page-2');
    Route::get('/page-catalogo', $controller_path . '\pages\catalogoPage@index')->name('pages-page-catalogo');
    Route::get('/page-inventario', $controller_path . '\pages\inventarioPage@index')->name('pages-page-inventario');
});
