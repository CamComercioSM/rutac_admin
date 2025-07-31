<?php

namespace App\Models\Crons;

use Illuminate\Database\Eloquent\Model;

class Cron extends Model
{
    protected $table = 'crons';

    protected $fillable = [
        'nombre',
        'descripcion',
        'periodicidad',
        'ruta'
    ];
}
