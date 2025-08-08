<?php

namespace App\Http\Controllers;

use App\Exports\UsuariosExport;
use App\Http\Controllers\Controller;
use App\Models\Programas\Programa;
use App\Models\Programas\ProgramaConvocatoria;
use App\Models\TablasReferencias\Sector;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ConvocatoriaController extends Controller
{
    function list()
    { 
        $data = [ 
            'programas'=> Programa::get(),
            'sectores'=> Sector::get(),
            'asesores'=> User::where('rol_id', 3)->get() 
        ];

        return View("convocatorias.index", $data);
    }
    
    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new UsuariosExport($query), 'convocatorias.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        $data['rows'] = collect($data['rows'])->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['asesores'] = $item->asesores->pluck('id')->toArray();
            return $itemArray;
        })->toArray();

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = ProgramaConvocatoria::findOrFail($id);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->filled('id')) 
        {
            $entity = ProgramaConvocatoria::findOrFail($request->id);
            $entity->update($data);
        } 
        else {
            $entity = ProgramaConvocatoria::create($data);
        }

        $entity->asesores()->detach();
        $entity->asesores()->attach( $request->asesores ?? [] );

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    public function destroy($id)
    {
        $entity = ProgramaConvocatoria::findOrFail($id);
        $entity->status = false;
        $entity->save();

        return response()->json(['message' => 'Removed']);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('searchText');
        $programa = $request->get('programa');
        $matricula = $request->get('matricula');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        $query = ProgramaConvocatoria::with(['asesores:id'])->
            select([
                'programas_convocatorias.convocatoria_id AS id',
                'programas_convocatorias.convocatoria_id',
                'programas_convocatorias.nombre_convocatoria',
                'programas_convocatorias.persona_encargada',
                'programas_convocatorias.correo_contacto',
                'programas_convocatorias.telefono',
                'programas_convocatorias.fecha_apertura_convocatoria',
                'programas_convocatorias.fecha_cierre_convocatoria',
                'programas_convocatorias.con_matricula',
                'programas_convocatorias.sector_id',
                'p.programa_id',
                'p.nombre AS nombre_programa'
            ])
            ->join('programas as p', 'programas_convocatorias.programa_id', '=', 'p.programa_id');

        if(!empty($search))
        {
            $filterts = ['programas_convocatorias.nombre_convocatoria', 'programas_convocatorias.persona_encargada'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        if(!empty($programa)){
            $query->where('p.programa_id', $programa);
        }

        if(!empty($matricula)){
            $query->where('programas_convocatorias.con_matricula', $matricula);
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query->whereBetween('programas_convocatorias.fecha_apertura_convocatoria', [$fecha_inicio, $fecha_fin]);
        } elseif (!empty($fecha_inicio)) {
            $query->whereDate('programas_convocatorias.fecha_apertura_convocatoria', '>=', $fecha_inicio);
        } elseif (!empty($fecha_fin)) {
            $query->whereDate('programas_convocatorias.fecha_apertura_convocatoria', '<=', $fecha_fin);
        }

        return $query;
    }
}
