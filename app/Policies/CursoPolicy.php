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

    // Solo admin puede crear cursos nuevos
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    // Solo admin puede editar
    public function update(User $user, Curso $curso): bool
    {
        return $user->isAdmin();
    }

    // Solo admin puede eliminar
    public function delete(User $user, Curso $curso): bool
    {
        return $user->isAdmin();
    }
}