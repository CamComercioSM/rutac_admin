<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UserTrait;

class Historia extends Model {
    use HasFactory, UserTrait;

    protected $table = 'histories';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'section_id',
        'name',
        'video_url',
        'image',
    ];
}
