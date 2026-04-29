<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresoMaterial extends Model
{
    protected $fillable = [
        'user_id',
        'material_id',
        'tiempo_visto',
        'video_completado',
        'scroll_completado',
        'material_completado'
    ];

    protected $casts = [
        'video_completado' => 'boolean',
        'scroll_completado' => 'boolean',
        'material_completado' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Verificar si el material está completo
    public function verificarCompletado($material)
    {
        if ($material->type === 'video') {
            // Para videos: tiempo mínimo de 2 minutos (120 segundos)
            $tiempoRequerido = 120;
            $this->video_completado = $this->tiempo_visto >= $tiempoRequerido;
        }

        if ($material->type === 'pdf') {
            // Para PDFs: scroll completado
            // El scroll_completado ya se actualiza via JavaScript
        }

        $this->material_completado = $this->video_completado || $this->scroll_completado;
        $this->save();

        return $this->material_completado;
    }
}