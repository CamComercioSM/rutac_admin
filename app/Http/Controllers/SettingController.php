<?php

namespace App\Http\Controllers;

use App\Exports\LinkExport;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SettingController extends Controller
{
    function list()
    { 
        return View("settings.index");
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new LinkExport($query), 'settings.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        $data['data'] = collect($data['data'])->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['type_name'] = Setting::$types[$itemArray['type']];
            return $itemArray;
        })->toArray();

        return response()->json( $data );
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->hasFile('formFile')) 
        {
            $path = $request->file('formFile')->store('storage/history', 'public');
            $path = config('app.archivos_url') . $path;
            $data['value'] = $path;
        }

        if ($request->filled('id')) {
            $entity = Setting::findOrFail($request->id);
            $entity->update($data);
        } else {
            $entity = Setting::create($data);
        }

        return response()->json(['message' => 'Stored'], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = Setting::query();

        if(!empty($search))
        {
            $filters = ['type'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
