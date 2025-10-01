<?php

namespace App\Http\Controllers;

use App\Exports\DiagnosticoExport;
use App\Http\Controllers\Controller;
use App\Models\Diagnosticos\Diagnostico;
use App\Models\TablasReferencias\Etapa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DiagnosticosController extends Controller
{
    function list()
    { 
        $data = [ 'etapas'=> Etapa::get() ];

        return View("diagnosticos.index", $data);
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new DiagnosticoExport($query), 'diagnosticos.xlsx');
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
            $entity = Diagnostico::findOrFail($request->id);
            $entity->update($data);
        } 
        else {
            $entity = Diagnostico::create($data);
        }

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = Diagnostico::select([
                'diagnosticos.diagnostico_id AS id',
                'diagnosticos.diagnostico_nombre',
                'diagnosticos.diagnostico_etapa_id',
                'diagnosticos.diagnostico_conventas',
                'e.name AS etapa'
            ])
            ->leftJoin('etapas as e', 'diagnosticos.diagnostico_etapa_id', '=', 'e.etapa_id');

        if(!empty($search))
        {
            $filters = ['diagnostico_nombre'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
