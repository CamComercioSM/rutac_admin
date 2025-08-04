<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\Model;

class CiiuActividad extends Model
{
    protected $table = 'ciiu_actividades';
    protected $primaryKey = 'ciiuactividad_id';

    // Define los campos asignables en el modelo
    protected $fillable = [
        'ciiuVIGENCIA',           // Vigencia de la actividad
        'ciiuActividadPADRE',      // ID de la actividad padre
        'ciiuActividadCODIGO',     // Código de la actividad
        'ciiuActividadTITULO',     // Título de la actividad
        'ciiuActividadDESCRIPCION', // Descripción de la actividad
        'ciiuSeccionID',           // ID de la sección CIIU
        'macroSectorID',           // ID del macro sector
    ];
}
