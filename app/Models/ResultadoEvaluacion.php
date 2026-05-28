<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadoEvaluacion extends Model
{
    protected $table = 'resultados_evaluacion';

    protected $fillable = [
        'user_id',
        'curso_id',
        'nota',
        'aprobado',
        'completado_at',
    ];

    protected $casts = [
        'aprobado' => 'boolean',
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
}
