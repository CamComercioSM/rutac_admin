<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Enviar correo de recuperación de contraseña
     */
    public function sendPasswordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'reset_url' => 'required|url',
            'user_name' => 'nullable|string',
            'project_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $data = [
                'email' => $request->email,
                'reset_url' => $request->reset_url,
                'user_name' => $request->user_name ?? 'Usuario',
                'project_name' => $request->project_name ?? 'Sistema',
                'reset_token' => $this->generateResetToken(),
                'expires_at' => now()->addHours(24)->format('Y-m-d H:i:s')
            ];

            // Enviar correo usando el servicio
            $this->mailService->sendPasswordReset(
                $data['email'],
                $data['user_name'],
                $data['reset_url'],
                $data['project_name']
            );

            Log::info('Correo de recuperación enviado exitosamente', [
                'email' => $data['email'],
                'project' => $data['project_name'],
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo de recuperación enviado exitosamente',
                'data' => [
                    'email' => $data['email'],
                    'expires_at' => $data['expires_at']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de recuperación', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo de recuperación',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Enviar correo personalizado
     */
    public function sendCustomEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'template' => 'required|string',
            'data' => 'required|array',
            'cc' => 'nullable|array',
            'cc.*' => 'email',
            'bcc' => 'nullable|array',
            'bcc.*' => 'email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $emailData = [
                'to' => $request->to,
                'subject' => $request->subject,
                'template' => $request->template,
                'data' => $request->data,
                'cc' => $request->cc ?? [],
                'bcc' => $request->bcc ?? []
            ];

            // Enviar correo personalizado
            $this->mailService->sendCustomEmail($emailData);

            Log::info('Correo personalizado enviado exitosamente', [
                'to' => $emailData['to'],
                'subject' => $emailData['subject'],
                'template' => $emailData['template'],
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo enviado exitosamente',
                'data' => [
                    'to' => $emailData['to'],
                    'subject' => $emailData['subject'],
                    'sent_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo personalizado', [
                'to' => $request->to,
                'subject' => $request->subject,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Enviar correo con HTML personalizado desde otra aplicación
     */
    public function sendHtml(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'html' => 'required|string',
            'cc' => 'nullable|array',
            'cc.*' => 'email',
            'bcc' => 'nullable|array',
            'bcc.*' => 'email',
            'reply_to' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $emailData = [
                'to' => $request->to,
                'subject' => $request->subject,
                'html' => $request->html,
                'cc' => $request->cc ?? [],
                'bcc' => $request->bcc ?? [],
                'reply_to' => $request->reply_to ?? null,
            ];

            // Enviar correo con HTML personalizado
            $this->mailService->sendRawHtml(
                $emailData['to'],
                $emailData['subject'],
                $emailData['html'],
                [
                    'cc' => $emailData['cc'],
                    'bcc' => $emailData['bcc'],
                    'reply_to' => $emailData['reply_to']
                ]
            );

            Log::info('Correo HTML personalizado enviado exitosamente', [
                'to' => $emailData['to'],
                'subject' => $emailData['subject'],
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo HTML enviado exitosamente',
                'data' => [
                    'to' => $emailData['to'],
                    'subject' => $emailData['subject'],
                    'sent_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo HTML personalizado', [
                'to' => $request->to,
                'subject' => $request->subject,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Verificar estado del servicio de correo
     */
    public function healthCheck()
    {
        try {
            // Verificar configuración de correo
            $config = config('mail');
            
            return response()->json([
                'success' => true,
                'message' => 'Servicio de correo funcionando correctamente',
                'data' => [
                    'driver' => $config['default'] ?? 'unknown',
                    'from_address' => $config['from']['address'] ?? 'unknown',
                    'from_name' => $config['from']['name'] ?? 'unknown',
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en health check del servicio de correo', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en el servicio de correo',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Generar token de recuperación
     */
    private function generateResetToken()
    {
        return \Illuminate\Support\Str::random(64);
    }
}
