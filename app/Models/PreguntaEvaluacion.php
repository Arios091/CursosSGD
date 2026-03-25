<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreguntaEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'preguntas_evaluacion';

    protected $fillable = [
        'evaluacion_final_id',
        'pregunta',
        'orden',
    ];

    public function evaluacionFinal()
    {
        return $this->belongsTo(EvaluacionFinal::class, 'evaluacion_final_id');
    }

    public function opciones()
    {
        return $this->hasMany(OpcionEvaluacion::class)->orderBy('orden');
    }
}