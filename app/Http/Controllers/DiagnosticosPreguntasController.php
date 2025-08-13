<?php

namespace App\Http\Controllers;

use App\Exports\DiagnosticoPreguntasExport;
use App\Http\Controllers\Controller;
use App\Models\Diagnosticos\Diagnostico;
use App\Models\Diagnosticos\DiagnosticoPregunta;
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
        $data = $request->all();
        
        if ($request->filled('id')) 
        {
            $entity = DiagnosticoPregunta::findOrFail($request->id);
            $entity->update($data);
        } 
        else {
            $entity = DiagnosticoPregunta::create($data);
        }

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');

        $query = DiagnosticoPregunta::where('diagnostico_id', $request->diagnostico);

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
