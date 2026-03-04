<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class HandleExpiredSession
{
    /**
     * Maneja el error 419 (Page Expired) redirigiendo a welcome con mensaje
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Si la sesión caducó o token CSRF no coincide → redirigir a welcome
            return redirect()
                ->route('welcome')
                ->with('error', 'Tu sesión ha caducado. Por favor inicia sesión de nuevo.');
        }
    }
}