<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresoCurso extends Model
{
    // Campos que se pueden llenar de forma masiva (desde controlador o formulario)
    protected $fillable = [
        'user_id',
        'curso_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'evaluacion_aprobada',
    ];

    /**
     * Relación: este progreso pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: este progreso pertenece a un curso
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
    
}