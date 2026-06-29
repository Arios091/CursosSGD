<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresoMaterial extends Model
{
    protected $table = 'progreso_material';

    protected $fillable = [
        'user_id',
        'material_id',
        'material_completado',
        'completado_at',
        'tiempo_visto',
        'video_completado',
        'scroll_completado',
    ];

    protected $casts = [
        'material_completado' => 'boolean',
        'video_completado' => 'boolean',
        'scroll_completado' => 'boolean',
        'completado_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}