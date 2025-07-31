<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PreguntaDimension extends Model
{
    use SoftDeletes;
    
    protected $table = 'preguntas_dimensiones';
    protected $primaryKey = 'preguntadimension_id';

    protected $fillable = [
        'preguntadimension_nombre'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
