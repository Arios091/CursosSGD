<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PageSettingsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Rutas de autenticación
Route::get('/login', '\App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', '\App\Http\Controllers\Auth\LoginController@login');
Route::post('/logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');

Route::get('/register', '\App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', '\App\Http\Controllers\Auth\RegisterController@register');

// 🔴 RUTA TEMPORAL - Eliminar después de crear el admin global
// Accede a: https://sistemasdemo.unas.edu.pe/setup-admin/UNAS2026Admin
Route::get('/setup-admin/{token}', function($token) {
    if ($token !== 'UNAS2026Admin') {
        abort(404);
    }
    
    if (\App\Models\User::where('role', 'admin_global')->exists()) {
        return response()->json(['message' => 'Ya existe un administrador global. Ruta desactivada.', 'success' => false]);
    }

    $email = 'admin@unas.edu.pe';
    $password = 'AdminUNAS2026#';

    $user = \App\Models\User::create([
        'name' => 'Administrador Global',
        'primer_nombre' => 'Administrador',
        'segundo_nombre' => '',
        'primer_apellido' => 'Global',
        'segundo_apellido' => 'UNAS',
        'email' => $email,
        'password' => \Illuminate\Support\Facades\Hash::make($password),
        'role' => 'admin_global',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Admin global creado exitosamente',
        'email' => $email,
        'password' => $password,
        'warning' => '⚠️ ELIMINA ESTA RUTA DEL ARCHIVO routes/web.php DESPUÉS DE USARLA'
    ]);
});
// 🔴 FIN RUTA TEMPORAL

// Rutas de recuperación de contraseña
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/reset', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/verify', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset/verify', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/home', [CursoController::class, 'home'])->name('home');

Route::middleware(['auth'])->group(function () {
    // Perfil
    Route::get('/perfil', function () {
        return view('perfil');
    })->name('perfil');
    
    // Certificado de curso completado
    Route::get('/certificado/{curso}', [CursoController::class, 'verCertificado'])->name('certificado');
    Route::get('/certificado/{curso}/ver', [CursoController::class, 'verCertificadoView'])->name('certificado.ver');
    Route::get('/certificado/{curso}/descargar', [CursoController::class, 'descargarCertificado'])->name('certificado.descargar');
    
    // Verificación de certificado público (sin auth)
    Route::get('/verificar/{codigo}', function($codigo) {
        return view('certificado-verificar', compact('codigo'));
    })->name('certificado.verificar');

    // Página de felicitaciones al completar el curso
    Route::get('/cursos/{curso}/completado', [CursoController::class, 'cursoCompletado'])->name('cursos.completado');
    
    // Ver cursos - lista de cursos disponibles
    Route::get('/cursos', [App\Http\Controllers\CursoController::class, 'index'])->name('cursos.index');
    
    // Crear curso - solo admin
    Route::get('/crear.curso', function () { 
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'No tienes permiso para crear cursos.');
        }
        return view('cursos.create-livewire'); 
    })->name('crear.curso');
    
    // Editar curso - solo admin
    Route::get('/cursos/{curso}/editar', [CursoController::class, 'editLivewire'])->name('cursos.edit')->middleware('can:update,curso');
    
    // Eliminar curso - solo admin
    Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy')->middleware('can:delete,curso');
    
    // Inscribirse en un curso
    Route::post('/cursos/{curso}/comenzar', [CursoController::class, 'comenzar'])->name('cursos.comenzar');
    
    // Reiniciar progreso - solo admin
    Route::post('/cursos/{curso}/reiniciar', [CursoController::class, 'reiniciarProgreso'])->name('cursos.reiniciar');
    
    // ====== FLUJO DEL CURSO ======
    
    // Ver contenido del curso (módulos y materiales)
    Route::get('/mis-cursos/{curso}', [CursoController::class, 'verCurso'])->name('cursos.ver');
    
    // Marcar material como completado
    Route::post('/mis-cursos/{curso}/material', [CursoController::class, 'marcarMaterial'])->name('cursos.material');
    
    // Cuestionario del módulo - ver y responder
    Route::get('/mis-cursos/{curso}/modulo/{modulo}/cuestionario', [CursoController::class, 'verCuestionario'])->name('cursos.cuestionario.ver');
    Route::post('/mis-cursos/{curso}/modulo/{modulo}/cuestionario', [CursoController::class, 'enviarCuestionario'])->name('cursos.cuestionario');
    
    // Resultado del cuestionario (feedback correcto/incorrecto)
    Route::get('/mis-cursos/{curso}/modulo/{modulo}/cuestionario/resultado', [CursoController::class, 'verResultadoCuestionario'])->name('cursos.cuestionario.resultado');
    
    // Evaluación final del curso - página separada
    Route::get('/mis-cursos/{curso}/evaluacion-final', [CursoController::class, 'verEvaluacionFinal'])->name('cursos.evaluacion-final');
    Route::post('/mis-cursos/{curso}/evaluacion-final', [CursoController::class, 'enviarEvaluacionFinal'])->name('cursos.evaluacion-final.enviar');
    
    // Resultado de la evaluación final (feedback correcto/incorrecto)
    Route::get('/mis-cursos/{curso}/evaluacion-final/resultado', [CursoController::class, 'verResultadoEvaluacionFinal'])->name('cursos.evaluacion-final.resultado');
    
    // Archivos privados (PDF) - requiere auth
    Route::get('/archivo/pdf/{filename}', [FileController::class, 'verPdf'])->name('archivo.pdf');
    Route::post('/archivo/pdf/{filename}/descargar', [FileController::class, 'descargarPdf'])->name('archivo.pdf.descargar');

    // Progreso de materiales (videos y PDFs)
    Route::post('/material/{material}/video-progress', [MaterialController::class, 'updateVideoProgress'])->name('material.video.progress');
    Route::post('/material/{material}/pdf-scroll', [MaterialController::class, 'updatePdfScroll'])->name('material.pdf.scroll');
    Route::get('/material/{material}/progress', [MaterialController::class, 'getProgress'])->name('material.progress');
});

// Rutas exclusivas para ADMIN
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
    Route::get('/usuarios/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/usuarios/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Configuración de página (solo admin_global)
    Route::get('/page-settings', [PageSettingsController::class, 'index'])->name('page-settings');
    Route::post('/page-settings', [PageSettingsController::class, 'update'])->name('page-settings.update');
});
