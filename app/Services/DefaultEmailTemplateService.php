<?php

namespace App\Services;

use App\Models\DefaultEmailTemplate;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;

class DefaultEmailTemplateService
{
    /**
     * Obtener plantilla activa para un tipo de proceso
     */
    public static function getActiveTemplate(string $processType): ?EmailTemplate
    {
        try {
            return DefaultEmailTemplate::getActiveTemplate($processType);
        } catch (\Exception $e) {
            Log::error("Error al obtener plantilla activa para {$processType}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener plantilla de recuperación de contraseña
     */
    public static function getPasswordResetTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('password_reset');
    }

    /**
     * Obtener plantilla de bienvenida
     */
    public static function getWelcomeTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('welcome');
    }

    /**
     * Obtener plantilla de verificación de cuenta
     */
    public static function getAccountVerificationTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('account_verification');
    }

    /**
     * Obtener plantilla de notificación general
     */
    public static function getNotificationTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('notification');
    }

    /**
     * Obtener plantilla de recordatorio
     */
    public static function getReminderTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('reminder');
    }

    /**
     * Obtener plantilla de invitación
     */
    public static function getInvitationTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('invitation');
    }

    /**
     * Obtener plantilla de confirmación
     */
    public static function getConfirmationTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('confirmation');
    }

    /**
     * Obtener plantilla de alerta
     */
    public static function getAlertTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('alert');
    }

    /**
     * Obtener plantilla de boletín
     */
    public static function getNewsletterTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('newsletter');
    }

    /**
     * Obtener plantilla personalizada
     */
    public static function getCustomTemplate(): ?EmailTemplate
    {
        return self::getActiveTemplate('custom');
    }

    /**
     * Verificar si existe una plantilla activa para un tipo de proceso
     */
    public static function hasActiveTemplate(string $processType): bool
    {
        return DefaultEmailTemplate::hasActiveTemplate($processType);
    }

    /**
     * Obtener todas las plantillas activas
     */
    public static function getAllActiveTemplates(): array
    {
        $activeTemplates = [];
        $processTypes = DefaultEmailTemplate::getAvailableProcessTypes();

        foreach ($processTypes as $processType => $processName) {
            $template = self::getActiveTemplate($processType);
            if ($template) {
                $activeTemplates[$processType] = [
                    'name' => $processName,
                    'template' => $template
                ];
            }
        }

        return $activeTemplates;
    }

    /**
     * Obtener resumen de configuración de plantillas por defecto
     */
    public static function getConfigurationSummary(): array
    {
        $summary = [];
        $processTypes = DefaultEmailTemplate::getAvailableProcessTypes();

        foreach ($processTypes as $processType => $processName) {
            $hasTemplate = self::hasActiveTemplate($processType);
            $template = $hasTemplate ? self::getActiveTemplate($processType) : null;

            $summary[$processType] = [
                'name' => $processName,
                'configured' => $hasTemplate,
                'template_name' => $template ? $template->name : null,
                'template_id' => $template ? $template->id : null,
                'status' => $hasTemplate ? 'active' : 'not_configured'
            ];
        }

        return $summary;
    }

    /**
     * Validar configuración de plantillas críticas
     */
    public static function validateCriticalTemplates(): array
    {
        $criticalTypes = ['password_reset', 'welcome', 'account_verification'];
        $validation = [
            'all_configured' => true,
            'missing_templates' => [],
            'warnings' => []
        ];

        foreach ($criticalTypes as $processType) {
            if (!self::hasActiveTemplate($processType)) {
                $validation['all_configured'] = false;
                $validation['missing_templates'][] = $processType;
            }
        }

        // Verificar si hay plantillas sin configurar
        $allProcessTypes = array_keys(DefaultEmailTemplate::getAvailableProcessTypes());
        $configuredTypes = array_keys(self::getAllActiveTemplates());
        $unconfiguredTypes = array_diff($allProcessTypes, $configuredTypes);

        if (!empty($unconfiguredTypes)) {
            $validation['warnings'][] = 'Hay tipos de proceso sin plantillas configuradas: ' . implode(', ', $unconfiguredTypes);
        }

        return $validation;
    }

    /**
     * Obtener plantilla con fallback
     */
    public static function getTemplateWithFallback(string $processType, ?EmailTemplate $fallbackTemplate = null): ?EmailTemplate
    {
        // Intentar obtener la plantilla por defecto
        $defaultTemplate = self::getActiveTemplate($processType);
        
        if ($defaultTemplate) {
            return $defaultTemplate;
        }

        // Si no hay plantilla por defecto y se proporciona un fallback, usarlo
        if ($fallbackTemplate) {
            Log::warning("Usando plantilla de fallback para {$processType} - ID: {$fallbackTemplate->id}");
            return $fallbackTemplate;
        }

        // Si no hay plantilla por defecto ni fallback, registrar error
        Log::error("No se encontró plantilla para {$processType} y no se proporcionó fallback");
        return null;
    }

    /**
     * Obtener plantilla de recuperación de contraseña con fallback
     */
    public static function getPasswordResetTemplateWithFallback(?EmailTemplate $fallbackTemplate = null): ?EmailTemplate
    {
        return self::getTemplateWithFallback('password_reset', $fallbackTemplate);
    }

    /**
     * Obtener plantilla de bienvenida con fallback
     */
    public static function getWelcomeTemplateWithFallback(?EmailTemplate $fallbackTemplate = null): ?EmailTemplate
    {
        return self::getTemplateWithFallback('welcome', $fallbackTemplate);
    }

    /**
     * Obtener plantilla de verificación de cuenta con fallback
     */
    public static function getAccountVerificationTemplateWithFallback(?EmailTemplate $fallbackTemplate = null): ?EmailTemplate
    {
        return self::getTemplateWithFallback('account_verification', $fallbackTemplate);
    }
}
