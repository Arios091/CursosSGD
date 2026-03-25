<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionFinal extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_finales';

    protected $fillable = [
        'curso_id',
        'titulo',
        'min_aprobacion',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function preguntas()
    {
        return $this->hasMany(PreguntaEvaluacion::class)->orderBy('orden');
    }
}