<?php

namespace App\Models\Inscripciones;

use App\Models\TablasReferencias\InscripcionEstado;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConvocatoriaInscripcionHistorial extends Model
{
    use HasFactory, SoftDeletes, UserTrait;

    protected $table = 'convocatorias_inscripciones_historial';
    protected $primaryKey = 'historial_id';

    protected $fillable = [
        'inscripcion_id',
        'inscripcionestado_id',
        'comentarios',
        'archivo',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    protected static function booted()
    {
        static::creating(function ($model) {
            // Asegurar que las fechas se guarden en timezone de Colombia
            $timezone = 'America/Bogota';
            if (!isset($model->fecha_creacion)) {
                $model->fecha_creacion = now($timezone);
            }
        });

        static::updating(function ($model) {
            // Asegurar que las fechas de actualización se guarden en timezone de Colombia
            $timezone = 'America/Bogota';
            $model->fecha_actualizacion = now($timezone);
        });
    }

    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime:Y-m-d H:i:s',
            'fecha_actualizacion' => 'datetime:Y-m-d H:i:s',
            'fecha_eliminacion' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function inscripcion()
    {
        return $this->belongsTo(ConvocatoriaInscripcion::class, 'inscripcion_id', 'inscripcion_id');
    }

    public function estado()
    {
        return $this->belongsTo(InscripcionEstado::class, 'inscripcionestado_id', 'inscripcionestado_id');
    }

    /**
     * Accessor para fecha_creacion - siempre retorna en timezone de Colombia
     */
    protected function fechaCreacion(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? \Carbon\Carbon::parse($value)->setTimezone('America/Bogota') : null,
        );
    }

    /**
     * Accessor para fecha_actualizacion - siempre retorna en timezone de Colombia
     */
    protected function fechaActualizacion(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? \Carbon\Carbon::parse($value)->setTimezone('America/Bogota') : null,
        );
    }

    /**
     * Accessor para archivo - siempre retorna URL absoluta
     */
    protected function archivo(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                
                // Si ya es una URL completa (http o https), retornarla tal cual
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }
                
                // Si es una ruta relativa, agregar ARCHIVOS_URL
                $archivosUrl = config('app.archivos_url');
                if ($archivosUrl) {
                    return rtrim($archivosUrl, '/') . '/' . ltrim($value, '/');
                }
                
                // Fallback: retornar la ruta relativa
                return $value;
            }
        );
    }
}
