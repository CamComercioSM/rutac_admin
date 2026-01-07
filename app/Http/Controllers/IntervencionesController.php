<?php

namespace App\Http\Controllers;

use App\Exports\IntervencionesExport;
use App\Imports\UnidadProductivaIntervencionesImport;
use App\Http\Controllers\Controller;
use App\Models\Empresarios\UnidadProductivaIntervenciones;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\User;
use App\Models\Role;
use App\Models\TablasReferencias\CategoriasIntervenciones;
use App\Models\TablasReferencias\TiposIntervenciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class IntervencionesController extends Controller
{
    function list(Request $request)
    { 
        $data = [ 
            'categorias'=> CategoriasIntervenciones::get(),
            'tipos'=> TiposIntervenciones::get(),
            'modalidades'=> UnidadProductivaIntervenciones::$modalidades,
            'asesores' => User::whereNotNull('rol_id')->get(),
            'esAsesor'=> Auth::user()->rol_id == Role::ASESOR ?  1 : 0,
            'filtros'=> $request->all(),
            'unidades'=> []
        ];

        if ($unidad = $request->get('unidad')) {
            $data['unidades'] = UnidadProductiva::where('unidadproductiva_id', $unidad)->get();
        }

        return View("intervenciones.index", $data);
    }

    public function informe(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date',
        ]);

        $fi = $request->fecha_inicio;
        $ff = $request->fecha_fin;

        // ----- LISTADO DETALLADO -----
        $query = UnidadProductivaIntervenciones::with([
            'unidadProductiva',
            'asesor',
            'categoria',
            'tipo'
        ])
        ->whereBetween('fecha_inicio', [$fi, $ff])
        ->orderBy('fecha_inicio', 'ASC');

        $asesor = (Auth::user()->rol_id === Role::ASESOR) ? Auth::id() : $request->get('asesor');
        if ($asesor) {
            $query->where('asesor_id', $asesor);
        }

        if ($unidad = $request->get('unidad')) {
            $query->where('unidadproductiva_id', $unidad);
        }

        // ----- AGRUPACIONES -----
        // Conteo por Categoría
        $porCategoria = UnidadProductivaIntervenciones::select(
                'categoria_id',
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('fecha_inicio', [$fi, $ff])
            ->groupBy('categoria_id')
            ->with('categoria')
            ->get();

        // Conteo por Tipo
        $porTipo = UnidadProductivaIntervenciones::select(
                'tipo_id',
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('fecha_inicio', [$fi, $ff])
            ->groupBy('tipo_id')
            ->with('tipo')
            ->get();

        // Conteo por Unidad Productiva
        $porUnidad = UnidadProductivaIntervenciones::select(
                'unidadproductiva_id',
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('fecha_inicio', [$fi, $ff])
            ->groupBy('unidadproductiva_id')
            ->with('unidadProductiva')
            ->get();

        
        $data = [
            'inicio' => Carbon::parse($fi)->translatedFormat('Y-m-d H:i'),
            'fin'    => Carbon::parse($ff)->translatedFormat('Y-m-d H:i'),
            'conclusiones' => $request->get('conclusiones', ''),
            'intervenciones' => $query->get(),
            'porCategoria' => $porCategoria,
            'porTipo' => $porTipo,
            'porUnidad' => $porUnidad,
            'totalGeneral' => $query->count(),
        ];

        $pdf = PDF::loadView('intervenciones.informe', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('informe_intervenciones.pdf');
    }

    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new IntervencionesExport($query), 'Intervenciones.xlsx');
    }

    public function import(Request $request)
    {
        Excel::import(new UnidadProductivaIntervencionesImport, $request->file('archivo'));

        return back()->with('ok', 'Datos cargados correctamente.');
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
            $path = config('app.archivos_url') . $path;
            $data['soporte'] = $path;
        }

        foreach($request->unidades as $item)
        {
            $data['unidadproductiva_id'] = $item['unidadproductiva_id'];
            $data['participantes'] = $item['participantes'];
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
                'categorias_intervenciones.nombre as categoria',
                'tipos_intervenciones.nombre as tipo',
            ])
            ->join('categorias_intervenciones', 'categorias_intervenciones.id', '=', 'unidadesproductivas_intervenciones.categoria_id')
            ->join('tipos_intervenciones', 'tipos_intervenciones.id', '=', 'unidadesproductivas_intervenciones.tipo_id')
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
