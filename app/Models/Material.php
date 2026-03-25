<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'modulo_id',
        'titulo',
        'tipo',
        'url',
        'orden',
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }
}
