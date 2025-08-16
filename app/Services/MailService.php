<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Enviar correo básico
     */
    public function send($to, $subject, $view, $data = [])
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            
            Log::info('Correo enviado exitosamente', [
                'to' => $to,
                'subject' => $subject,
                'template' => $view
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error al enviar correo', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Enviar correo de recuperación de contraseña
     */
    public function sendPasswordReset($email, $userName, $resetUrl, $projectName = 'Sistema')
    {
        $data = [
            'user_name' => $userName,
            'reset_url' => $resetUrl,
            'project_name' => $projectName,
            'expires_at' => now()->addHours(24)->format('d/m/Y H:i:s'),
            'support_email' => config('mail.from.address'),
            'current_year' => date('Y')
        ];

        $subject = "Recuperación de Contraseña - {$projectName}";

        return $this->send($email, $subject, 'emails.password-reset', $data);
    }

    /**
     * Enviar correo personalizado con opciones avanzadas
     */
    public function sendCustomEmail($emailData)
    {
        try {
            Mail::send($emailData['template'], $emailData['data'], function ($message) use ($emailData) {
                $message->to($emailData['to'])
                        ->subject($emailData['subject']);
                
                // Agregar CC si existe
                if (!empty($emailData['cc'])) {
                    $message->cc($emailData['cc']);
                }
                
                // Agregar BCC si existe
                if (!empty($emailData['bcc'])) {
                    $message->bcc($emailData['bcc']);
                }
            });
            
            Log::info('Correo personalizado enviado exitosamente', [
                'to' => $emailData['to'],
                'subject' => $emailData['subject'],
                'template' => $emailData['template'],
                'cc' => $emailData['cc'] ?? [],
                'bcc' => $emailData['bcc'] ?? []
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error al enviar correo personalizado', [
                'to' => $emailData['to'],
                'subject' => $emailData['subject'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Enviar correo con archivos adjuntos
     */
    public function sendWithAttachments($to, $subject, $view, $data, $attachments = [])
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject, $attachments) {
                $message->to($to)->subject($subject);
                
                // Agregar archivos adjuntos
                foreach ($attachments as $attachment) {
                    if (is_array($attachment)) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'] ?? basename($attachment['path']),
                            'mime' => $attachment['mime'] ?? null
                        ]);
                    } else {
                        $message->attach($attachment);
                    }
                }
            });
            
            Log::info('Correo con adjuntos enviado exitosamente', [
                'to' => $to,
                'subject' => $subject,
                'attachments_count' => count($attachments)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error al enviar correo con adjuntos', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verificar si el servicio está configurado correctamente
     */
    public function isConfigured()
    {
        try {
            $config = config('mail');
            return !empty($config['default']) && 
                   !empty($config['from']['address']) && 
                   !empty($config['from']['name']);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Enviar email de prueba desde el constructor de plantillas
     */
    public function sendTestEmail($to, $subject, $htmlContent, $textContent = null)
    {
        try {
            // Crear una vista temporal para el email
            $viewData = [
                'htmlContent' => $htmlContent,
                'textContent' => $textContent,
                'subject' => $subject
            ];

            // Enviar email HTML
            Mail::send('emails.test-template', $viewData, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            // Si hay contenido de texto plano, enviar también
            if ($textContent) {
                Mail::send('emails.test-template-text', $viewData, function ($message) use ($to, $subject) {
                    $message->to($to)
                            ->subject($subject . ' (Texto Plano)')
                            ->from(config('mail.from.address'), config('mail.from.name'));
                });
            }
            
            Log::info('Email de prueba enviado exitosamente', [
                'to' => $to,
                'subject' => $subject,
                'has_html' => !empty($htmlContent),
                'has_text' => !empty($textContent)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error al enviar email de prueba', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
