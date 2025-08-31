<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefaultEmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_type',
        'name',
        'description',
        'email_template_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relación con la plantilla de email
     */
    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    /**
     * Obtener plantilla activa para un tipo de proceso
     */
    public static function getActiveTemplate(string $processType): ?EmailTemplate
    {
        $defaultTemplate = self::where('process_type', $processType)
            ->where('is_active', true)
            ->with('emailTemplate')
            ->first();

        return $defaultTemplate?->emailTemplate;
    }

    /**
     * Verificar si existe una plantilla activa para un tipo de proceso
     */
    public static function hasActiveTemplate(string $processType): bool
    {
        return self::where('process_type', $processType)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Obtener todos los tipos de procesos disponibles
     */
    public static function getAvailableProcessTypes(): array
    {
        return [
            'password_reset' => 'Recuperación de Contraseña',
            'welcome' => 'Bienvenida',
            'account_verification' => 'Verificación de Cuenta',
            'notification' => 'Notificación General',
            'reminder' => 'Recordatorio',
            'invitation' => 'Invitación',
            'confirmation' => 'Confirmación',
            'alert' => 'Alerta',
            'newsletter' => 'Boletín',
            'custom' => 'Personalizado'
        ];
    }

    /**
     * Obtener plantilla por defecto para recuperación de contraseña
     */
    public static function getPasswordResetTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('password_reset');
    }

    /**
     * Obtener plantilla por defecto para bienvenida
     */
    public static function getWelcomeTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('welcome');
    }

    /**
     * Obtener plantilla por defecto para verificación de cuenta
     */
    public static function getAccountVerificationTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('account_verification');
    }
}
