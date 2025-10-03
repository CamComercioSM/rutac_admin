<?php

namespace App\Http\Controllers;

use App\Exports\EmpresariosExport;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class EmpresariosController extends Controller
{
    function list()
    { 
        return View("empresarios.index");
    }
    
    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new EmpresariosExport($query), 'empresarios.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = User::with('unidades')->findOrFail($id);

        return view('empresarios.detail', [ 'detalle' => $result ]);
    }


    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = User::whereNull('rol_id')
            ->select(
                'id',
                'identification',
                'email',
                DB::raw("concat(name, ' ', lastname) as full_name")
            );

        if(!empty($search))
        {
            $filterts = ['identification', 'name', 'lastname', 'email'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
