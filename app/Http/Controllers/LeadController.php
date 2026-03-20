<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller {
    public function search(Request $request) {
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

    public function store(Request $request) {
        $data = $request->validate([
            'type' => 'required|string|max:2',
            'document' => 'nullable|string|max:48',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:28',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
        ]);

        $lead = Lead::create($data);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $lead->id,
                'text' => $lead->name . ' - ' . ($lead->document ?? $lead->phone)
            ]
        ]);
    }
}
