<?php

namespace App\Models\Intervenciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UserTrait;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\RutaCModel;

class IntervencionUnidad extends RutaCModel
{

    protected $table = 'intervencion_unidades';
    protected $primaryKey = 'intervencion_unidad_id';

    protected $fillable = [
        'intervencion_id',
        'unidadproductiva_id',
        'participantes',
    ];

    public function intervencion()
    {
        return $this->belongsTo(IntervencionUnidadProductiva::class, 'intervencion_id', 'id');
    }

    public function unidadProductiva()
    {
        return $this->belongsTo(
            \App\Models\Empresarios\UnidadProductiva::class,
            'unidadproductiva_id',
            'unidadproductiva_id'
        );
    }

}
