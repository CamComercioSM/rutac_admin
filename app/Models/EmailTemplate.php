<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'html_content',
        'text_content',
        'variables',
        'is_active',
        'description'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function getVariablesListAttribute()
    {
        return $this->variables ?? [];
    }

    public function render($data = [])
    {
        $content = $this->html_content;
        
        foreach ($data as $key => $value) {
            $content = str_replace('{{ $' . $key . ' }}', $value, $content);
        }
        
        return $content;
    }
}
