<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'leads';

    protected $fillable = [
        'id',
        'type',
        'name',
        'document',
        'email',
        'phone',
        'description',
        'department',
        'municipality',
        'dress',
        'created_at',
        'updated_at',
    ];
}
