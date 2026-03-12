<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function search(Request $request)
    {
        $busqueda = $request->term;

        $items = Lead::where('name', 'like', "%{$busqueda}%")
            ->orWhere('document', 'like', "%{$busqueda}%")
            ->orWhere('email', 'like', "%{$busqueda}%")
            ->orWhere('phone', 'like', "%{$busqueda}%")
            ->take(10)
            ->get([
                'id',
                DB::raw("CONCAT(name, ' - ', COALESCE(document, email, phone)) as text")
            ]);

        return response()->json(['results' => $items]);
    }
}
