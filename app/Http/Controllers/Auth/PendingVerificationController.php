<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Http\Request;

class PendingVerificationController extends Controller
{
    public function verify($token)
    {
        $pendingUser = PendingUser::where('token', $token)->first();

        if (!$pendingUser) {
            return redirect()->route('login')
                ->with('error', 'El enlace de verificación no es válido. Por favor, regístrate nuevamente.');
        }

        if ($pendingUser->hasExpired()) {
            $pendingUser->delete();
            return redirect()->route('register')
                ->with('error', 'El enlace de verificación ha expirado. Por favor, regístrate nuevamente.');
        }

        if (User::where('email', $pendingUser->email)->exists()) {
            $pendingUser->delete();
            return redirect()->route('login')
                ->with('info', 'Esta cuenta ya está registrada. Inicia sesión con tus credenciales.');
        }

        $user = User::create([
            'name' => $pendingUser->name,
            'primer_nombre' => $pendingUser->primer_nombre,
            'segundo_nombre' => $pendingUser->segundo_nombre,
            'primer_apellido' => $pendingUser->primer_apellido,
            'segundo_apellido' => $pendingUser->segundo_apellido,
            'email' => $pendingUser->email,
            'password' => $pendingUser->password,
            'role' => $pendingUser->role,
            'email_verified_at' => now(),
        ]);

        $pendingUser->delete();

        return redirect()->route('login')
            ->with('success', 'Tu correo ha sido verificado exitosamente. Ahora puedes iniciar sesión.');
    }
}
