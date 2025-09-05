<?php

namespace App\Models\Diagnosticos;

use App\Models\TablasReferencias\Etapa;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnostico extends Model
{
    use SoftDeletes, UserTrait;

    // Nombre de la tabla
    protected $table = 'diagnosticos';

    // Clave primaria personalizada
    protected $primaryKey = 'diagnostico_id';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'diagnostico_nombre',
        'diagnostico_etapa_id',
        'diagnostico_conventas'
    ];

    public function preguntas()
    {
        return $this->hasMany(DiagnosticoPregunta::class, 'diagnostico_id', 'diagnostico_id');
    }

    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'diagnostico_etapa_id', 'etapa_id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
