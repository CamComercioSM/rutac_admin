<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;

class IAService
{
    public function analizar(array $data): ?string
    {
        $url = config('services.analizar_intervenciones_ia.api_url');
        if (!$url) return null;

        $payload = $this->buildPayload($data);

        try {
            $response = Http::timeout(30)->asJson()->post($url, $payload);
            if ($response->successful()) {
                $mensaje = $response->json('mensaje') ?? $response->json('MENSAJE');
                return $mensaje ? $this->markdownToHtml($mensaje) : null;
            }
        } catch (\Throwable $e) {
            Log::error("IA Analysis Error: " . $e->getMessage());
        }
        return null;
    }

    private function buildPayload(array $data): array
    {
        $estadisticas = [
            'total_intervenciones' => $data['totalGeneral'],
            'categorias' => collect($data['porCategoria'])->map(fn($c) => ['nombre' => $c->categoria->nombre ?? 'N/A', 'total' => $c->total]),
            // ... resto de mapeos de estadísticas
        ];

        return [
            'conclusiones' => (string) ($data['conclusiones'] ?? ''),
            'estadisticas' => json_encode($estadisticas)
        ];
    }

    public function markdownToHtml(string $markdown): string
    {
        try {
            $converter = new CommonMarkConverter(['html_input' => 'strip', 'allow_unsafe_links' => false]);
            return $converter->convert($markdown)->getContent();
        } catch (\Throwable $e) {
            return nl2br(preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $markdown));
        }
    }
}
