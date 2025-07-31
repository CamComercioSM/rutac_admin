<?php

namespace App\Models\Crons;

use Illuminate\Database\Eloquent\Model;

class CronLog extends Model
{
    protected $table = 'historico_ejecucion_cron';

    protected $fillable = [
        'nombre_tarea',
        'inicio_ejecucion',
        'fin_ejecucion',
        'estado',
        'mensaje',
    ];
}
