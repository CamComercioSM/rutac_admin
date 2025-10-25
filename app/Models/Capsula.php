<?php

namespace App\Models;

use App\Models\TablasReferencias\Etapa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UserTrait;

class Capsula extends Model
{
    use HasFactory, SoftDeletes, UserTrait;

    protected $table = 'capsulas';
    protected $primaryKey = 'capsula_id';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'url_video',
        'imagen',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function etapas()
    {
        return $this->belongsToMany(Etapa::class, 'capsulas_etapas', 'capsula_id', 'etapa_id');
    }
}
