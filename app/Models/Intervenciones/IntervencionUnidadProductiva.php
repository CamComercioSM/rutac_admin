<?php

namespace App\Models\Intervenciones;

use App\Models\Programas\FasePrograma;
use App\Models\Intervenciones\IntervencionLead;
use App\Models\Intervenciones\IntervencionUnidad;
use App\Models\Lead;
use App\Models\Programas\Programa;
use App\Models\Programas\ProgramaConvocatoria;
use App\Models\User;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\TablasReferencias\CategoriasIntervenciones;
use App\Models\TablasReferencias\TiposIntervenciones;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IntervencionUnidadProductiva extends Model
{
    use SoftDeletes, UserTrait;

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
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    // ✅ Accessor para el total de asistentes (Unidades + Leads)
    public function getTotalAsistentesAttribute()
    {
        return ($this->participantes ?? 0) + ($this->participantes_otros ?? 0);
    }

    // ✅ Accessor para la URL de la evidencia
    public function getEvidenciaUrlAttribute()
    {
        if (!$this->soporte) return null;
        // Asumiendo que se guardan en el disco 'public' dentro de 'intervenciones'
        return $this->soporte;
    }

    // Relaciones
    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id', 'id');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }

    public function convocatoria()
    {
        return $this->belongsTo(ProgramaConvocatoria::class, 'convocatoria_id');
    }

    public function categoria()
    {
        return $this->belongsTo(IntervencionCategoria::class, 'categoria_id', 'id');
    }

    public function tipo()
    {
        return $this->belongsTo(IntervencionTipo::class, 'tipo_id', 'id');
    }

    public function fase()
    {
        return $this->belongsTo(FasePrograma::class, 'fase_id', 'fase_id');
    }

    public function unidades()
    {
        return $this->hasMany(IntervencionUnidad::class, 'intervencion_id', 'id');
    }

    public function leads()
    {
        return $this->hasMany(IntervencionLead::class, 'intervencion_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }

    /**
     * Registra una intervención y la vincula a una unidad productiva en un solo paso.
     */
    public static function registrarParaUnidad($unidadId, array $datos)
    {
        return DB::transaction(function () use ($unidadId, $datos) {
            $now = now();
            $intervencion = self::create([
                'asesor_id'      => $datos['asesor_id'] ?? Auth::id(),
                'programa_id'    => $datos['programa_id'] ?? null,
                'convocatoria_id' => $datos['convocatoria_id'] ?? null,
                'fase_id'        => $datos['fase_id'] ?? null,
                'categoria_id'   => $datos['categoria_id'] ?? IntervencionCategoria::CATEGORIA_GESTION_PROGRAMAS,
                'tipo_id'        => $datos['tipo_id'] ?? IntervencionTipo::TIPO_WHATSAPP,
                'descripcion'    => $datos['descripcion'],
                'fecha_inicio'   => $datos['fecha_inicio'] ?? $now,
                'fecha_fin'      => $datos['fecha_fin'] ?? $now,
                'modalidad'      => $datos['modalidad'] ?? 'Virtual',
                'cant_unidades'  => 1,
                'participantes'  => $datos['participantes'] ?? 1,
                'conclusiones'   => $datos['conclusiones'] ?? null,
            ]);
            IntervencionUnidad::create([
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
