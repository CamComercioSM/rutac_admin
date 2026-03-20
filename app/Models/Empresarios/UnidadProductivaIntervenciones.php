<?php

namespace App\Models\Empresarios;

use App\Models\Intervenciones\IntervencionLead;
use App\Models\Intervenciones\IntervencionUnidad;
use App\Models\Lead;
use App\Models\Programas\FasePrograma;
use App\Models\User;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\TablasReferencias\CategoriasIntervenciones;
use App\Models\TablasReferencias\TiposIntervenciones;

class UnidadProductivaIntervenciones extends Model {
    use SoftDeletes, UserTrait;

    protected $table = 'unidadesproductivas_intervenciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'asesor_id',
        'programa_id',
        'convocatoria_id',
        'fase_id',
        'categoria_id',
        'tipo_id',


        'unidadproductiva_id',
        'lead_id',

        'fecha_inicio',
        'fecha_fin',

        'referencia_id',
        'modalidad',
        'participantes',

        'descripcion',
        'conclusiones',
        'soporte',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime:Y-m-d H:i:s',
    ];

    // Relaciones
    public function unidadProductiva() {
        return $this->belongsTo(UnidadProductiva::class, 'unidadproductiva_id', 'unidadproductiva_id');
    }

    public function asesor() {
        return $this->belongsTo(User::class, 'asesor_id', 'id');
    }

    public function categoria() {
        return $this->belongsTo(CategoriasIntervenciones::class, 'categoria_id', 'id');
    }

    public function tipo() {
        return $this->belongsTo(TiposIntervenciones::class, 'tipo_id', 'id');
    }

    public function fase() {
        return $this->belongsTo(FasePrograma::class, 'fase_id', 'fase_id');
    }

    public function unidades() {
        return $this->hasMany(IntervencionUnidad::class, 'intervencion_id', 'id');
    }

    public function leads() {
        return $this->hasMany(IntervencionLead::class, 'intervencion_id', 'id');
    }

    public function lead() {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }

    public static $modalidades = [
        'Presencial' => 'Presencial',
        'Virtual' => 'Virtual',
        'Hibrida' => 'Hibrida',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
