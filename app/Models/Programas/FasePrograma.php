<?php

namespace App\Models\Programas;

use App\Models\RutaCModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FasePrograma extends RutaCModel
{
    protected $table = 'fases_programas';
    protected $primaryKey = 'fase_id';

    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'orden' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_eliminacion' => 'datetime',
    ];

    const CONVOCATORIA = 1;
    const FORTALECIMIENTO = 2;
    const MEDICION = 3;
    const CIERRE = 4;
    const OTROS = 5;

    /**
     * Scope para obtener solo fases activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para ordenar por orden ascendente
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden', 'asc');
    }
}
