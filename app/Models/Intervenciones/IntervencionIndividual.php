<?php

namespace App\Models\Intervenciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UserTrait;
use App\Models\User;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\Lead;
use App\Models\Programas\FasePrograma;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public static function registrarSeguimientoInscripcion(array $inscripcion) {
        return self::registrarParaUnidad($inscripcion['unidadproductiva_id'], [
            'categoria_id'    => IntervencionCategoria::CATEGORIA_GESTION_PROGRAMAS,
            'tipo_id'         => IntervencionTipo::TIPO_GESTION_INSCRIPCIONES,
            'programa_id'     => $inscripcion['programa_id'] ?? null,
            'convocatoria_id' => $inscripcion['convocatoria_id'] ?? null,
            'fase_id'         => $inscripcion['fase_id'] ?? null,

            'descripcion'     => $inscripcion['descripcion'],
            'conclusiones'    => $inscripcion['conclusiones'] ?? null,

            'fecha_inicio'    => $inscripcion['fecha_inicio'] ?? now(),
            'fecha_fin'       => $inscripcion['fecha_final'] ?? now(),
        ]);
    }

    /**
     * Registra una intervención individual a partir de un envío de WhatsApp.
     * * @param int $unidadId
     * @param array $requestData Datos del request (phone_type, to, nombre_empresario, mensaje)
     * @param object $template El modelo de la plantilla de WhatsApp utilizada
     */
    public static function registrarEnvioWhatsApp(array $mensajeWhatsapp) {
        return self::registrarParaUnidad($mensajeWhatsapp['unidadproductiva_id'], [
            'categoria_id'     => $mensajeWhatsapp['categoria_id'] ?? IntervencionCategoria::CATEGORIA_GESTION_PROGRAMAS,
            'tipo_id'          => IntervencionTipo::TIPO_WHATSAPP,
            'programa_id'      => $mensajeWhatsapp['programa_id'] ?? null,
            'convocatoria_id'  => $mensajeWhatsapp['convocatoria_id'] ?? null,
            'fase_id'          => $mensajeWhatsapp['fase_id'] ?? null,
            'descripcion'      => $mensajeWhatsapp['descripcion'] ?? 'Sin contenido',
            'conclusiones'     => $mensajeWhatsapp['conclusiones'] ?? null,
            'modalidad'        => $mensajeWhatsapp['modalidad'] ?? 'Virtual',
        ]);
    }



    public static function registrarParaUnidad(int $unidadId, array $datos) {
        if (empty($unidadId)) {
            throw new \Exception('unidadproductiva_id es obligatorio');
        }
        return DB::transaction(function () use ($unidadId, $datos) {
            $now = now();
            $intervencion = self::create([
                'asesor_id'      => $datos['asesor_id'] ?? Auth::id(),
                'programa_id'    => $datos['programa_id'] ?? null,
                'convocatoria_id' => $datos['convocatoria_id'] ?? null,
                'fase_id' => $datos['fase_id'] ?? null,
                'unidadproductiva_id' => $unidadId,
                'categoria_id'   => $datos['categoria_id'] ?? null,
                'tipo_id'        => $datos['tipo_id'] ?? null,
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
