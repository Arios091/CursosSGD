<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $fillable = [
        'curso_id',
        'titulo',
        'orden',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    // Accesor para mostrar título autogenerado si no hay manual
    public function getTituloAttribute($value)
    {
        return $value ?? 'Módulo ' . $this->orden;
    }
}