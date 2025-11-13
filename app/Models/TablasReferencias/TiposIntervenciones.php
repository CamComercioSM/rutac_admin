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
}
