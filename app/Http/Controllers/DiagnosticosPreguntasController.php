<?php

namespace App\Http\Controllers;

use App\Exports\DiagnosticoPreguntasExport;
use App\Http\Controllers\Controller;
use App\Models\Diagnosticos\Diagnostico;
use App\Models\Diagnosticos\DiagnosticoPregunta;
use App\Models\Diagnosticos\PreguntaOpcion;
use App\Models\TablasReferencias\PreguntaDimension;
use App\Models\TablasReferencias\PreguntaGrupo;
use App\Models\TablasReferencias\PreguntaTipo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DiagnosticosPreguntasController extends Controller
{
    function list($id = null)
    { 
        $data = [ 
            'grupos'=> PreguntaGrupo::get(),
            'tipos'=> PreguntaTipo::get(),
            'dimensiones'=> PreguntaDimension::get(),
            'diagnostico'=> Diagnostico::with('etapa')->find($id),
        ];

        return View("diagnosticos.preguntas", $data);
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new DiagnosticoPreguntasExport($query), 'diagnosticosPreguntas.xlsx');
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
            $pregunta = DiagnosticoPregunta::findOrFail($request->id);
            $pregunta->update($data);
        } else {
            $pregunta = DiagnosticoPregunta::create($data);
        }

        // IDs recibidos en el request
        $idsRecibidos = collect($opcionesData)->pluck('opcion_id')->filter()->toArray();

        // Eliminar opciones que no llegaron
        PreguntaOpcion::where('pregunta_id', $pregunta->pregunta_id)
            ->whereNotIn('opcion_id', $idsRecibidos)
            ->delete();

        // Crear o actualizar opciones
        foreach ($opcionesData as $opcion) {
            $opcionId = $opcion['opcion_id'] ?? null; // usa null si no existe

            if (!empty($opcionId)) {
                // Actualizar existente
                $opcionModel = PreguntaOpcion::find($opcionId);
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

        $query = DiagnosticoPregunta::with('opciones')
        ->select(
            'pregunta_id as id',
            'pregunta_id',
            'diagnostico_id',
            'preguntagrupo_id',
            'preguntatipo_id',
            'preguntadimension_id',
            'pregunta_titulo',
            'pregunta_porcentaje')
        ->where('diagnostico_id', $request->diagnostico);

        if(!empty($search))
        {
            $filters = ['pregunta_titulo'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
