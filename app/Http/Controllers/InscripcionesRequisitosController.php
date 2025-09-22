<?php

namespace App\Http\Controllers;

use App\Exports\InscripcionesRequisitosExport;
use App\Http\Controllers\Controller;
use App\Models\Programas\InscripcionesRequisitos;
use App\Models\Programas\ProgramaIndicador;
use App\Models\Programas\RequisitosOpciones;
use App\Models\TablasReferencias\PreguntaTipo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InscripcionesRequisitosController extends Controller
{
    function list()
    { 
        $data = [ 
            'indicadores'=> ProgramaIndicador::get(),
            'tipos'=> PreguntaTipo::get(),
        ];

        return View("inscripciones.preguntas", $data);
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new InscripcionesRequisitosExport($query), 'inscripcionRequisitos.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = InscripcionesRequisitos::with('opciones')->findOrFail($id);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->except('opciones'); // Separa las opciones
        $opcionesData = $request->input('opciones', []);

        // Crear o actualizar la pregunta
        if ($request->filled('id')) {
            $pregunta = InscripcionesRequisitos::findOrFail($request->id);
            $pregunta->update($data);
        } else {
            $pregunta = InscripcionesRequisitos::create($data);
        }

        // IDs recibidos en el request
        $idsRecibidos = collect($opcionesData)->pluck('opcion_id')->filter()->toArray();

        // Eliminar opciones que no llegaron
        RequisitosOpciones::where('pregunta_id', $pregunta->pregunta_id)
            ->whereNotIn('opcion_id', $idsRecibidos)
            ->delete();

        // Crear o actualizar opciones
        foreach ($opcionesData as $opcion) {
            $opcionId = $opcion['opcion_id'] ?? null; // usa null si no existe

            if (!empty($opcionId)) {
                // Actualizar existente
                $opcionModel = RequisitosOpciones::find($opcionId);
                if ($opcionModel) {
                    $opcionModel->update([
                        'opcion_variable_response' => $opcion['opcion_variable_response'] ?? null,
                        'opcion_percentage'        => $opcion['opcion_percentage'] ?? null
                    ]);
                }
            } else {
                // Crear nueva
                $pregunta->opciones()->create([
                    'opcion_variable_response' => $opcion['opcion_variable_response'] ?? null,
                    'opcion_percentage'        => $opcion['opcion_percentage'] ?? null
                ]);
            }
        }

        return response()->json(['message' => 'Stored'], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = InscripcionesRequisitos::with('opciones')
        ->select(
            'requisito_id as id',
            'i.indicador_id',
            'requisito_id',
            'preguntatipo_id',
            'requisito_titulo',
            'requisito_porcentaje',
            'indicador_nombre as indicador'
            )
            ->leftJoin('programa_indicadores as i', 'inscripciones_requisitos.indicador_id', '=', 'i.indicador_id');

        if(!empty($search))
        {
            $filters = ['requisito_titulo', 'indicador_nombre'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

       if (!empty($request->convocatoria)) {
            $query->whereIn('requisito_id', function ($q) use ($request) {
                $q->select('requisito_id')
                ->from('convocatorias_requisitos')
                ->where('convocatoria_id', $request->convocatoria);
            });

            if($request->tipo == 1){
                $query->whereNotNull('i.indicador_id'); 
            }
            else{
                $query->whereNull('i.indicador_id'); 
            }

        }

        return $query;
    }
}
