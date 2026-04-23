<?php

namespace App\Models\Intervenciones;

use App\Models\RutaCModel;

class IntervencionTipo extends RutaCModel {
    
    const TIPO_GESTION_INSCRIPCIONES = 1;
    const TIPO_WHATSAPP = 2;
    const TIPO_LLAMADA = 3;
    const TIPO_CORREO = 4;

    protected $table = 'intervenciones_tipos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
        'requiere_evidencia',
    ];

    protected $casts = [
        'requiere_evidencia' => 'boolean',
    ];
}
