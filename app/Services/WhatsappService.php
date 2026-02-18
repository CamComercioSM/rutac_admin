<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $apiUrl;
    protected $apiToken;
    protected $useWhatsappApi;

    public function __construct()
    {
        $whatsappUrl = config('services.whatsapp.api_url');
        if (!empty($whatsappUrl)) {
            $this->apiUrl = rtrim($whatsappUrl, '/');
            $this->apiToken = config('services.whatsapp.api_token');
            $this->useWhatsappApi = true;
        } else {
            $this->apiUrl = rtrim(config('services.wati.api_url', 'https://api.wati.io'), '/');
            $this->apiToken = config('services.wati.api_token');
            $this->useWhatsappApi = false;
        }
    }

    /**
     * Enviar mensaje de WhatsApp usando Wati API
     * 
     * @param string $to Número de teléfono en formato internacional (ej: 573001234567)
     * @param string $message Mensaje a enviar
     * @return array
     */
    public function send($to, $message)
    {
        try {
            // Limpiar el número de teléfono (remover espacios, guiones, etc.)
            $to = preg_replace('/[^0-9]/', '', $to);
            
            // Si el número no empieza con código de país, agregar código de Colombia (57)
            if (!preg_match('/^57/', $to) && strlen($to) == 10) {
                $to = '57' . $to;
            }

            $headers = ['Content-Type' => 'application/json'];
            if (!empty($this->apiToken)) {
                $headers['Authorization'] = 'Bearer ' . $this->apiToken;
            }

            $response = Http::withHeaders($headers)
                ->post($this->apiUrl . '/api/v1/sendSessionMessage/' . $to, [
                    'text' => $message
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Mensaje enviado correctamente',
                    'data' => $response->json()
                ];
            } else {
                Log::error('Error al enviar mensaje WhatsApp', [
                    'to' => $to,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Error al enviar el mensaje',
                    'error' => $response->json() ?? $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Excepción al enviar mensaje WhatsApp', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error al enviar el mensaje: ' . $e->getMessage()
            ];
        }
    }
}
