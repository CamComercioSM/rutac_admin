<?php

namespace App\Http\Controllers;

use App\Exports\CronExport;
use App\Http\Controllers\Controller;
use App\Models\Crons\Cron;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CronController extends Controller
{
    function list()
    { 
        return View("crons.index");
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new CronExport($query), 'crons.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = Cron::findOrFail($id);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        
        if ($request->filled('id')) 
        {
            $entity = Cron::findOrFail($request->id);
            $entity->update($data);
        } 
        else {
            $entity = Cron::create($data);
        }

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    public function destroy($id)
    {
        $entity = Cron::findOrFail($id);
        $entity->delete();

        return response()->json(['message' => 'Removed']);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');

        $query = Cron::query();

        if(!empty($search))
        {
            $filterts = ['nombre', 'descripcion'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
