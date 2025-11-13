<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\Model;

class CategoriasIntervenciones extends Model
{
    protected $table = 'categorias_intervenciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre'
    ];
}
