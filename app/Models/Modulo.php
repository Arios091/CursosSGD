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

    public function cuestionario()
    {
        return $this->hasOne(Cuestionario::class);
    }

    public function getTituloDisplayAttribute()
    {
        return $this->titulo ?: 'Módulo ' . $this->orden;
    }
}