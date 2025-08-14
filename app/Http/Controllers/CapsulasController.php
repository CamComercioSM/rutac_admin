<?php

namespace App\Http\Controllers;

use App\Exports\CapsulaExport;
use App\Http\Controllers\Controller;
use App\Models\Capsula;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CapsulasController extends Controller
{
    function list()
    { 
        return View("capsulas.index");
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new CapsulaExport($query), 'capsulas.xlsx');
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

        if ($request->hasFile('formFile')) 
        {
            $path = $request->file('formFile')->store('capsules', 'public');
            $data['imagen'] = $path;
        }

        if ($request->filled('id')) {
            $entity = Capsula::findOrFail($request->id);
            $entity->update($data);
        } else {
            $entity = Capsula::create($data);
        }

        return response()->json(['message' => 'Stored'], 201);
    }


    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');

        $query = Capsula::select(
            'capsula_id as id',
            'nombre',
            'descripcion',
            'url_video',
            'imagen');

        if(!empty($search))
        {
            $filters = ['nombre', 'descripcion'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
