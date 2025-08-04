<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\Model;

class SectorSecciones extends Model
{
    protected $table = 'ciiu_secciones';

    protected $primaryKey = 'ciiuSeccionID';

    protected $fillable = [
        'macroSectorID',
        'ciiuSeccionCODIGO',
        'ciiuSeccionTITULO'
    ];
}
