<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\UserTrait;

class Section  extends Model {
    use HasFactory, UserTrait;

    protected $table = 'sections';
    protected $primaryKey = 'id';

    protected $casts = [
        'data' => 'array',
    ];
    
    protected $fillable = [
        'tag',
        'h1',
        'video_url',
        'seo_title',
        'seo_description',
        'data'
    ];

    protected function historia(): Attribute
    {
        return Attribute::get(function () {
            $data = $this->data ?? [];
            return collect($data)
                ->first(fn($item) => strtolower($item['layout'] ?? '') === 'histories')['attributes'] ?? [];
        });
    }

    protected function footer(): Attribute
    {
        return Attribute::get(function () {
            $data = $this->data ?? [];
            return collect($data)
                ->first(fn($item) => strtolower($item['layout'] ?? '') === 'footer')['attributes'] ?? [];
        });
    }

}
