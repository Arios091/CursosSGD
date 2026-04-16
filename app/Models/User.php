<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'email',
        'password',
        'role',
    ];

    //Agregamos un método para verificar si el usuario es admin
    public function isAdmin(): bool
    {
    return $this->role === 'admin';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
    * Curso que el usuario está cursando actualmente (solo uno permitido)
    */
    public function cursoEnProgreso()
    {
    return $this->belongsTo(Curso::class, 'curso_en_progreso_id');
    }

    /**
    * Todos los progresos/inscripciones del usuario
    */
    public function progresos()
    {
    return $this->hasMany(ProgresoCurso::class);
    }


}
