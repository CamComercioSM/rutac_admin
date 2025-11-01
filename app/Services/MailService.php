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

    /**
     * Enviar correo con HTML personalizado (para uso desde API externa)
     */
    public function sendRawHtml($to, $subject, $htmlContent, $options = [])
    {
        try {
            Mail::send([], [], function ($message) use ($to, $subject, $htmlContent, $options) {
                $message->to($to)
                        ->subject($subject)
                        ->html($htmlContent)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                
                // Agregar CC si existe
                if (!empty($options['cc'])) {
                    $message->cc($options['cc']);
                }
                
                // Agregar BCC si existe
                if (!empty($options['bcc'])) {
                    $message->bcc($options['bcc']);
                }
                
                // Agregar Reply-To si existe
                if (!empty($options['reply_to'])) {
                    $message->replyTo($options['reply_to']);
                }
            });
            
            Log::info('Correo HTML personalizado enviado exitosamente', [
                'to' => $to,
                'subject' => $subject,
                'has_html' => !empty($htmlContent)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error al enviar correo HTML personalizado', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Enviar correo de prueba con template personalizado
     * Permite probar diferentes tipos de correos antes de integrarlos en los flujos
     */
    public function sendTestTemplate($to, $templateType, $data = [], $options = [])
    {
        try {
            // Determinar el template y subject según el tipo
            $templateInfo = $this->getTemplateInfo($templateType, $data);
            
            // Renderizar el contenido del template con los datos proporcionados
            $htmlContent = $this->renderTemplate($templateInfo['html'], $data);
            $subject = $this->renderTemplate($templateInfo['subject'], $data);
            
            // Enviar el correo
            return $this->sendRawHtml($to, $subject, $htmlContent, $options);
            
        } catch (\Exception $e) {
            Log::error('Error al enviar correo de prueba con template', [
                'to' => $to,
                'template_type' => $templateType,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener información del template según el tipo
     * Aquí se irán agregando nuevos tipos de correos según se necesiten
     */
    private function getTemplateInfo($templateType, $data = [])
    {
        // Usar zona horaria de Bogotá
        $timezone = 'America/Bogota';
        $now = now($timezone);
        $currentYear = $now->format('Y');
        $projectName = $data['project_name'] ?? 'Ruta C';
        
        // Template base con header y footer
        $header = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . ($data['subject'] ?? 'Correo de Prueba') . '</title>
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8f9fa;">
            <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); padding: 40px 20px 30px; text-align: center; position: relative;">
                    <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 30px; margin-bottom: 25px;">
                        <div style="width: 180px; height: 100px; background-color: transparent; display: flex; align-items: center; justify-content: center;">
                            <img src="https://cdnsicam.net/img/rutac/rutac_blanco.png" alt="Ruta C Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <p style="color: #ffffff; text-align: left; font-size: 20px; font-weight: 600; margin: 0; opacity: 0.9;">Haz crecer tu negocio</p>
                    </div>
                </div>
                <div style="padding: 40px 30px; background-color: #ffffff;">';
        
        $footer = '
                </div>
                <div style="background-color: #1e3a8a; color: #ffffff; text-align: center; padding: 25px 30px; font-size: 14px;">
                    <p style="margin: 5px 0; color: #cbd5e1;">Este es un correo de prueba del sistema de plantillas.</p>
                    <p style="color: #94a3b8; font-size: 12px;">&copy; ' . $currentYear . ' ' . $projectName . '. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>';
        
        // Definir templates por tipo
        $templates = [
            'test' => [
                'subject' => 'Correo de Prueba - ' . $projectName,
                'html' => $header . '
                    <h2 style="color: #1e3a8a; font-size: 24px; margin-bottom: 20px;">Correo de Prueba</h2>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
                        Este es un correo de prueba del sistema. Si recibes este mensaje, significa que el servicio de correos está funcionando correctamente.
                    </p>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6;">
                        Fecha y hora de envío: ' . $now->format('d/m/Y H:i:s') . ' (Hora de Bogotá)
                    </p>
                ' . $footer
            ],
            'inscripcion_programa' => [
                'subject' => 'Bienvenido al programa {{ $programa_nombre }} - ' . $projectName,
                'html' => $header . '
                    <h2 style="color: #1e3a8a; font-size: 24px; margin-bottom: 20px;">¡Bienvenido al programa {{ $programa_nombre }}!</h2>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
                        Estimado/a <strong>{{ $unidad_productiva_nombre }}</strong>,
                    </p>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
                        Nos complace informarle que su unidad productiva ha sido inscrita exitosamente en el programa <strong>{{ $programa_nombre }}</strong>, específicamente en la <strong>{{ $convocatoria_nombre }}</strong>, por parte de un asesor de Ruta C.
                    </p>
                    <div style="background-color: #f0f9ff; border-left: 4px solid #1e3a8a; padding: 20px; margin: 25px 0; border-radius: 4px;">
                        <h3 style="color: #1e3a8a; font-size: 18px; margin-top: 0; margin-bottom: 15px;">Detalles de su inscripción:</h3>
                        <table style="width: 100%; color: #4b5563; font-size: 15px;">
                            <tr style="margin-bottom: 10px;">
                                <td style="padding: 8px 0; font-weight: 600; width: 40%;">Programa:</td>
                                <td style="padding: 8px 0;">{{ $programa_nombre }}</td>
                            </tr>
                            <tr style="margin-bottom: 10px;">
                                <td style="padding: 8px 0; font-weight: 600;">Convocatoria:</td>
                                <td style="padding: 8px 0;">{{ $convocatoria_nombre }}</td>
                            </tr>
                            <tr style="margin-bottom: 10px;">
                                <td style="padding: 8px 0; font-weight: 600;">Asesor:</td>
                                <td style="padding: 8px 0;">{{ $asesor_nombre }}</td>
                            </tr>
                            <tr style="margin-bottom: 10px;">
                                <td style="padding: 8px 0; font-weight: 600;">Fecha de inscripción:</td>
                                <td style="padding: 8px 0;">{{ $fecha_inscripcion }}</td>
                            </tr>
                        </table>
                    </div>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
                        {{ $programa_nombre }} es una iniciativa diseñada para apoyar el crecimiento y desarrollo de su negocio. Estamos comprometidos en brindarle las herramientas y el acompañamiento necesario para alcanzar sus objetivos empresariales.
                    </p>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
                        En los próximos días, nuestro equipo se pondrá en contacto con usted para proporcionarle más información sobre los próximos pasos del programa y responder cualquier pregunta que pueda tener.
                    </p>
                    <div style="text-align: center; margin: 30px 0;">
                        <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 10px;">
                            <strong>¡Gracias por ser parte de ' . $projectName . '!</strong>
                        </p>
                        <p style="color: #6b7280; font-size: 14px; margin: 0;">
                            Estamos aquí para ayudarte a hacer crecer tu negocio.
                        </p>
                    </div>
                    <p style="color: #4b5563; font-size: 14px; line-height: 1.6; margin-top: 25px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                        Si tiene alguna pregunta o necesita asistencia, no dude en contactarnos. Estaremos encantados de ayudarle.
                    </p>
                ' . $footer
            ],
            'bienvenida_empresario' => [
                'subject' => '¡Bienvenido a Ruta C - Registro de Empresario!',
                'html' => $header . '
                    <h2 style="color: #1e3a8a; font-size: 24px; margin-bottom: 20px;">¡Bienvenido a Ruta C!</h2>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
                        Estimado/a <strong>{{ $nombre_completo }}</strong>,
                    </p>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
                        Nos complace informarle que ha sido registrado exitosamente como empresario en el sistema de Ruta C.
                    </p>
                    <div style="background-color: #f0f9ff; border-left: 4px solid #1e3a8a; padding: 20px; margin: 25px 0; border-radius: 4px;">
                        <h3 style="color: #1e3a8a; font-size: 18px; margin-top: 0; margin-bottom: 15px;">Información de registro:</h3>
                        <table style="width: 100%; color: #4b5563; font-size: 15px;">
                            <tr style="margin-bottom: 10px;">
                                <td style="padding: 8px 0; font-weight: 600; width: 40%;">Nombre completo:</td>
                                <td style="padding: 8px 0;">{{ $nombre_completo }}</td>
                            </tr>
                            <tr style="margin-bottom: 10px;">
                                <td style="padding: 8px 0; font-weight: 600;">Identificación:</td>
                                <td style="padding: 8px 0;">{{ $identificacion }}</td>
                            </tr>
                            <tr style="margin-bottom: 10px;">
                                <td style="padding: 8px 0; font-weight: 600;">Email:</td>
                                <td style="padding: 8px 0;">{{ $email }}</td>
                            </tr>
                            <tr style="margin-bottom: 10px;">
                                <td style="padding: 8px 0; font-weight: 600;">Cargo:</td>
                                <td style="padding: 8px 0;">{{ $cargo }}</td>
                            </tr>
                            <tr style="margin-bottom: 10px;">
                                <td style="padding: 8px 0; font-weight: 600;">Fecha de registro:</td>
                                <td style="padding: 8px 0;">{{ $fecha_registro }}</td>
                            </tr>
                        </table>
                    </div>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
                        En Ruta C, estamos comprometidos en apoyar el crecimiento y desarrollo de su negocio. Como empresario registrado, tendrá acceso a diferentes programas, convocatorias y servicios diseñados para fortalecer su empresa.
                    </p>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
                        Nuestro equipo estará disponible para acompañarlo en este proceso y proporcionarle las herramientas necesarias para alcanzar sus objetivos empresariales.
                    </p>
                    <div style="text-align: center; margin: 30px 0;">
                        <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 10px;">
                            <strong>¡Gracias por ser parte de ' . $projectName . '!</strong>
                        </p>
                        <p style="color: #6b7280; font-size: 14px; margin: 0;">
                            Estamos aquí para ayudarte a hacer crecer tu negocio.
                        </p>
                    </div>
                    <p style="color: #4b5563; font-size: 14px; line-height: 1.6; margin-top: 25px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                        Si tiene alguna pregunta o necesita asistencia, no dude en contactarnos. Estaremos encantados de ayudarle.
                    </p>
                ' . $footer
            ],
        ];
        
        // Si el tipo de template no existe, usar el template de prueba por defecto
        if (!isset($templates[$templateType])) {
            Log::warning('Template type no encontrado, usando template de prueba por defecto', [
                'template_type' => $templateType
            ]);
            return $templates['test'];
        }
        
        return $templates[$templateType];
    }

    /**
     * Renderizar template reemplazando variables
     */
    private function renderTemplate($template, $data = [])
    {
        // Reemplazar variables del tipo {{ $variable }}
        foreach ($data as $key => $value) {
            $template = str_replace('{{ $' . $key . ' }}', $value, $template);
            $template = str_replace('{{$' . $key . '}}', $value, $template);
        }
        
        // Variables globales siempre disponibles (usando zona horaria de Bogotá)
        $timezone = 'America/Bogota';
        $now = now($timezone);
        
        $globalVars = [
            'current_year' => $now->format('Y'),
            'current_date' => $now->format('d/m/Y'),
            'current_time' => $now->format('H:i:s'),
            'project_name' => $data['project_name'] ?? 'Ruta C',
        ];
        
        foreach ($globalVars as $key => $value) {
            $template = str_replace('{{ $' . $key . ' }}', $value, $template);
            $template = str_replace('{{$' . $key . '}}', $value, $template);
        }
        
        return $template;
    }

    /**
     * Enviar correo de bienvenida por inscripción a programa
     * 
     * @param object $inscripcion ConvocatoriaInscripcion con relaciones cargadas
     * @param object $asesor User que realizó la inscripción
     * @return bool
     */
    public function sendInscripcionPrograma($inscripcion, $asesor)
    {
        try {
            // Obtener datos necesarios
            $unidadProductiva = $inscripcion->unidadProductiva;
            $convocatoria = $inscripcion->convocatoria;
            $programa = $convocatoria->programa ?? null;

            // Validar que existan los emails necesarios
            $to = $unidadProductiva->registration_email;
            $cc = $unidadProductiva->contact_email;

            if (!$to && !$cc) {
                Log::warning('No se encontró email para enviar correo de inscripción', [
                    'inscripcion_id' => $inscripcion->inscripcion_id,
                    'unidad_productiva_id' => $unidadProductiva->unidadproductiva_id
                ]);
                return false;
            }

            // Preparar datos para el template con zona horaria de Bogotá
            $timezone = 'America/Bogota';
            
            if ($inscripcion->fecha_creacion) {
                if (is_string($inscripcion->fecha_creacion)) {
                    $fechaInscripcion = \Carbon\Carbon::parse($inscripcion->fecha_creacion)
                        ->setTimezone($timezone)
                        ->format('d/m/Y H:i:s');
                } else {
                    $fechaInscripcion = $inscripcion->fecha_creacion
                        ->setTimezone($timezone)
                        ->format('d/m/Y H:i:s');
                }
            } else {
                $fechaInscripcion = now($timezone)->format('d/m/Y H:i:s');
            }

            $data = [
                'programa_nombre' => $programa->nombre ?? 'Programa',
                'convocatoria_nombre' => $convocatoria->nombre_convocatoria ?? 'Convocatoria',
                'unidad_productiva_nombre' => $unidadProductiva->business_name ?? 'Unidad Productiva',
                'asesor_nombre' => $asesor->name ?? 'Asesor de Ruta C',
                'fecha_inscripcion' => $fechaInscripcion,
                'project_name' => 'Ruta C'
            ];

            // Preparar opciones para el correo
            $options = [];
            if ($cc && strcasecmp($to, $cc) !== 0) {
                $options['cc'] = [$cc];
            }

            // Enviar el correo
            return $this->sendTestTemplate($to ?? $cc, 'inscripcion_programa', $data, $options);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de inscripción a programa', [
                'inscripcion_id' => $inscripcion->inscripcion_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Enviar correo de bienvenida cuando se registra un empresario
     * 
     * @param object $empresario User (empresario)
     * @return bool
     */
    public function sendBienvenidaEmpresario($empresario)
    {
        try {
            // Validar que exista el email
            $to = $empresario->email;

            if (!$to) {
                Log::warning('No se encontró email para enviar correo de bienvenida de empresario', [
                    'user_id' => $empresario->id
                ]);
                return false;
            }

            // Preparar fecha de registro con zona horaria de Bogotá
            $timezone = 'America/Bogota';
            $fechaRegistro = $empresario->created_at 
                ? (is_string($empresario->created_at) 
                    ? \Carbon\Carbon::parse($empresario->created_at)->setTimezone($timezone)->format('d/m/Y H:i:s')
                    : $empresario->created_at->setTimezone($timezone)->format('d/m/Y H:i:s'))
                : now($timezone)->format('d/m/Y H:i:s');

            // Preparar datos para el template
            $nombreCompleto = trim(($empresario->name ?? '') . ' ' . ($empresario->lastname ?? '')) ?: 'Estimado/a';
            
            $data = [
                'nombre_completo' => $nombreCompleto,
                'identificacion' => $empresario->identification ?? 'No especificado',
                'email' => $empresario->email ?? 'No especificado',
                'cargo' => $empresario->position ?? 'No especificado',
                'fecha_registro' => $fechaRegistro,
                'project_name' => 'Ruta C'
            ];

            // Enviar el correo
            return $this->sendTestTemplate($to, 'bienvenida_empresario', $data, []);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de bienvenida de empresario', [
                'user_id' => $empresario->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
