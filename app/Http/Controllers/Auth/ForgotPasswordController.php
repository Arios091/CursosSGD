<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'No encontramos una cuenta con este correo electrónico.']);
        }

        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false);

        try {
            \Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($user, $resetUrl));
        } catch (\Exception $e) {
            \Log::error('Error sending password reset email: ' . $e->getMessage());
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Error al enviar el correo. Por favor, intenta más tarde.']);
        }

        return back()->with('status', 'Te hemos enviado un correo con el enlace para restablecer tu contraseña. Revisa tu bandeja de entrada (y spam).');
    }

    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.exists' => 'No encontramos una cuenta con este correo electrónico.'
        ]);
    }
}