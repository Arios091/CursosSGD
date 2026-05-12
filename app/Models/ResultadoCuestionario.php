<?php

namespace App\Models;

use App\Models\Traits\BoolToPgString;
use Illuminate\Database\Eloquent\Model;

class ResultadoCuestionario extends Model
{
    use BoolToPgString;

    protected $pgBoolFields = ['aprobado'];

    protected $table = 'resultados_cuestionario';

    protected $fillable = [
        'user_id',
        'modulo_id',
        'cuestionario_id',
        'nota',
        'aprobado',
        'completado_at',
    ];

    protected $casts = [
        'aprobado' => 'boolean',
        'completado_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }

    public function cuestionario()
    {
        return $this->belongsTo(Cuestionario::class);
    }
}
