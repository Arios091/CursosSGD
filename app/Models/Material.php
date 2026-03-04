<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'curso_id',
        'titulo',
        'tipo',
        'url',
        'orden',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
