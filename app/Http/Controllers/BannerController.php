<?php

namespace App\Http\Controllers;

use App\Exports\BannerExport;
use App\Exports\CapsulaExport;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BannerController extends Controller
{
    function list()
    { 
        return View("banners.index");
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new BannerExport($query), 'banners.xlsx');
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
            $path = $request->file('formFile')->store('banners', 'public');
            $data['image'] = $path;
        }

        if ($request->filled('id')) {
            $entity = Banner::findOrFail($request->id);
            $entity->update($data);
        } else {
            $entity = Banner::create($data);
        }

        return response()->json(['message' => 'Stored'], 201);
    }


    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');

        $query = Banner::query();

        if(!empty($search))
        {
            $filters = ['name', 'title'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
