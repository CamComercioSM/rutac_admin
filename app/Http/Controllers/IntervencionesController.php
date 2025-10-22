<?php

namespace App\Http\Controllers;

use App\Exports\IntervencionesExport;
use App\Http\Controllers\Controller;
use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class IntervencionesController extends Controller
{
    function list(Request $request)
    { 
        $data = [ 
            'asesores'=> User::where('rol_id', Role::ASESOR)->get(),
            'esAsesor'=> Auth::user()->rol_id == Role::ASESOR ?  1 : 0,
            'filtros'=> $request->all(),
            'unidades'=> []
        ];

        if ($unidad = $request->get('unidad')) {
            $data['unidades'] = UnidadProductiva::where('unidadproductiva_id', $unidad)->get();
        }

        return View("Intervenciones.index", $data);
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new IntervencionesExport($query), 'Intervenciones.xlsx');
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

        $data['asesor_id'] = Auth::user()->id;

        if ($request->hasFile('formFile')) 
        {
            $path = $request->file('formFile')->store('storage/Intervenciones', 'public');
            $data['soporte'] = $path;
        }

        foreach($request->unidades as $id)
        {
            $data['unidadproductiva_id'] = $id;
            $entity = UnidadProductivaIntervenciones::create($data);
        }

        return response()->json(['message' => 'Stored'], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = UnidadProductivaIntervenciones::query()
            ->select([
                'unidadesproductivas_intervenciones.*',
                DB::raw("CONCAT(users.name, ' ', users.lastname) as asesor"),
                'unidadesproductivas.business_name as unidad',
            ])
            ->join('users', 'users.id', '=', 'unidadesproductivas_intervenciones.asesor_id')
            ->join('unidadesproductivas', 'unidadesproductivas.unidadproductiva_id', '=', 'unidadesproductivas_intervenciones.unidadproductiva_id');


        $asesor = (Auth::user()->rol_id === Role::ASESOR) ? Auth::id() : $request->get('asesor');
        if ($asesor) {
            $query->where('unidadesproductivas_intervenciones.asesor_id', $asesor);
        }

        if ($unidad = $request->get('unidad')) {
            $query->where('unidadesproductivas_intervenciones.unidadproductiva_id', $unidad);
        }

        if ($fechaInicio = $request->get('fecha_inicio')) {
            $query->whereDate('fecha_inicio', '>=', $fechaInicio);
        }

        if ($fechaFin = $request->get('fecha_fin')) {
            $query->whereDate('fecha_fin', '<=', $fechaFin);
        }

        if(!empty($search))
        {
            $filters = ['descripcion'];
            $query->where(function ($q) use ($search, $filters) {
                foreach ($filters as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
