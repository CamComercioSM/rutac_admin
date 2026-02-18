<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessageLog;
use App\Models\WhatsappTemplate;
use App\Models\WhatsappTemplateCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Http\Requests\Whatsapp\SendTestRequest;

class WhatsappTestSendController extends Controller
{
    public function index(Request $request): View
    {
        $categories = WhatsappTemplateCategory::where('is_active', true)->orderBy('name')->get();
        $templates = WhatsappTemplate::where('is_active', true)
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->orderBy('name')
            ->get(['id', 'name', 'category_id', 'group_code', 'expected_fields', 'default_payload']);
        return view('whatsapp.test-send.index', compact('categories', 'templates'));
    }

    /**
     * Endpoint para enviar prueba: POST /admin/whatsapp/send-test
     */
    public function sendTest(SendTestRequest $request): JsonResponse
    {
        $template = WhatsappTemplate::where('name', $request->template_name)->first();

        if (! $template) {
            return response()->json([
                'ok' => false,
                'message' => 'Plantilla no encontrada.',
            ], 422);
        }

        if (! $template->is_active) {
            return response()->json([
                'ok' => false,
                'message' => 'La plantilla no está activa.',
            ], 422);
        }

        $requiredKeys = $template->getRequiredFieldKeys();
        $data = $request->input('data', []);
        $data = is_array($data) ? $data : [];

        foreach ($requiredKeys as $key) {
            if (empty($data[$key])) {
                return response()->json([
                    'ok' => false,
                    'message' => "Falta el campo requerido: {$key}",
                ], 422);
            }
        }

        $defaultPayload = $template->default_payload ?? [];
        $finalData = array_merge($defaultPayload, $data);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        if (strlen($phone) === 10 && ! str_starts_with($phone, '57')) {
            $phone = '57' . $phone;
        }

        $payload = [
            'phone' => $phone,
            'templateName' => $template->name,
            'templateGroup' => $template->group_code,
            'data' => $finalData,
        ];

        $log = WhatsappMessageLog::create([
            'template_id' => $template->id,
            'user_id' => auth()->id(),
            'phone' => $phone,
            'template_name' => $template->name,
            'template_group' => $template->group_code,
            'status' => 'queued',
            'payload' => $payload,
        ]);

        try {
            $providerMessageId = (string) Str::uuid();
            $providerResponse = [
                'simulated' => true,
                'message_id' => $providerMessageId,
                'status' => 'sent',
            ];

            $log->update([
                'status' => 'sent',
                'provider_message_id' => $providerMessageId,
                'provider_response' => $providerResponse,
            ]);

            return response()->json([
                'ok' => true,
                'status' => 'sent',
                'message' => 'Mensaje de prueba registrado y simulado correctamente.',
                'provider_message_id' => $providerMessageId,
            ]);
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'provider_response' => $log->provider_response ?? [],
            ]);
            return response()->json([
                'ok' => false,
                'message' => 'Error al procesar el envío: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API para listar plantillas (filtro por categoría) - usado por el formulario de prueba.
     */
    public function templatesByCategory(Request $request): JsonResponse
    {
        $templates = WhatsappTemplate::where('is_active', true)
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->orderBy('name')
            ->get(['id', 'name', 'group_code', 'expected_fields', 'default_payload']);
        return response()->json($templates);
    }
}
