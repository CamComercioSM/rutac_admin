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



    public static function registrarParaUnidad($unidadId, array $datos) {
        return DB::transaction(function () use ($unidadId, $datos) {
            $now = now();

            $intervencion = self::create([
                'asesor_id'      => $datos['asesor_id'] ?? Auth::id(),

                'programa_id'    => $datos['programa_id'] ?? null,
                'convocatoria_id' => $datos['convocatoria_id'] ?? null,
                'fase_id' => $datos['fase_id'] ?? null,
                'unidadproductiva_id' => $unidadId,

                'categoria_id'   => $datos['categoria_id'] ?? self::CATEGORIA_GESTION_PROGRAMAS,
                'tipo_id'        => $datos['tipo_id'] ?? self::TIPO_WHATSAPP,
                'descripcion'    => $datos['descripcion'],
                'fecha_inicio'   => $datos['fecha_inicio'] ?? $now,
                'fecha_fin'      => $datos['fecha_fin'] ?? $now,
                'modalidad'      => $datos['modalidad'] ?? 'Virtual',
                'participantes'  => $datos['participantes'] ?? 1,
                'descripcion'   => $datos['descripcion'] ?? null,
                'conclusiones'   => $datos['conclusiones'] ?? null,
            ]);

            return $intervencion;
        });
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
