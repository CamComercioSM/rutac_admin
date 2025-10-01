<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model 
{
    protected $table = 'municipios';
    protected $primaryKey = 'municipio_id';
    
    protected $fillable = [
        'municipioNOMBREOFICIAL',
        'municipiocodigo',
        'departamento_id'
    ];
    
    // Relación con departamento
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id', 'departamento_id');
    }
    
    // Relación con unidades productivas
    public function unidadesProductivas()
    {
        return $this->hasMany(\App\Models\Empresarios\UnidadProductiva::class, 'municipality_id', 'municipio_id');
    }
}
