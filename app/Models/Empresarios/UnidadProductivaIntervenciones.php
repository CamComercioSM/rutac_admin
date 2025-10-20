<?php

namespace App\Models\Empresarios;

use App\Models\User;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UnidadProductivaIntervenciones extends Model
{
    use SoftDeletes, UserTrait;

    protected $table = 'unidadesproductivas_intervenciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'asesor_id',
        'unidadproductiva_id',
        'descripcion',
        'soporte',
        'fecha_inicio',
        'fecha_fin',
    ];

    // Relaciones
    public function unidadProductiva()
    {
        return $this->belongsTo(unidadProductiva::class, 'unidadproductiva_id', 'unidadproductiva_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id', 'id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
