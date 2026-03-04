<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;

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


Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/home', [App\Http\Controllers\CursoController::class, 'index'])->name('home');


Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');
// crear curso (muestra formulario)
Route::get('/cursos/crear', [CursoController::class, 'create'])->name('cursos.create');
// Editar curso (muestra formulario)
Route::get('/cursos/{curso}/editar', [CursoController::class, 'edit'])->name('cursos.edit');

// Actualizar curso (guardar cambios)
Route::put('/cursos/{curso}', [CursoController::class, 'update'])->name('cursos.update');

// Eliminar curso
Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy');

//Comenzar curso (inscribirse y crear progreso)
Route::post('/cursos/{curso}/comenzar', [CursoController::class, 'comenzar'])->name('cursos.comenzar');