<?php

namespace App\Http\Controllers;

use App\Exports\LinkExport;
use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LinkController extends Controller
{
    function list()
    { 
        return View("links.index");
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new LinkExport($query), 'links.xlsx');
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
            $data['value'] = $path;
        }

        if ($request->filled('id')) {
            $entity = Link::findOrFail($request->id);
            $entity->update($data);
        } else {
            $entity = Link::create($data);
        }

        return response()->json(['message' => 'Stored'], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = Link::query();

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
