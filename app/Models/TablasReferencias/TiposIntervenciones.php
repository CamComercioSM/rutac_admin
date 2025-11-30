<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\Model;

class TiposIntervenciones extends Model
{
    protected $table = 'tipos_intervenciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre'
    ];
    
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
