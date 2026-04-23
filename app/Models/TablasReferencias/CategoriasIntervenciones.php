<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\Model;

class CategoriasIntervenciones extends Model
{
    protected $table = 'intervenciones_categorias';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre'
    ];
}
