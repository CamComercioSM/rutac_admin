<?php

namespace App\Http\Controllers;

use App\Exports\DiagnosticoResultadoExport;
use App\Http\Controllers\Controller;
use App\Models\Diagnosticos\DiagnosticoResultado;
use App\Models\TablasReferencias\Etapa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DiagnosticosResultadosController extends Controller
{
    function list()
    { 
        $data = [
            'etapas'=> Etapa::get(),
            'unidades'=> [],
        ];
        
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
            'unidadproductiva',
            'etapa',
            'respuestas'
        ])->findOrFail($id);
        
        return view('diagnosticosRespuesta.detail', ['detalle' => $result]);
    }


    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');
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
            $filterts = ['pc.nombre_convocatoria','p.nombre','up.nit','up.business_name'];
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

}

