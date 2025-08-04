<?php

namespace App\Models\TablasReferencias;

use App\Models\Diagnosticos\DiagnosticoPregunta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreguntaOpcion extends Model
{
    use SoftDeletes;
    protected $table = 'preguntas_opciones';

    protected $primaryKey = 'opcion_id';

    protected $fillable = [
        'pregunta_id',
        'opcion_variable_response',
        'opcion_percentage',
    ];

    public function pregunta()
    {
        return $this->belongsTo(DiagnosticoPregunta::class, 'pregunta_id', 'pregunta_id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
