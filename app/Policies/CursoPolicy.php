<?php

namespace App\Policies;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CursoPolicy
{
    use HandlesAuthorization;

    // Ver cualquier curso (lista o detalle)
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Curso $curso): bool
    {
        return true;
    }

    // Solo admin y docentes pueden crear cursos nuevos
    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'docente';
    }

    // Solo admin puede editar todos los cursos, docentes solo los suyos
    public function update(User $user, Curso $curso): bool
    {
        return $user->isAdmin() || ($user->id === $curso->user_id && $user->role === 'docente');
    }

    // Solo admin puede eliminar todos los cursos, docentes solo los suyos
    public function delete(User $user, Curso $curso): bool
    {
        return $user->isAdmin() || ($user->id === $curso->user_id && $user->role === 'docente');
    }
}