<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $table = 'whatsapp_templates';

    protected $fillable = [
        'category_id',
        'name',
        'group_code',
        'channel',
        'provider',
        'language',
        'expected_fields',
        'default_payload',
        'is_active',
    ];

    protected $casts = [
        'expected_fields' => 'array',
        'default_payload' => 'array',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(WhatsappTemplateCategory::class, 'category_id');
    }

    public function messageLogs()
    {
        return $this->hasMany(WhatsappMessageLog::class, 'template_id');
    }

    /**
     * Get required keys from expected_fields (where required === true).
     */
    public function getRequiredFieldKeys(): array
    {
        if (! is_array($this->expected_fields)) {
            return [];
        }
        $keys = [];
        foreach ($this->expected_fields as $item) {
            if (is_array($item) && ! empty($item['key'])) {
                if (isset($item['required']) && $item['required']) {
                    $keys[] = $item['key'];
                }
            }
        }
        return $keys;
    }
}
