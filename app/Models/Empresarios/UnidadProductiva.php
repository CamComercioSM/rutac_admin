<?php

namespace App\Models\Empresarios;

use App\Models\Diagnosticos\DiagnosticoResultado;
use App\Models\Inscripciones\ConvocatoriaInscripcion;
use App\Models\TablasReferencias\CiiuActividad;
use App\Models\TablasReferencias\Departamento;
use App\Models\TablasReferencias\Etapa;
use App\Models\TablasReferencias\Municipio;
use App\Models\TablasReferencias\Sector;
use App\Models\TablasReferencias\UnidadProductivaPersona;
use App\Models\TablasReferencias\UnidadProductivaTamano;
use App\Models\TablasReferencias\UnidadProductivaTipo;
use App\Models\TablasReferencias\VentasAnuales;
use App\Models\Traits\UserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UnidadProductiva extends Model
{
    use SoftDeletes, UserTrait;

    protected $table = 'unidadesproductivas';
    protected $primaryKey = 'unidadproductiva_id';

    protected $fillable = [
        'business_name',
        'description',
        'nit',
        'registration_number',
        'registration_date',
        'registration_email',
        'name_legal_representative',
        'size',
        'tipopersona_id',
        'type_person',
        'sector_id',
        'sector',
        'ventaanual_id',
        'anual_sales',
        'ciiuactividad_id',
        'comercial_activity',
        'tamano_id',
        'affiliated',
        'department_id',
        'department_viejo',
        'municipality_id',
        'municipality_viejo',
        'address',
        'geolocation',
        'telephone',
        'mobile',
        'website',
        'social_instagram',
        'social_facebook',
        'social_linkedin',
        'contact_person',
        'contact_position',
        'contact_email',
        'contact_phone',
        'etapa_id',
        'update_info',
        'complete_diagnostic',
        'show_poll',
        'logo',
        'user_id',
        'camara_comercio',
        'unidadtipo_id',
        'tipo_registro_rutac',
        'tipo_identificacion',
        'identificacion',
        'etapa_intervencion',
        'transformada_fecha',
        'transformada_en',
        'transformada_desde',
    ];

    protected $casts = [
        'registration_date' => 'date',
    ];

    // Relaciones
    
    public function tipoPersona()
    {
        return $this->belongsTo(UnidadProductivaPersona::class, 'tipopersona_id');
    }

    public function sectorUnidad()
    {
        return $this->belongsTo(Sector::class, 'sector_id', 'sector_id');
    }

    public function ventaAnual()
    {
        return $this->belongsTo(VentasAnuales::class, 'ventaanual_id', 'ventasAnualesID');
    }

    public function ciiuActividad()
    {
        return $this->belongsTo(CiiuActividad::class, 'ciiuactividad_id');
    }

    public function tamano()
    {
        return $this->belongsTo(UnidadProductivaTamano::class, 'tamano_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'department_id', 'departamento_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipality_id', 'municipio_id');
    }

    public function etapa()
    {
        return $this->belongsTo(Etapa::class, 'etapa_id');
    }

    public function tipoUnidad()
    {
        return $this->belongsTo(UnidadProductivaTipo::class, 'unidadtipo_id');
    }

    public function diagnosticos()
    {
        return $this->HasMany(DiagnosticoResultado::class, 'unidadproductiva_id', 'unidadproductiva_id');
    }
    

    public function inscripciones()
    {
        return $this->HasMany(ConvocatoriaInscripcion::class, 'unidadproductiva_id', 'unidadproductiva_id');
    }

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    const DELETED_AT = 'fecha_eliminacion';
}
