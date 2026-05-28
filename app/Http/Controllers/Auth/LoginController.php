<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string|email',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'El correo electrónico es obligatorio. Por favor, ingresa tu correo institucional.',
            'email.email' => 'El formato del correo electrónico no es válido. Ejemplo: usuario@unas.edu.pe',
            'email.exists' => 'No encontramos una cuenta registrada con este correo. Verifica que sea correcto o regístrate.',
            'password.required' => 'La contraseña es obligatoria. Por favor, ingresa tu contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => 'El usuario o la contraseña son incorrectos. Por favor, verifica tus credenciales e intenta nuevamente.'
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        if (! $user->hasVerifiedEmail()) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('verification.notice')
                ->with('warning', 'Debes verificar tu correo electrónico antes de iniciar sesión. Revisa tu bandeja de entrada.');
        }
    }
}
