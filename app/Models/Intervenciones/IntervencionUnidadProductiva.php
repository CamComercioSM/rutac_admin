<?php

namespace App\Models\Intervenciones;

use App\Models\Programas\FasePrograma;
use App\Models\Intervenciones\IntervencionLead;
use App\Models\Intervenciones\IntervencionUnidad;
use App\Models\Lead;
use App\Models\User; 
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\TablasReferencias\CategoriasIntervenciones;
use App\Models\TablasReferencias\TiposIntervenciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IntervencionUnidadProductiva extends Model {
    use SoftDeletes, UserTrait;

    const CATEGORIA_GESTION_PROGRAMAS = 1;
    const CATEGORIA_GESTION_CAMPANA = 2;
    const CATEGORIA_SOCIALIZACION = 3;
    const CATEGORIA_VINCULACION = 4;

    const TIPO_GESTION_INSCRIPCIONES = 1;
    const TIPO_WHATSAPP = 2;
    const TIPO_LLAMADA = 3;
    const TIPO_CORREO = 4;

    protected $table = 'intervenciones_unidadesproductivas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'asesor_id',
        'programa_id',
        'convocatoria_id',
        'fase_id',
        'categoria_id',
        'tipo_id',

        'fecha_inicio',
        'fecha_fin',

        'referencia_id',
        'modalidad',

        'cant_unidades', 
        'participantes',
        'cant_leads',  
        'participantes_otros',

        'descripcion',
        'conclusiones',
        'soporte',
        'estado',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime:Y-m-d H:i:s',
    ];

    // Relaciones
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

    /**
     * Registra una intervención y la vincula a una unidad productiva en un solo paso.
     */
    public static function registrarParaUnidad($unidadId, array $datos) {
        return DB::transaction(function () use ($unidadId, $datos) {
            $now = now();

            $intervencion = self::create([
                'asesor_id'      => $datos['asesor_id'] ?? Auth::id(),
                // AGREGAMOS ESTOS CAMPOS:
                'programa_id'    => $datos['programa_id'] ?? null,
                'convocatoria_id' => $datos['convocatoria_id'] ?? null,
                'categoria_id'   => $datos['categoria_id'] ?? self::CATEGORIA_GESTION_PROGRAMAS,
                'tipo_id'        => $datos['tipo_id'] ?? self::TIPO_WHATSAPP,
                'descripcion'    => $datos['descripcion'],
                'fecha_inicio'   => $datos['fecha_inicio'] ?? $now,
                'fecha_fin'      => $datos['fecha_fin'] ?? $now,
                'modalidad'      => $datos['modalidad'] ?? 'Virtual',
                'cant_unidades'  => 1,
                'participantes'  => $datos['participantes'] ?? 1,
                'conclusiones'   => $datos['conclusiones'] ?? null, // IMPORTANTE
            ]);

            \App\Models\Intervenciones\IntervencionUnidad::create([
                'intervencion_id'     => $intervencion->id,
                'unidadproductiva_id' => $unidadId,
                'participantes'       => $datos['participantes'] ?? 1,
            ]);

            return $intervencion;
        });
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
