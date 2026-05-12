<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresoCurso extends Model
{
    protected $table = 'progreso_cursos';

    protected $fillable = [
        'user_id',
        'curso_id',
        'estado',
        'modulo_actual',
        'material_actual',
        'completado_at',
        'evaluacion_aprobada',
        'fecha_fin',
    ];

    protected $casts = [
        'evaluacion_aprobada' => 'boolean',
        'completado_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function getCompletadoAttribute()
    {
        return in_array($this->estado, ['completado', 'terminado']);
    }

    public function isCompletado()
    {
        return $this->completado;
    }
}