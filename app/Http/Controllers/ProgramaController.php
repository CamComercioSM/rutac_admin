<?php

namespace App\Http\Controllers;

use App\Exports\ConvocatoriaExport;
use App\Http\Controllers\Controller;
use App\Models\Programas\Programa;
use App\Models\Programas\ProgramaConvocatoria;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProgramaController extends Controller
{
    function list()
    { 
        $data = [ 
            
        ];

        return View("programas.index", $data);
    }
    
    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new ConvocatoriaExport($query), 'convocatorias.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = Programa::with('etapas')->findOrFail($id);

        return view('programas.detail', [ 'detalle' => $result ]);
    }

    

    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');

        $query = Programa::
            select([
                'programa_id AS id',
                'programa_id',
                'nombre',
                'duracion'
            ]);

        if(!empty($search))
        {
            $filterts = ['nombre'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

       

        return $query;
    }
}
