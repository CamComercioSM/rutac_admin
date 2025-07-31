<?php

namespace App\Models\Inscripciones;

use App\Models\Programas\InscripcionesRequisitos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConvocatoriaRespuesta extends Model
{
    use HasFactory, SoftDeletes;

    // Nombre de la tabla
    protected $table = 'convocatorias_respuestas';

    // Clave primaria personalizada
    protected $primaryKey = 'convocatoriarespuesta_id';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'fecha_respuesta',
        'inscripcion_id',
        'requisito_id',
        'value',
        'fecha_respuesta'
    ];

    // Definición de constantes para los timestamps personalizados
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';


    public function inscripcion()
    {
        return $this->belongsTo(ConvocatoriaInscripcion::class, 'inscripcion_id', 'inscripcion_id');
    }

    public function requisito()
    {
        return $this->belongsTo(InscripcionesRequisitos::class, 'requisito_id', 'requisito_id');
    }

}
