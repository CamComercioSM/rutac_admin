<?php

namespace App\Models\Intervenciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UserTrait;
use App\Models\User;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\Lead;
use App\Models\Programas\FasePrograma;

class IntervencionIndividual extends Model {
    use SoftDeletes, UserTrait;

    protected $table = 'intervenciones_individuales';
    protected $primaryKey = 'id';

    protected $fillable = [
        'asesor_id',
        'programa_id',
        'convocatoria_id',
        'fase_id',

        'unidadproductiva_id',
        'lead_id',

        'participantes',

        'descripcion',
        'soporte',
        'evidencia_url',

        'fecha_inicio',
        'fecha_fin',

        'categoria_id',
        'tipo_id',

        'titulo',
        'referencia_id',
        'modalidad',

        'conclusiones',

        'estado',

        'fecha_creacion',
        'fecha_actualizacion',
        'fecha_eliminacion',

        'usuario_creo',
        'usuario_actualizo',
        'usuario_elimino',

        'reporte_mensual_id'
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin',
        'fecha_creacion',
        'fecha_actualizacion',
        'fecha_eliminacion'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function asesor() {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function unidadProductiva() {
        return $this->belongsTo(UnidadProductiva::class, 'unidadproductiva_id', 'unidadproductiva_id');
    }

    public function lead() {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }

    public function fase() {
        return $this->belongsTo(FasePrograma::class, 'fase_id', 'fase_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function esUnidad() {
        return !is_null($this->unidadproductiva_id);
    }

    public function esLead() {
        return !is_null($this->lead_id);
    }
}
