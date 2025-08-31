<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class EmailTemplateService
{
    /**
     * Enviar correo usando una plantilla
     */
    public function sendEmail(string $templateName, string $to, array $data = [], array $options = [])
    {
        $template = EmailTemplate::where('name', $templateName)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            throw new \Exception("Plantilla '{$templateName}' no encontrada o inactiva");
        }

        // Renderizar contenido con las variables
        $htmlContent = $this->renderTemplate($template->html_content, $data);
        $textContent = $template->text_content ? $this->renderTemplate($template->text_content, $data) : null;
        $subject = $this->renderTemplate($template->subject, $data);

        // Enviar correo
        Mail::send([], [], function ($message) use ($to, $subject, $htmlContent, $textContent, $options) {
            $message->to($to)
                    ->subject($subject)
                    ->html($htmlContent);

            if ($textContent) {
                $message->text($textContent);
            }

            // Opciones adicionales
            if (isset($options['from'])) {
                $message->from($options['from']);
            }

            if (isset($options['cc'])) {
                $message->cc($options['cc']);
            }

            if (isset($options['bcc'])) {
                $message->bcc($options['bcc']);
            }

            if (isset($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    if (is_array($attachment)) {
                        $message->attach($attachment['path'], $attachment['options'] ?? []);
                    } else {
                        $message->attach($attachment);
                    }
                }
            }
        });

        return true;
    }

    /**
     * Renderizar una plantilla con variables
     */
    public function renderTemplate(string $content, array $data = []): string
    {
        $rendered = $content;

        foreach ($data as $key => $value) {
            $rendered = str_replace('{{ $' . $key . ' }}', $value, $rendered);
        }

        return $rendered;
    }

    /**
     * Obtener una plantilla por nombre
     */
    public function getTemplate(string $templateName): ?EmailTemplate
    {
        return EmailTemplate::where('name', $templateName)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Previsualizar una plantilla con datos de ejemplo
     */
    public function previewTemplate(string $templateName, array $customData = []): array
    {
        $template = $this->getTemplate($templateName);

        if (!$template) {
            throw new \Exception("Plantilla '{$templateName}' no encontrada o inactiva");
        }

        $sampleData = $this->getSampleData($template, $customData);

        return [
            'subject' => $this->renderTemplate($template->subject, $sampleData),
            'html' => $this->renderTemplate($template->html_content, $sampleData),
            'text' => $template->text_content ? $this->renderTemplate($template->text_content, $sampleData) : null,
        ];
    }

    /**
     * Obtener datos de ejemplo para previsualización
     */
    private function getSampleData(EmailTemplate $template, array $customData = []): array
    {
        $defaultData = [
            'business_name' => 'Mi Empresa S.A.S.',
            'contact_person' => 'Juan Pérez',
            'project_name' => 'Ruta C',
            'current_year' => date('Y'),
        ];

        return array_merge($defaultData, $customData);
    }

    /**
     * Validar que todas las variables requeridas estén presentes
     */
    public function validateVariables(string $templateName, array $data): array
    {
        $template = $this->getTemplate($templateName);

        if (!$template) {
            throw new \Exception("Plantilla '{$templateName}' no encontrada o inactiva");
        }

        $requiredVariables = $template->variables ?? [];
        $missingVariables = [];

        foreach ($requiredVariables as $variable) {
            if (!isset($data[$variable]) || empty($data[$variable])) {
                $missingVariables[] = $variable;
            }
        }

        return [
            'valid' => empty($missingVariables),
            'missing' => $missingVariables,
            'required' => $requiredVariables,
        ];
    }
}
