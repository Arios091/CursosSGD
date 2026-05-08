<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->puedeGestionarUsuarios()) {
                return redirect()->route('home')->with('error', 'No tienes permiso para acceder a esta sección.');
            }
            return $next($request);
        });
    }
    
    public function index(Request $request)
    {
        $query = User::query()->with('progresos');
        
        // Búsqueda por nombre o email (case insensitive)
        if ($request->has('search') && $request->search) {
            $search = mb_strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(primer_nombre) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(segundo_nombre) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(primer_apellido) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(segundo_apellido) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
            });
        }
        
        // Filtro por rol
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        // Ordenar por
        $sortBy = $request->get('sort', 'latest');
        if ($sortBy === 'oldest') {
            $query->oldest();
        } elseif ($sortBy === 'name') {
            $query->orderBy('primer_nombre', 'asc');
        } else {
            $query->latest();
        }
        
        $users = $query->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin_global,admin,docente,estudiante',
        ]);

        $user->update([
            'primer_nombre' => $request->primer_nombre,
            'segundo_nombre' => $request->segundo_nombre,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'email' => $request->email,
            'role' => $request->role,
            'name' => trim($request->primer_nombre . ' ' . ($request->segundo_nombre ? $request->segundo_nombre . ' ' : '') . $request->primer_apellido . ' ' . $request->segundo_apellido),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success_password', 'Contraseña restablecida correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
