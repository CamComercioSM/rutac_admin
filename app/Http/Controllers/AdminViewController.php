<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\TablasReferencias\Departamento;
use App\Models\TablasReferencias\Municipio;
use App\Models\TablasReferencias\CiiuActividad;
use App\Models\TablasReferencias\Etapa;
use App\Models\TablasReferencias\UnidadProductivaTamano;
use App\Models\TablasReferencias\UnidadProductivaPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdminViewController extends Controller
{
    public function dashboard(Request $request)
    {
        $datos = UnidadProductiva::select( 't.tamanoNOMBRE as tamano', 's.sectorNOMBRE as sector', DB::raw('COUNT(1) as cantidad'))
        ->leftJoin('unidadesproductivas_tamanos as t', 'unidadesproductivas.tamano_id', '=', 't.tamano_id')
        ->leftJoin('ciiu_macrosectores as s', 'unidadesproductivas.sector_id', '=', 's.sector_id')
        ->groupBy('t.tamanoNOMBRE', 's.sectorNOMBRE')
        ->orderByRaw('t.tamanoNOMBRE IS NULL')
        ->orderByRaw('s.sectorNOMBRE IS NULL')
        ->orderByDesc('cantidad')->get();

        $sectores = $datos->pluck('sector')->unique()->values();

        $data = 
        [
            'cantidadUnidades' => UnidadProductiva::count(),

            'diagnosticos' => UnidadProductiva::
                        select('complete_diagnostic', DB::raw('count(1) as cantidad'))
                        ->groupBy('complete_diagnostic')
                        ->orderByDesc('cantidad')->get()
                        ->map(function ($item) {
                            $item->nombre = $item->complete_diagnostic ? 'Terminado' : 'Pendiente';
                            $item->image = $item->complete_diagnostic ? 'terminado.png' : 'advertencia.png';
                            return $item;
                        }),

            'etapas' => UnidadProductiva::
                          select('t.etapa_id', 't.name as nombre', 't.image', DB::raw('COUNT(1) as cantidad') )
                        ->leftJoin('etapas as t', 'unidadesproductivas.etapa_id', '=', 't.etapa_id')
                        ->groupBy('t.etapa_id', 't.name', 't.image')
                        ->orderByRaw('t.etapa_id IS NULL')->orderBy('t.etapa_id') ->get(),

            'tiposRegistro' => UnidadProductiva::
                          select('t.unidadtipo_id', 't.unidadtipo_nombre as nombre', DB::raw('COUNT(1) as cantidad') )
                        ->leftJoin('unidadesproductivas_tipos as t', 'unidadesproductivas.unidadtipo_id', '=', 't.unidadtipo_id')
                        ->groupBy('t.unidadtipo_id', 't.unidadtipo_nombre')->orderBy('t.unidadtipo_id')->get(),

            
            'tiposOrganizacion' => UnidadProductiva::
                          select('t.tipopersona_id', 't.tipoPersonaNOMBRE as nombre', DB::raw('COUNT(1) as cantidad') )
                        ->leftJoin('unidadesproductivas_personas as t', 'unidadesproductivas.tipopersona_id', '=', 't.tipopersona_id')
                        ->groupBy('t.tipopersona_id', 't.tipoPersonaNOMBRE')
                        ->orderBy('t.tipopersona_id')->get(),

            'municipios' => UnidadProductiva::
                          select('t.municipio_id', 't.municipioNOMBREOFICIAL as nombre', DB::raw('COUNT(1) as cantidad') )
                        ->leftJoin('municipios as t', 'unidadesproductivas.municipality_id', '=', 't.municipio_id')
                        ->groupBy('t.municipio_id', 't.municipioNOMBREOFICIAL')
                        ->orderByDesc('cantidad')->take(6)->get(),

            'sectores'=> $sectores,

            'pivot'=> $datos->groupBy('tamano')->map(function ($grupo) use ($sectores) {
                    $fila = [];
                    foreach ($sectores as $sector) {
                        $fila[$sector] = $grupo->firstWhere('sector', $sector)->cantidad ?? 0;
                    }
                    return $fila;
                }),

            'imgTipoRegistro'=> [ 1=> 'idea_negocio.png', 2=> 'informal_negocio_en_casa.png', 3=> 'registrado_ccsm.png', 4=> 'registrado_fuera_ccsm.png' ],                   
        ];

        return view("dashboard", $data);
    }
    
}
