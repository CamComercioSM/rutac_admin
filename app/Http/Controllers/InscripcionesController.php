<?php

namespace App\Http\Controllers;

use App\Exports\InscripcionesExport;
use App\Http\Controllers\Controller;
use App\Models\Inscripciones\ConvocatoriaInscripcion;
use App\Models\Inscripciones\ConvocatoriaRespuesta;
use App\Models\Programas\Programa;
use App\Models\Programas\ProgramaConvocatoria;
use App\Models\TablasReferencias\InscripcionEstado;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class InscripcionesController extends Controller
{
    function list(Request $request)
    { 
        $data = [
            'estados'=> InscripcionEstado::get(),
            'programas'=> Programa::get(),
            'convocatorias'=> ProgramaConvocatoria::get(),
            'unidades'=> [],
            'filtros'=> $request->all()
        ];
        
        return View("inscripciones.index", $data);
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new InscripcionesExport($query), 'inscripciones.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = ConvocatoriaInscripcion::with([
            'convocatoria',
            'unidadProductiva'=> function ($q) { $q->with(['etapa', 'sectorUnidad', 'ventaAnual']); },
            'estado',
            'historial',
            'respuestas'
        ])->findOrFail($id);

        $estados = InscripcionEstado::get();

        return view('inscripciones.detail', ['detalle' => $result, 'estados' => $estados]);
    }

    public function update($id, Request $request)
    {
        $entity = ConvocatoriaInscripcion::findOrFail($id);

        if($request->hasFile('archivo')) 
        {
            $path = $request->file('archivo')->store('aplications', 'public');

           $entity->archivo = $path;
        }

        $entity->inscripcionestado_id = $request->input('inscripcionestado_id');
        $entity->comentarios = $request->input('comentarios');
        $entity->activarPreguntas = $request->input('activarPreguntas') == 1;
        $entity->save();

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    public function store(Request $request)
    {
        $entity = ConvocatoriaRespuesta::findOrFail($request->respuestaId);
        $entity->value = $request->valorPregunta;
        $entity->save();

        return response()->json([ 'message' => 'Stored' ], 201);
    }


    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');
        $programa = $request->get('programa');
        $convocatoria = $request->get('convocatoria');
        $estado = $request->get('estado');
        $unidad = $request->get('unidad');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        $query = ConvocatoriaInscripcion::select([
                'inscripcion_id AS id',
                'convocatorias_inscripciones.fecha_creacion',
                'pc.nombre_convocatoria',
                'p.nombre as nombre_programa',
                'up.nit',
                'up.business_name',
                'ie.inscripcionEstadoNOMBRE as estado',
            ])
            ->join('programas_convocatorias as pc', 'convocatorias_inscripciones.convocatoria_id', '=', 'pc.convocatoria_id')
            ->join('programas as p', 'pc.programa_id', '=', 'p.programa_id')
            ->join('unidadesproductivas as up', 'convocatorias_inscripciones.unidadproductiva_id', '=', 'up.unidadproductiva_id')
            ->join('inscripciones_estados as ie', 'convocatorias_inscripciones.inscripcionestado_id', '=', 'ie.inscripcionestado_id');

        if(!empty($search))
        {
            $filterts = ['pc.nombre_convocatoria','p.nombre','up.nit','up.business_name'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        if(!empty($programa)){
            $query->where('p.programa_id', $programa);
        }

        if(!empty($convocatoria)){
            $query->where('pc.convocatoria_id', $convocatoria);
        }

        if(!empty($estado)){
            $query->where('ie.inscripcionestado_id', $estado);
        }

        if(!empty($unidad)){
            $query->where('up.unidadproductiva_id', $unidad);
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query->whereBetween('convocatorias_inscripciones.fecha_creacion', [$fecha_inicio, $fecha_fin]);
        } elseif (!empty($fecha_inicio)) {
            $query->whereDate('convocatorias_inscripciones.fecha_creacion', '>=', $fecha_inicio);
        } elseif (!empty($fecha_fin)) {
            $query->whereDate('convocatorias_inscripciones.fecha_creacion', '<=', $fecha_fin);
        }

        return $query;
    }

}
