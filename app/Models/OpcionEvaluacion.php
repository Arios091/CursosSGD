<?php

namespace App\Models;

use App\Models\Traits\BoolToPgString;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcionEvaluacion extends Model
{
    use BoolToPgString, HasFactory;

    protected $pgBoolFields = ['es_correcta'];

    protected $table = 'opciones_evaluacion';

    protected $fillable = [
        'pregunta_evaluacion_id',
        'opcion',
        'es_correcta',
        'orden',
    ];

    protected $casts = [
        'es_correcta' => 'boolean',
    ];

    public function preguntaEvaluacion()
    {
        return $this->belongsTo(PreguntaEvaluacion::class, 'pregunta_evaluacion_id');
    }
}