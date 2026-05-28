<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'primer_nombre' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'segundo_nombre' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'primer_apellido' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'segundo_apellido' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'string', 
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'confirmed'
            ],
        ], [
            'primer_nombre.required' => 'El primer nombre es obligatorio. Por favor, ingresa tu nombre.',
            'primer_nombre.regex' => 'El primer nombre solo puede contener letras. No uses números ni símbolos.',
            'segundo_nombre.regex' => 'El segundo nombre solo puede contener letras. No uses números ni símbolos.',
            'primer_apellido.required' => 'El primer apellido es obligatorio. Por favor, ingresa tu apellido.',
            'primer_apellido.regex' => 'El primer apellido solo puede contener letras. No uses números ni símbolos.',
            'segundo_apellido.required' => 'El segundo apellido es obligatorio. Por favor, ingresa tu apellido.',
            'segundo_apellido.regex' => 'El segundo apellido solo puede contener letras. No uses números ni símbolos.',
            'email.required' => 'El correo electrónico es obligatorio. Por favor, ingresa tu correo institucional.',
            'email.email' => 'El formato del correo no es válido. Ejemplo: juan.perez@unas.edu.pe',
            'email.unique' => 'Este correo ya está registrado. ¿Ya tienes una cuenta? <a href="' . route('login') . '" class="alert-link">Inicia sesión</a>',
            'email.max' => 'El correo electrónico es demasiado largo. Máximo 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria. Por favor, ingresa una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden. Por favor, verifica que sean iguales.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function registered(Request $request, $user)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('verification.notice')
            ->with('success', 'Te hemos enviado un correo de verificación. Revisa tu bandeja de entrada.');
    }

    protected function create(array $data)
    {
        $name = $data['primer_nombre'];
        if (!empty($data['segundo_nombre'])) {
            $name .= ' ' . $data['segundo_nombre'];
        }
        $name .= ' ' . $data['primer_apellido'];
        if (!empty($data['segundo_apellido'])) {
            $name .= ' ' . $data['segundo_apellido'];
        }
        
        return User::create([
            'name' => $name,
            'primer_nombre' => $data['primer_nombre'],
            'segundo_nombre' => $data['segundo_nombre'] ?? null,
            'primer_apellido' => $data['primer_apellido'],
            'segundo_apellido' => $data['segundo_apellido'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'docente',
        ]);
    }
}
