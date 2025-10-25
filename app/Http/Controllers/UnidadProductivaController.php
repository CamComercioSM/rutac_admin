<?php

namespace App\Http\Controllers;

use App\Exports\UnidadProductivaExport;
use App\Http\Controllers\Controller;
use App\Models\Diagnosticos\ResultadosDiagnostico;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\TablasReferencias\Etapa;
use App\Models\TablasReferencias\Sector;
use App\Models\TablasReferencias\UnidadProductivaPersona;
use App\Models\TablasReferencias\UnidadProductivaTamano;
use App\Models\TablasReferencias\UnidadProductivaTipo;
use App\Models\TablasReferencias\Departamento;
use App\Models\TablasReferencias\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UnidadProductivaController extends Controller
{
    function list()
    { 
        $data = [
            'etapas'=> Etapa::get(),
            'sectores'=> Sector::get(),
            'tamanos'=> UnidadProductivaTamano::get(),
            'tipoPersona'=> UnidadProductivaPersona::get(),
            'unidades'=> [],
        ];
        
        return View("unidadesProductivas.index", $data);
    }

    function edit($id, $transformar = null)
    { 
        $data = [
            'sectores'=> Sector::get(),
            'tamanos'=> UnidadProductivaTamano::get(),
            'tipoPersona'=> UnidadProductivaPersona::get(),
            'tipoUnidad'=> UnidadProductivaTipo::get(),
            'departamentos'=> Departamento::get(),
            'municipios'=> Municipio::get(),
            "elemento"=> UnidadProductiva::find($id),
            "api"=> "/unidadesProductivas" . ($transformar != null ? "/$id" : ''),
            "accion"=> $transformar != null ? "Transformar" : 'Editar'
        ];
        
        return View("unidadesProductivas.edit", $data);
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new UnidadProductivaExport($query), 'unidadesProductivas.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $unidadProductiva = UnidadProductiva::with([
            'etapa',
            'tipoPersona',
            'diagnosticos',
            'inscripciones',
            'diagnosticos',
            'usuario',
            'transformadaDesde',
            'transformadaEn'
        ])->findOrFail($id);

        $diagnostico = $unidadProductiva->diagnosticos->last();
           
        $resultados = ResultadosDiagnostico::where('resultado_id', $diagnostico->resultado_id)->get();

        $dimensiones = $resultados->pluck('dimension')->toArray();
        $resultados = $resultados->pluck('valor')->toArray();
                
        return view('unidadesProductivas.detail',
         [
            'detalle' => $unidadProductiva,
            'dimensions'=> json_encode($dimensiones),
            'results'=> json_encode($resultados),
         ]);
    }

    public function store(Request $request)
    {
        $entity = UnidadProductiva::findOrFail($request->unidadproductiva_id);
        $entity->update( $request->all() );

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    public function update($id, Request $request)
    {
        $current = UnidadProductiva::findOrFail($id);

        $data = $request->except('unidadproductiva_id');
        $data['transformada_desde'] = $current->unidadproductiva_id;
        $data['complete_diagnostic'] = 0;
        $entity = UnidadProductiva::create($data);

        $current->etapa_intervencion = 'TRANSFORMADA';
        $current->transformada_fecha = date();
        $current->transformada_en = $entity->unidadproductiva_id;
        $current->save();

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');
        $tipopersona = $request->get('tipopersona');
        $sector = $request->get('sector');
        $tamano = $request->get('tamano');
        $etapa = $request->get('etapa');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        $query = UnidadProductiva::select([
                'unidadesproductivas.unidadproductiva_id AS id',
                'unidadesproductivas.fecha_creacion',
                'unidadesproductivas.tipo_registro_rutac',
                'unidadesproductivas.business_name',
                'unidadesproductivas.nit',
                'unidadesproductivas.name_legal_representative',
                'unidadesproductivas.registration_email',
                'tp.tipoPersonaNOMBRE as tipo_persona',
                'st.sectorNOMBRE as sector',
                'tm.tamanoNOMBRE as tamano',
                'dp.departamentonombre as departamento',
                'mp.municipionombreoficial as municipio',
                'etapas.name as etapa',
            ])
            ->leftJoin('etapas', 'unidadesproductivas.etapa_id', '=', 'etapas.etapa_id')
            ->leftJoin('unidadesproductivas_personas as tp', 'unidadesproductivas.tipopersona_id', '=', 'tp.tipopersona_id')
            ->leftJoin('ciiu_macrosectores as st', 'unidadesproductivas.sector_id', '=', 'st.sector_id')
            ->leftJoin('unidadesproductivas_tamanos as tm', 'unidadesproductivas.tamano_id', '=', 'tm.tamano_id')
            ->leftJoin('departamentos as dp', 'unidadesproductivas.department_id', '=', 'dp.departamento_id')
            ->leftJoin('municipios as mp', 'unidadesproductivas.municipality_id', '=', 'mp.municipio_id');

        if(!empty($search))
        {
            $filterts = ['unidadesproductivas.nit', 'unidadesproductivas.business_name', 'unidadesproductivas.name_legal_representative'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        if(!empty($tipopersona)){
            $query->where('tp.tipopersona_id', $tipopersona);
        }

        if(!empty($sector)){
            $query->where('st.sector_id', $sector);
        }

        if(!empty($tamano)){
            $query->where('tm.tamano_id', $tamano);
        }

        if(!empty($etapa)){
            $query->where('etapas.etapa_id', $etapa);
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query->whereBetween('unidadesproductivas.fecha_creacion', [$fecha_inicio, $fecha_fin]);
        } elseif (!empty($fecha_inicio)) {
            $query->whereDate('unidadesproductivas.fecha_creacion', '>=', $fecha_inicio);
        } elseif (!empty($fecha_fin)) {
            $query->whereDate('unidadesproductivas.fecha_creacion', '<=', $fecha_fin);
        }

        return $query;
    }

    function search(Request $request)
    { 
        $busqueda = $request->term;

        $items = UnidadProductiva::where('nit', 'like', "%{$busqueda}%")
            ->orWhere('business_name', 'like', "%{$busqueda}%")
            ->take(10)
            ->get([
                'unidadproductiva_id as id',
                DB::raw("CONCAT(nit, ' - ', business_name) as text")
            ]);

        return response()->json(['results' => $items]);
    }

}
