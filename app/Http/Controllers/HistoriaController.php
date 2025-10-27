<?php

namespace App\Http\Controllers;

use App\Exports\HistoriaExport;
use App\Http\Controllers\Controller;
use App\Models\Historia;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HistoriaController extends Controller
{
    function list()
    { 
        return View("historias.index");
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new HistoriaExport($query), 'historias.xlsx');
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
            $path = $request->file('formFile')->store('storage/history', 'public');
            $path = config('app.archivos_url') . $path;
            $data['image'] = $path;
        }

        if ($request->filled('id')) {
            $entity = Historia::findOrFail($request->id);
            $entity->update($data);
        } else {
            $entity = Historia::create($data);
        }

        return response()->json(['message' => 'Stored'], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = Historia::query();

        if(!empty($search))
        {
            $filters = ['name'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
