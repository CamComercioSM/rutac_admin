<?php

namespace App\Models\Programas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisitosOpciones extends Model
{
    use SoftDeletes;
    protected $table = 'requisitos_opciones';

    protected $primaryKey = 'opcionrequisito_id';

    protected $fillable = [
        'requisito_id',
        'opcion_variable_response',
        'opcion_percentage',
    ];

    public function requisito()
    {
        return $this->belongsTo(InscripcionesRequisitos::class, 'requisito_id', 'requisito_id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
