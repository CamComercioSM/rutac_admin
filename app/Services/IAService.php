<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;

class IAService
{

    public function analizarInforme(Request $request, array $data): ?string
    {
        return $this->callIAApi(
            $this->buildPayloadInformeIntervenciones($request, $data)
        );
    }

    /**
     * Endpoint para obtener payload estructurado
     */
    public function getPayloadAnalisisIA(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date',
        ]);

        $data = $this->getInformeData($request);

        return response()->json([
            'payload' => $this->buildPayloadInformeIntervenciones($request, $data),
            'api_url' => config('services.analizar_intervenciones_ia.api_url'),
        ]);
    }

    /**
     * ==============================
     * PAYLOAD PRINCIPAL (INFORME)
     * ==============================
     */
    public function buildPayloadInformeIntervenciones(Request $request, array $data): array
    {
        $estadisticas = [
            'fecha_inicio'            => $request->input('fecha_inicio'),
            'fecha_fin'               => $request->input('fecha_fin'),
            'total_intervenciones'    => (int) ($data['totalGeneral'] ?? 0),
            'categorias_intervencion' => $this->mapCategorias($data['porCategoria'] ?? []),
            'tipos_intervencion'      => $this->mapTipos($data['porTipo'] ?? []),
            'unidades_productivas'    => $this->mapUnidades($data['porUnidad'] ?? []),
        ];

        return [
            'conclusiones' => (string) ($data['conclusiones'] ?? ''),
            'estadisticas' => json_encode($estadisticas, JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * ==============================
     * MÉTODO GENÉRICO DE ANÁLISIS
     * ==============================
     */
    public function analizar(array $data): ?string
    {
        return $this->callIAApi(
            $this->buildPayloadBasico($data)
        );
    }

    /**
     * ==============================
     * MÉTODO INTERNO PARA INFORME
     * ==============================
     */
    private function llamarApiAnalizarIntervencionesIA(Request $request, array $data): ?string
    {
        return $this->callIAApi(
            $this->buildPayloadInformeIntervenciones($request, $data)
        );
    }

    /**
     * ==============================
     * CORE HTTP (REUTILIZABLE)
     * ==============================
     */
    private function callIAApi(array $payload): ?string
    {
        $url = config('services.analizar_intervenciones_ia.api_url');
        if (!$url) return null;

        Log::info('IAService: Payload enviado', [
            'url' => $url,
            'payload' => $payload,
        ]);

        try {
            $response = Http::timeout(30)
                ->asJson()
                ->post($url, $payload);

            if (!$response->successful()) {
                Log::warning('IAService: respuesta no exitosa', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $mensaje = $response->json('mensaje') ?? $response->json('MENSAJE');

            return is_string($mensaje)
                ? $this->markdownToHtml($mensaje)
                : null;
        } catch (\Throwable $e) {
            Log::error('IAService: error', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * ==============================
     * PAYLOAD SIMPLE (GENÉRICO)
     * ==============================
     */
    private function buildPayloadBasico(array $data): array
    {
        $estadisticas = [
            'total_intervenciones' => (int) ($data['totalGeneral'] ?? 0),
            'categorias' => $this->mapCategoriasSimple($data['porCategoria'] ?? []),
        ];

        return [
            'conclusiones' => (string) ($data['conclusiones'] ?? ''),
            'estadisticas' => json_encode($estadisticas, JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * ==============================
     * MAPPERS (OPTIMIZADOS)
     * ==============================
     */

    private function mapCategorias(array $items): array
    {
        return array_map(fn($c) => [
            'categoria' => $c->categoria->nombre ?? 'Sin categoría',
            'cantidad'  => (int) $c->total,
        ], $items);
    }

    private function mapTipos(array $items): array
    {
        return array_map(fn($t) => [
            'tipo'     => $t->tipo->nombre ?? 'Sin tipo',
            'cantidad' => (int) $t->total,
        ], $items);
    }

    private function mapUnidades(array $items): array
    {
        return array_map(fn($u) => [
            'unidad_productiva' => $u->unidadProductiva?->business_name ?? 'Sin unidad productiva',
            'cantidad'          => (int) $u->total,
        ], $items);
    }

    private function mapCategoriasSimple(array $items): array
    {
        return array_map(fn($c) => [
            'nombre' => $c->categoria->nombre ?? 'N/A',
            'total'  => (int) $c->total,
        ], $items);
    }

    /**
     * ==============================
     * MARKDOWN → HTML
     * ==============================
     */
    private function markdownToHtml(string $text): string
    {
        return (new CommonMarkConverter())->convert($text)->getContent();
    }
}
