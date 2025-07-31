<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresarios\UnidadProductiva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnidadProductivaController extends Controller
{
    function search(Request $request)
    { 
        $busqueda = $request->term;

        $items = UnidadProductiva::where('nit', 'like', "%{$busqueda}%")
            ->orWhere('business_name', 'like', "%{$busqueda}%")
            ->take(10)
            ->get([
                'unidadproductiva_id as id',
                DB::raw("CONCAT(nit, ' - ', business_name) as text")
            ]);

        
        return response()->json(['results' => $items]);
    }

}
