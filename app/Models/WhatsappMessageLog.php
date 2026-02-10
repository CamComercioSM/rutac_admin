<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessageLog extends Model
{
    protected $table = 'whatsapp_message_logs';

    protected $fillable = [
        'template_id',
        'user_id',
        'phone',
        'template_name',
        'template_group',
        'status',
        'payload',
        'provider_message_id',
        'provider_response',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'provider_response' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
