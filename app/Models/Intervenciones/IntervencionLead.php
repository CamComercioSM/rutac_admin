<?php

namespace App\Models\Intervenciones;

use App\Models\Empresarios\UnidadProductivaIntervenciones;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UserTrait;

class IntervencionLead extends Model
{
    use SoftDeletes, UserTrait;

    protected $table = 'intervencion_leads';
    protected $primaryKey = 'intervencion_lead_id';

    protected $fillable = [
        'intervencion_id',
        'lead_id',
        'participantes',
    ];

    public function intervencion()
    {
        return $this->belongsTo(UnidadProductivaIntervenciones::class, 'intervencion_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo(\App\Models\Lead::class, 'lead_id', 'id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
