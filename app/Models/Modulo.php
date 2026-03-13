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

    public function materiales()
    {
        return $this->hasMany(Material::class)->orderBy('orden');
    }

    // Accesor: título autogenerado si no hay manual
    public function getTituloAttribute($value)
    {
        return $value ?? 'Módulo ' . $this->orden;
    }
}