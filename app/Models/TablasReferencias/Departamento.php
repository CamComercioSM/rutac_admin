<?php

namespace App\Models\TablasReferencias;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model 
{
    protected $table = 'departamentos';
    protected $primaryKey = 'departamento_id';
    
    protected $fillable = [
        'departamentoNOMBRE',
        'departamentocodigo'
    ];
    
    // Relación con municipios
    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'departamento_id', 'departamento_id');
    }
    
    // Relación con unidades productivas
    public function unidadesProductivas()
    {
        return $this->hasMany(\App\Models\Empresarios\UnidadProductiva::class, 'department_id', 'departamento_id');
    }
}
