<?php

namespace App\Models\Programas;

use App\Models\TablasReferencias\Sector;
use App\Models\Traits\UserTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramaConvocatoria extends Model
{
    use SoftDeletes, UserTrait;

    protected $table = 'programas_convocatorias';
    protected $primaryKey = 'convocatoria_id';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'programa_id',
        'nombre_convocatoria',
        'persona_encargada',
        'correo_contacto',
        'telefono',
        'fecha_apertura_convocatoria',
        'fecha_cierre_convocatoria',
        'con_matricula',
        'sector_id',
    ];

    // Definición de constantes para los timestamps personalizados
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';

    /**
     * Relación con el modelo Programa.
     */
    public function programa(): BelongsTo
    {
        return $this->belongsTo(Programa::class, 'programa_id', 'programa_id');
    }

    public function asesores()
    {
        return $this->belongsToMany(User::class, 'convocatorias_asesores', 'convocatoria_id', 'user_id');
    }

    
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sector_id', 'sector_id');
    }

    public function requisitos()
    {
        return $this->belongsToMany(
            InscripcionesRequisitos::class, 
            'convocatorias_requisitos', 
            'convocatoria_id', 
            'requisito_id')
            ->whereNull('indicador_id');
    }

    public function requisitosIndicadores()
    {
        return $this->belongsToMany(
            InscripcionesRequisitos::class, 
            'convocatorias_requisitos', 
            'convocatoria_id', 
            'requisito_id')
            ->withPivot('referencia')     
            ->whereNotNull('indicador_id'); 
    }

}
