<?php

namespace App\Http\Controllers;

use App\Exports\ConvocatoriaExport;
use App\Http\Controllers\Controller;
use App\Models\Programas\InscripcionesRequisitos;
use App\Models\Programas\Programa;
use App\Models\Programas\ProgramaConvocatoria;
use App\Models\Role;
use App\Models\TablasReferencias\Sector;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ConvocatoriaController extends Controller
{
    function list(Request $request)
    { 
        $data = [ 
            'programas'=> Programa::get(),
            'sectores'=> Sector::get(),
            'asesores'=> User::where('rol_id', Role::ASESOR)->get(),
            'preguntas'=> InscripcionesRequisitos::select('requisito_id', 'requisito_titulo')->get(),
            'puedeExportar'=> Auth::user()->rol_id != Role::ASESOR, 
            'filtros'=> $request->all(),
            'esAsesor'=> Auth::user()->rol_id == Role::ASESOR ?  1 : 0
        ];

        return View("convocatorias.index", $data);
    }
    
    function export(Request $request)
    { 
        $query = $this->getQuery($request);
        return Excel::download(new ConvocatoriaExport($query), 'convocatorias.xlsx');
    }

    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        $data = $this->paginate($query, $request);

        $data['data'] = collect($data['data'])->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['asesores'] = $item->asesores->pluck('id')->toArray();
            return $itemArray;
        })->toArray();

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = ProgramaConvocatoria::with(['programa', 'sector', 'asesores', 'requisitosTodos'])->findOrFail($id);

        return view('convocatorias.detail', [ 'detalle' => $result ]);
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

        $entity->requisitosTodos()->detach();
        $requisitosTodos = $request->requisitosTodos ?? [];
        $data = [];
        foreach ($requisitosTodos as $index => $id) {
            $data[$id] = ['orden' => $index];
        }
        $entity->requisitosTodos()->attach($data);

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    function search(Request $request)
    { 
        $busqueda = $request->term;

        $items = ProgramaConvocatoria::where('nombre_convocatoria', 'like', "%{$busqueda}%")
            ->take(10)
            ->get([
                'convocatoria_id as id',
                'nombre_convocatoria  as text'
            ]);

        return response()->json(['results' => $items]);
    }

    public function destroy($id)
    {
        $entity = ProgramaConvocatoria::findOrFail($id);
        $entity->delete();

        return response()->json(['message' => 'Removed']);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');
        $programa = $request->get('programa');
        $matricula = $request->get('matricula');
        $sector = $request->get('sector');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        $query = ProgramaConvocatoria::with(['asesores:id', 'requisitosTodos:requisito_id,requisito_titulo'])->
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
                'p.nombre AS nombre_programa',
                'st.sectorNOMBRE as sector',
            ])
            ->join('programas as p', 'programas_convocatorias.programa_id', '=', 'p.programa_id')
            ->leftJoin('ciiu_macrosectores as st', 'programas_convocatorias.sector_id', '=', 'st.sector_id');

        if(!empty($search))
        {
            $filterts = [
                'p.nombre',
                'programas_convocatorias.nombre_convocatoria', 
                'programas_convocatorias.persona_encargada'
            ];
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

        if(!empty($sector)){
            $query->where('programas_convocatorias.sector_id', $sector);
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $query->whereBetween('programas_convocatorias.fecha_apertura_convocatoria', [$fecha_inicio, $fecha_fin]);
        } elseif (!empty($fecha_inicio)) {
            $query->whereDate('programas_convocatorias.fecha_apertura_convocatoria', '>=', $fecha_inicio);
        } elseif (!empty($fecha_fin)) {
            $query->whereDate('programas_convocatorias.fecha_apertura_convocatoria', '<=', $fecha_fin);
        }

        if(Auth::user()->rol_id == Role::ASESOR)
        {
            $userId = Auth::user()->id;

            $query->whereHas('asesores', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }

        return $query;
    }

    /**
     * Obtener convocatorias por programa
     */
    public function getByPrograma($programa_id)
    {
        try {
            $query = ProgramaConvocatoria::select('convocatoria_id', 'nombre_convocatoria', 'programa_id')
                ->where('programa_id', $programa_id);

            // Si hay usuario autenticado y es asesor, filtrar solo sus convocatorias
            if (Auth::check() && Auth::user()->rol_id == Role::ASESOR) {
                $userId = Auth::id();
                $query->whereHas('asesores', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            }

            $convocatorias = $query->get();

            return response()->json([
                'success' => true,
                'data' => $convocatorias
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener convocatorias: ' . $e->getMessage()
            ], 500);
        }
    }

}
