<?php

namespace App\Http\Controllers\Soporte;

use App\Http\Controllers\Controller;
use App\Models\Soporte\SoporteNovedad;
use Illuminate\Http\Request;

class SoporteNovedadController extends Controller
{
    // Listado para el administrador
    public function index()
    {
        $novedades = SoporteNovedad::all();
        return view('content.soporte.index', compact('novedades'));
    }

    // Guardar nueva novedad (ej. lo de la reunión del viernes)
    public function guardar(Request $solicitud)
    {
        $datosValidados = $solicitud->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estilo_visual' => 'required|string',
        ]);

        SoporteNovedad::create($datosValidados);

        return redirect()->back()->with('success', 'Novedad de soporte publicada correctamente.');
    }
}