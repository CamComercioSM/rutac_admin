<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Programas\InscripcionesRequisitos;
use App\Models\Programas\ProgramaConvocatoria;
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
        //return Excel::download(new InscripcionesRequisitossExport($query), 'inscripcionRequisitos.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
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
        $search = $request->get('searchText');

        $query = InscripcionesRequisitos::with('opciones')
        ->select(
            'requisito_id as id',
            'indicador_id',
            'requisito_id',
            'preguntatipo_id',
            'requisito_titulo',
            'requisito_porcentaje');

        if(!empty($search))
        {
            $filters = ['requisito_titulo'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
