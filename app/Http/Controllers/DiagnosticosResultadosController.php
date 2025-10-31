<?php

namespace App\Http\Controllers;

use App\Exports\DiagnosticoResultadoExport;
use App\Exports\DiagnosticoRespuestasResultadoExport;
use App\Http\Controllers\Controller;
use App\Models\Diagnosticos\DiagnosticoResultado;
use App\Models\Diagnosticos\ResultadosDiagnostico;
use App\Models\Diagnosticos\DiagnosticoRespuesta;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\TablasReferencias\Etapa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class DiagnosticosResultadosController extends Controller
{
    function list(Request $request)
    { 
        $data = [
            'etapas'=> Etapa::get(),
            'unidades'=> [],
            'filtros'=> $request->all(),
        ];
        
        if ($unidad = $request->get('unidad')) {
            $data['unidades'] = UnidadProductiva::where('unidadproductiva_id', $unidad)->get();
        }

        return View("diagnosticosRespuesta.index", $data);
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new DiagnosticoResultadoExport($query), 'diagnosticosResultados.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = DiagnosticoResultado::with([
            'unidadProductiva'=> function ($q) { $q->with(['etapa', 'sectorUnidad', 'ventaAnual']); },
            'etapa',
            'respuestas'
        ])->findOrFail($id);

        $rows = DB::table('resultados_diagnosticos')
            ->where('resultado_id', $id)
            ->get(['dimension', 'valor']);

        $dimensiones = $rows->pluck('dimension')->values()->toArray();
        $valores = $rows->pluck('valor')->map(function($v){ return round((float)$v, 3); })->values()->toArray();

        if (empty($dimensiones)) {
            // Fallback: reconstruir dimensiones y valores desde respuestas
            $reconstruidos = DB::table('diagnosticos_respuestas as dr')
                ->join('diagnosticos_preguntas as dp', 'dp.pregunta_id', '=', 'dr.pregunta_id')
                ->join('preguntas_dimensiones as pd', 'pd.preguntadimension_id', '=', 'dp.preguntadimension_id')
                ->where('dr.resultado_id', $id)
                ->groupBy('pd.preguntadimension_nombre')
                ->orderBy('pd.preguntadimension_nombre')
                ->select([
                    'pd.preguntadimension_nombre as dimension',
                    DB::raw('ROUND(SUM(dp.pregunta_porcentaje/100 * dr.diagnosticorespuesta_valor), 2) as valor')
                ])->get();

            $dimensiones = $reconstruidos->pluck('dimension')->values()->toArray();
            $valores = $reconstruidos->pluck('valor')->map(function($v){ return round((float)$v, 3); })->values()->toArray();
        }
        
        return view('diagnosticosRespuesta.detail',
         [
            'detalle' => $result,
            'dimensions'=> $dimensiones,
            'results'=> $valores,
         ]);
    }


    private function getQuery(Request $request)
    {
        $search = $request->get('search');
        $etapa = $request->get('etapa');
        $unidad = $request->get('unidad');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        $query = DiagnosticoResultado::select([
                'diagnosticos_resultados.resultado_id AS id',
                'diagnosticos_resultados.fecha_creacion',
                'diagnosticos_resultados.resultado_puntaje',
                'up.nit',
                'up.business_name',
                'etapas.name as etapa',
            ])
            ->join('etapas', 'diagnosticos_resultados.etapa_id', '=', 'etapas.etapa_id')
            ->join('unidadesproductivas as up', 'diagnosticos_resultados.unidadproductiva_id', '=', 'up.unidadproductiva_id');

        if(!empty($search))
        {
            $filterts = ['up.nit','up.business_name','etapas.name'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        if(!empty($etapa)){
            $query->where('etapas.etapa_id', $etapa);
        }

        if(!empty($unidad)){
            $query->where('up.unidadproductiva_id', $unidad);
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query->whereBetween('diagnosticos_resultados.fecha_creacion', [$fecha_inicio, $fecha_fin]);
        } elseif (!empty($fecha_inicio)) {
            $query->whereDate('diagnosticos_resultados.fecha_creacion', '>=', $fecha_inicio);
        } elseif (!empty($fecha_fin)) {
            $query->whereDate('diagnosticos_resultados.fecha_creacion', '<=', $fecha_fin);
        }

        return $query;
    }

    function exportRespuestas(Request $request)
    { 
        $query = DiagnosticoRespuesta::select([
                'diagnosticos_respuestas.resultado_id',
                'diagnosticos_respuestas.diagnosticorespuesta_id',
                'diagnosticos_respuestas.fecha_creacion',
                'p.pregunta_titulo',
                'diagnosticos_respuestas.diagnosticorespuesta_valor',
                'p.pregunta_porcentaje',
            ])
        ->join('diagnosticos_preguntas as p', 'diagnosticos_respuestas.pregunta_id', '=', 'p.pregunta_id');

        $query = $query->where('resultado_id', $request->id);

        return Excel::download(new DiagnosticoRespuestasResultadoExport($query), 'respuestasDiagnostico.xlsx');
    }

}

