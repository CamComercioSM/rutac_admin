<?php

namespace App\Http\Controllers;

use App\Exports\ConvocatoriaExport;
use App\Http\Controllers\Controller;
use App\Models\Programas\Programa;
use App\Models\Programas\ProgramaConvocatoria;
use App\Models\Programas\InscripcionesRequisitos;
use App\Models\TablasReferencias\Etapa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProgramaController extends Controller
{
    function list()
    { 
        $data = [
            'etapas'=> Etapa::get(),
            'modalidades'=> Programa::$es_virtual_text,
            'preguntas'=> InscripcionesRequisitos::select('requisito_id', 'requisito_titulo')->get(),
        ];

        return View("programas.index", $data);
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
            $itemArray['etapas'] = $item->etapas->pluck('etapa_id')->toArray();
            return $itemArray;
        })->toArray();

        return response()->json( $data );
    }

    public function show($id)
    {
        $result = Programa::with(['etapas', 'requisitosTodos'])->findOrFail($id);

        return view('programas.detail', [ 'detalle' => $result ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if($request->hasFile('logo_archivo')) 
        {
            $data['logo'] = $request->file('logo_archivo')->store('storage/programas', 'public');
        }

        if($request->hasFile('procedimiento_imagen_archivo')) 
        {
            $data['procedimiento_imagen'] = $request->file('procedimiento_imagen_archivo')->store('storage/programas', 'public');
        }

        if ($request->filled('id')) 
        {
            $entity = Programa::findOrFail($request->id);
            $entity->update($data);
        } 
        else {
            $entity = Programa::create($data);
        }

        $entity->etapas()->detach();
        $entity->etapas()->attach( $request->etapas ?? [] );

        $entity->requisitosTodos()->detach();
        $requisitosTodos = $request->requisitosTodos ?? [];
        $data = [];
        foreach ($requisitosTodos as $index => $id) {
            $data[$id] = ['orden' => $index];
        }
        $entity->requisitosTodos()->attach($data);

        return response()->json([ 'message' => 'Stored' ], 201);
    }

    private function getQuery(Request $request)
    {
        $search = $request->get('search');

        $query = Programa::with(['etapas:etapa_id', 'requisitosTodos:requisito_id,requisito_titulo'])->
            select([
                'programa_id AS id',
                'programa_id',
                'nombre',                    
                'descripcion',
                'logo',
                'beneficios',
                'requisitos',
                'duracion',
                'dirigido_a',
                'objetivo',
                'determinantes',
                'procedimiento_imagen',
                'herramientas_requeridas',
                'es_virtual',
                'informacion_adicional',
                'sitio_web',
                'codigo_pac'
            ]);

        if(!empty($search))
        {
            $filterts = ['nombre', 'codigo_pac'];
            $query->where(function ($q) use ($search, $filterts) {
                foreach ($filterts as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

       

        return $query;
    }
}
