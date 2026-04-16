<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model {

    use SoftDeletes;
    protected $table = 'leads';

    protected $fillable = [
        'type',
        'name',
        'document',
        'email',
        'phone',
        'description',
        'department',
        'municipality',
        'address'
    ];

    public function intervencionesIndividuales() {
        return $this->hasMany(
            \App\Models\Intervenciones\IntervencionIndividual::class,
            'lead_id',
            'id'
        );
    }
}
