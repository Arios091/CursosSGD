<?php

namespace App\Models;

use App\Models\Traits\BoolToPgString;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opcion extends Model
{
    use BoolToPgString, HasFactory;

    protected $pgBoolFields = ['es_correcta'];

    protected $table = 'opciones';

    protected $fillable = [
        'pregunta_id',
        'opcion',
        'es_correcta',
        'orden',
    ];

    protected $casts = [
        'es_correcta' => 'boolean',
    ];

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class);
    }
}