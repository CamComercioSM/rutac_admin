<?php

namespace App\Http\Controllers;

use App\Exports\CapsulaExport;
use App\Http\Controllers\Controller;
use App\Models\Capsula;
use App\Models\TablasReferencias\Etapa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CapsulasController extends Controller
{
    function list()
    { 
        $data = [
            'etapas'=> Etapa::get()
        ];

        return View("capsulas.index", $data);
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

        $data['data'] = collect($data['data'])->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['etapas'] = $item->etapas->pluck('etapa_id')->toArray();
            return $itemArray;
        })->toArray();

        return response()->json( $data );
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->hasFile('formFile')) 
        {
            $path = $request->file('formFile')->store('storage/capsules', 'public');
            $path = config('app.archivos_url') . $path;
            $data['imagen'] = $path;
        }

        if ($request->filled('id')) {
            $entity = Capsula::findOrFail($request->id);
            $entity->update($data);
        } else {
            $entity = Capsula::create($data);
        }

        $entity->etapas()->detach();
        $entity->etapas()->attach( $request->etapas ?? [] );

        return response()->json(['message' => 'Stored'], 201);
    }


    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = Capsula::with('etapas:etapa_id')
        ->select(
            'capsula_id as id',
            'capsula_id',
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
