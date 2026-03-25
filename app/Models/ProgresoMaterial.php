<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresoMaterial extends Model
{
    protected $table = 'progreso_materiales';

    protected $fillable = [
        'user_id',
        'material_id',
        'completado',
        'completado_at',
    ];

    protected $casts = [
        'completado' => 'boolean',
        'completado_at' => 'datetime',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
