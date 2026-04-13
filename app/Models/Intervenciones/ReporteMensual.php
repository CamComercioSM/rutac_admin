<?php

namespace App\Models\Intervenciones;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReporteMensual extends Model {
    use SoftDeletes;

    protected $table = 'reportes_mensuales';

    protected $fillable = [
        'asesor_id',
        'anio',
        'mes',
        'total_intervenciones',
        'total_unidades',
        'estado',
        'conclusiones',
        'observaciones_supervisor',
        'supervisor_id',
        'fecha_generacion',
        'fecha_revision',
        'informe_url',
        'hash_consolidado',
        'fecha_creacion',
        'fecha_actualizacion',
        'fecha_eliminacion',
        'usuario_creo',
        'usuario_actualizo',
        'usuario_elimino',
        'meta_intervenciones',
        'avance_meta'
    ];

    public function asesor() {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function supervisor() {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
