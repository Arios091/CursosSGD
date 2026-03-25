<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::get('/home', [App\Http\Controllers\CursoController::class, 'index'])->name('home');

Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');

Route::middleware(['auth'])->group(function () {
    // Crear curso - solo admin
    Route::get('/crear.curso', function () { 
        if (auth()->user()->role !== 'admin') {
            abort(403, 'No tienes permiso para crear cursos.');
        }
        return view('cursos.create-livewire'); 
    })->name('crear.curso');
    
    // Editar curso - solo admin
    Route::get('/cursos/{curso}/editar', [CursoController::class, 'editLivewire'])->name('cursos.edit')->middleware('can:update,curso');
    
    // Eliminar curso - solo admin
    Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy')->middleware('can:delete,curso');
});

Route::post('/cursos/{curso}/comenzar', [CursoController::class, 'comenzar'])->name('cursos.comenzar');

Route::post('/cursos/{curso}/reiniciar', [CursoController::class, 'reiniciarProgreso'])->middleware('auth')->name('cursos.reiniciar');

Route::get('/mis-cursos/{curso}', [CursoController::class, 'verCurso'])->middleware('auth')->name('cursos.ver');

Route::post('/mis-cursos/{curso}/material', [CursoController::class, 'marcarMaterial'])->middleware('auth')->name('cursos.material');

Route::get('/mis-cursos/{curso}/modulo/{modulo}/cuestionario', [CursoController::class, 'verCuestionario'])->middleware('auth')->name('cursos.cuestionario.ver');

Route::post('/mis-cursos/{curso}/modulo/{modulo}/cuestionario', [CursoController::class, 'enviarCuestionario'])->middleware('auth')->name('cursos.cuestionario');

Route::post('/mis-cursos/{curso}/evaluacion', [CursoController::class, 'enviarEvaluacion'])->middleware('auth')->name('cursos.evaluacion');
