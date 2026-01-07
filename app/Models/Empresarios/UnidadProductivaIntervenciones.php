<?php

namespace App\Models\Empresarios;

use App\Models\User;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\TablasReferencias\CategoriasIntervenciones;
use App\Models\TablasReferencias\TiposIntervenciones;

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

        'categoria_id',
        'tipo_id',
        'referencia_id',
        'modalidad',
        'participantes',
        'conclusiones',
    ];

    // Relaciones
    public function unidadProductiva()
    {
        return $this->belongsTo(UnidadProductiva::class, 'unidadproductiva_id', 'unidadproductiva_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id', 'id');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriasIntervenciones::class, 'categoria_id', 'id');
    }

    public function tipo()
    {
        return $this->belongsTo(TiposIntervenciones::class, 'tipo_id', 'id');
    }

    public static $modalidades = [
        'Presencial' => 'Presencial',
        'Virtual' => 'Virtual',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
