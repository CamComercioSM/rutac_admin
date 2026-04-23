<?php

namespace App\Models\Intervenciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UserTrait;
use App\Models\Traits\AuditTrait;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\Empresarios\UnidadProductivaIntervenciones;

class IntervencionUnidad extends Model
{
    use SoftDeletes, UserTrait, AuditTrait;

    protected $table = 'intervencion_unidades';
    protected $primaryKey = 'intervencion_unidad_id';

    protected $fillable = [
        'intervencion_id',
        'unidadproductiva_id',
        'participantes',
    ];

    public function intervencion()
    {
        return $this->belongsTo(UnidadProductivaIntervenciones::class, 'intervencion_id', 'id');
    }

    public function unidadProductiva()
    {
        return $this->belongsTo(
            \App\Models\Empresarios\UnidadProductiva::class,
            'unidadproductiva_id',
            'unidadproductiva_id'
        );
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
