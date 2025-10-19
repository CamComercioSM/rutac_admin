<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UserTrait;

class Link extends Model {
    use HasFactory, UserTrait;

    protected $table = 'links';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'section_id',
        'name',
        'type',
        'value',
    ];
}
