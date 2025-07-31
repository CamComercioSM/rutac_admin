<?php

namespace App\Models\Programas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramaConvocatoria extends Model
{
    use SoftDeletes;

    protected $table = 'programas_convocatorias';
    protected $primaryKey = 'convocatoria_id';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'programa_id',
        'nombre_convocatoria',
        'descripcion',
        'logo',
        'beneficios',
        'requisitos',
        'duracion',
        'dirigido_a',
        'objetivo',
        'determinantes',
        'procedimiento_imagen',
        'herramientas_requeridas',
        'es_virtual',
        'persona_encargada',
        'correo_contacto',
        'telefono',
        'informacion_adicional',
        'sitio_web',
        'fecha_apertura_convocatoria',
        'fecha_cierre_convocatoria',
        'con_matricula',
        'sector_id',
    ];

    protected $casts = [
        'fecha_apertura_convocatoria' => 'date',
        'fecha_cierre_convocatoria' => 'date',
    ];

    // Definición de constantes para los timestamps personalizados
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    /**
     * Relación con el modelo Programa.
     */
    public function programa(): BelongsTo
    {
        return $this->belongsTo(Programa::class, 'programa_id', 'programa_id');
    }

    /*
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sector_id', 'sector_id');
    }

    public function inscripciones(): HasMany {
        return $this->hasMany(ConvocatoriaInscripcion::class, 'convocatoria_id', 'convocatoria_id');
    }

    public function requisitos()
    {
        return $this->belongsToMany(
            InscripcionesRequisitos::class, 
            'convocatorias_requisitos', 
            'convocatoria_id', 
            'requisito_id')
            ->whereNull('indicador_id');
    }

    public function requisitosIndicadores()
    {
        return $this->belongsToMany(
            InscripcionesRequisitos::class, 
            'convocatorias_requisitos', 
            'convocatoria_id', 
            'requisito_id')
            ->withPivot('referencia')     
            ->whereNotNull('indicador_id'); 
    }

    public static $es_virtual = [
        '0' => 'Presencial',
        '1' => 'Virtual',
        '2' => 'Presencial y virtual'
    ];
    */
}
