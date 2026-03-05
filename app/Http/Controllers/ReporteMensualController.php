<?php

namespace App\Http\Controllers;

use App\Models\ReporteMensual;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReporteMensualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reporteMensual.index');
    }

    public function reportesMensuales(): JsonResponse
    {
        $reportes = ReporteMensual::with(['asesor:id,name', 'supervisor:id,name'])->get();

        return response()->json([
            'data' => $reportes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
