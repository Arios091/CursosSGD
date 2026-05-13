<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    // Mostrar formulario de recuperación
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Enviar correo de recuperación
    public function sendResetLinkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.exists' => 'No encontramos una cuenta con este correo electrónico.'
        ]);

        // Buscar usuario
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput()
                ->withErrors(['email' => 'No encontramos una cuenta con este correo electrónico.']);
        }

        // Generar token único
        $token = Str::random(64);

        // Guardar token en base de datos
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Generar URL de recuperación - usar la ruta correcta con token y email como query params
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));
        } catch (\Exception $e) {
            \Log::error('Error sending password reset email: ' . $e->getMessage());
            return back()->withInput()
                ->withErrors(['email' => 'Error al enviar el correo. Por favor, intenta más tarde.']);
        }

        return back()->with('status', 'Te hemos enviado un correo con el enlace para restablecer tu contraseña. Revisa tu bandeja de entrada (incluyendo spam).');
    }
}