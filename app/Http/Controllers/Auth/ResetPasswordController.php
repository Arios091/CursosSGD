<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    // Mostrar formulario de nueva contraseña
    public function showResetForm(Request $request)
    {
        $token = $request->token;
        $email = $request->email;
        
        return view('auth.passwords.reset', compact('token', 'email'));
    }

    // Procesar nueva contraseña
    public function reset(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'token.required' => 'El token de verificación es requerido.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El formato del correo no es válido.',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Buscar el token
        $passwordReset = DB::table('password_resets')
            ->where('email', $validated['email'])
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'El enlace de recuperación ha expirado o es inválido.']);
        }

        // Verificar token
        if (!hash_equals($passwordReset->token, $validated['token'])) {
            return back()->withErrors(['email' => 'El token de verificación no coincide.']);
        }

        // Verificar expiración (60 minutos)
        $createdAt = \Carbon\Carbon::parse($passwordReset->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'El enlace de recuperación ha expirado. Por favor, solicita uno nuevo.']);
        }

        // Actualizar contraseña
        $user = \App\Models\User::where('email', $validated['email'])->first();
        
        if ($user) {
            $user->update([
                'password' => Hash::make($validated['password'])
            ]);
            
            // Eliminar token usado
            DB::table('password_resets')->where('email', $validated['email'])->delete();
            
            return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida exitosamente. Ya puedes iniciar sesión.');
        }

        return back()->withErrors(['email' => 'No se encontró el usuario.']);
    }
}