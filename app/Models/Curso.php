<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;
    /**
     * Los campos que se pueden llenar de forma masiva desde formularios o create()
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'carga_horaria',
        'imagen_referencial',
        'user_id',
        'categoria',
    ];

    /**
     * Relación con el docente (usuario que creó el curso)
     */
    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuarios inscritos en este curso (con su progreso)
     */
    public function progresos()
    {
    return $this->hasMany(ProgresoCurso::class);
    }

    /**
     * Módulos del curso (para organización)
     */
    public function modulos()
    {
        return $this->hasMany(Modulo::class)->orderBy('orden');
    }

    /**
     * Materiales del curso
     */
    public function materiales()
    {
        return $this->hasMany(Material::class)->orderBy('orden');
    }

    /**
     * Evaluación final del curso
     */
    public function evaluacionFinal()
    {
        return $this->hasOne(EvaluacionFinal::class);
    }
} 
