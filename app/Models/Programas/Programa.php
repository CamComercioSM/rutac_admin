<?php

namespace App\Models\Programas;

use App\Models\Inscripciones\ConvocatoriaInscripcion;
use App\Models\TablasReferencias\Etapa;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programa extends Model
{
    use SoftDeletes, UserTrait;

    protected $table = 'programas';
    protected $primaryKey = 'programa_id';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',                    
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
        'sitio_web'
    ];
    
    public function inscripciones(): HasMany {
        return $this->hasMany(ConvocatoriaInscripcion::class, 'programa_id', 'programa_id');
    }

    public function convocatorias()
    {
        return $this->hasMany(ProgramaConvocatoria::class, 'programa_id', 'programa_id');
    }

    
    public function etapas()
    {
        return $this->belongsToMany(Etapa::class, 'programas_etapas', 'programa_id', 'etapa_id');
    }
    

    public static $es_virtual_text = [
        '0' => 'Presencial',
        '1' => 'Virtual',
        '2' => 'Presencial y virtual'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
