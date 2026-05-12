<?php

namespace App\Policies;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CursoPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver la lista de cursos
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede ver un curso específico
     */
    public function view(User $user, Curso $curso): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede crear cursos
     * Solo admin puede crear
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'admin_global']);
    }

    /**
     * Determina si el usuario puede editar un curso
     * Solo admin o admin_global puede editar
     */
    public function update(User $user, Curso $curso): bool
    {
        return in_array($user->role, ['admin', 'admin_global']);
    }

    /**
     * Determina si el usuario puede eliminar un curso
     * Solo admin o admin_global puede eliminar
     */
    public function delete(User $user, Curso $curso): bool
    {
        return in_array($user->role, ['admin', 'admin_global']);
    }
}
