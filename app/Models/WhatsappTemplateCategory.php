<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplateCategory extends Model
{
    protected $table = 'whatsapp_template_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function templates()
    {
        return $this->hasMany(WhatsappTemplate::class, 'category_id');
    }
}
