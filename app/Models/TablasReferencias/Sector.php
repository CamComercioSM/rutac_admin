<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'ciiu_macrosectores';

    protected $primaryKey = 'sector_id';

    protected $fillable = [
        'sectorCODIGO',
        'sectorNOMBRE'
    ];
}
