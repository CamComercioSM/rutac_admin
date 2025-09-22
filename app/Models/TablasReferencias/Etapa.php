<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etapa extends Model
{
    use SoftDeletes;

    protected $table = 'etapas';
    protected $primaryKey = 'etapa_id';

    protected $fillable = [
        'name',
        'image',
        'descripcion',
        'etapa_anterior_id',
        'etapa_siguiente_id',
        'score_inicial',
        'score_final'
    ];

    // Relaciones
    public function etapaAnterior()
    {
        return $this->belongsTo(Etapa::class, 'etapa_anterior_id');
    }

    public function etapaSiguiente()
    {
        return $this->belongsTo(Etapa::class, 'etapa_siguiente_id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
