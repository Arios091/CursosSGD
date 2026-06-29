<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use App\Notifications\VerifyPendingEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'primer_nombre' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'segundo_nombre' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'primer_apellido' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'segundo_apellido' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                'unique:users',
                'unique:pending_users',
                'regex:/^[a-zA-Z0-9._%+-]+@unas\.edu\.pe$/i',
            ],
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
            'email.regex' => 'Solo se permiten correos institucionales @unas.edu.pe.',
            'email.unique' => 'Este correo ya está registrado. ¿Ya tienes una cuenta? <a href="' . route('login') . '" class="alert-link">Inicia sesión</a>',
            'email.max' => 'El correo electrónico es demasiado largo. Máximo 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria. Por favor, ingresa una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden. Por favor, verifica que sean iguales.',
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $name = $request->primer_nombre;
        if ($request->filled('segundo_nombre')) {
            $name .= ' ' . $request->segundo_nombre;
        }
        $name .= ' ' . $request->primer_apellido;
        if ($request->filled('segundo_apellido')) {
            $name .= ' ' . $request->segundo_apellido;
        }

        $pendingUser = PendingUser::create([
            'name' => $name,
            'primer_nombre' => $request->primer_nombre,
            'segundo_nombre' => $request->segundo_nombre,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'docente',
            'token' => Str::random(64),
            'expires_at' => now()->addHours(24),
        ]);

        $pendingUser->notify(new VerifyPendingEmail($pendingUser));

        return redirect()->route('pending-verification.notice')
            ->with('email', $request->email);
    }
}
