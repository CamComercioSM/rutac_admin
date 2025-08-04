<?php

namespace App\Models\Diagnosticos;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DiagnosticoRespuesta extends Model
{
    use SoftDeletes;
    protected $table = 'diagnosticos_respuestas';

    protected $primaryKey = 'diagnosticorespuesta_id';

    protected $fillable = [
        'resultado_id',
        'pregunta_id',
        'diagnosticorespuesta_valor',
        'diagnosticorespuesta_porcentaje',
    ];

    // Relaciones

    public function resultado()
    {
        return $this->belongsTo(DiagnosticoResultado::class, 'resultado_id');
    }

    public function pregunta()
    {
        return $this->belongsTo(DiagnosticoPregunta::class, 'pregunta_id', 'pregunta_id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
