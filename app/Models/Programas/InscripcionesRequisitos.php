<?php

namespace App\Models\Programas;

use App\Models\TablasReferencias\PreguntaDimension;
use App\Models\TablasReferencias\PreguntaGrupo;
use App\Models\TablasReferencias\PreguntaTipo;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InscripcionesRequisitos extends Model
{
    use SoftDeletes, UserTrait;
    
    protected $table = 'inscripciones_requisitos';
    protected $primaryKey = 'requisito_id';

    protected $fillable = [
        'indicador_id',
        'preguntagrupo_id',
        'preguntatipo_id',
        'preguntadimension_id',
        'requisito_titulo',
        'requisito_porcentaje',
    ];

    // Relaciones

    public function indicador()
    {
        return $this->belongsTo(ProgramaIndicador::class, 'indicador_id');
    }

    public function grupo()
    {
        return $this->belongsTo(PreguntaGrupo::class, 'preguntagrupo_id');
    }

    public function tipo()
    {
        return $this->belongsTo(PreguntaTipo::class, 'preguntatipo_id', 'preguntatipo_id');
    }

    public function dimension()
    {
        return $this->belongsTo(PreguntaDimension::class, 'preguntadimension_id');
    }

    public function opciones()
    {
        return $this->hasMany(RequisitosOpciones::class, 'requisito_id', 'requisito_id');
    }

    public function convocatorias()
    {
        return $this->belongsToMany(ProgramaConvocatoria::class, 'convocatoria_requisito', 'requisito_id', 'convocatoria_id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}

