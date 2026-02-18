<?php

namespace App\Models;

use App\Models\Empresarios\UnidadProductiva;
use Illuminate\Database\Eloquent\Model;

class UnidadProductivaWhatsappLog extends Model
{
    protected $table = 'unidad_productiva_whatsapp_logs';

    protected $fillable = [
        'unidadproductiva_id',
        'user_id',
        'phone',
        'phone_type',
        'message',
        'status',
        'provider_response',
        'error_message',
    ];

    protected $casts = [
        'provider_response' => 'array',
    ];

    public function unidadProductiva()
    {
        return $this->belongsTo(UnidadProductiva::class, 'unidadproductiva_id', 'unidadproductiva_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
