<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UserTrait;

class Banner extends Model {
    use HasFactory, UserTrait;

    protected $table = 'banners';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'name',
        'title',
        'image',
        'image_movil',
        'description',
        'text_button',
        'url'
    ];
}
