<?php

namespace App\Models\Intervenciones;

use App\Models\RutaCModel;

class IntervencionCategoria extends RutaCModel {

    protected $table = 'intervenciones_categorias';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
    ];

    const CATEGORIA_GESTION_PROGRAMAS = 1;
    const CATEGORIA_GESTION_CAMPANA = 2;
    const CATEGORIA_SOCIALIZACION = 3;
    const CATEGORIA_VINCULACION = 4;

}
