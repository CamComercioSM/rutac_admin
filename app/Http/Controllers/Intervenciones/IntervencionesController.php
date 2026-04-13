<?php

namespace App\Http\Controllers\Intervenciones;

use App\Exports\IntervencionesExport;
use App\Imports\UnidadProductivaIntervencionesImport;
use App\Http\Controllers\Controller;
use App\Models\Empresarios\UnidadProductiva;
use App\Models\Intervenciones\UnidadProductivaIntervenciones;
use App\Models\Intervenciones\IntervencionLead;
use App\Models\Intervenciones\IntervencionUnidad;
use App\Models\Intervenciones\ReporteMensual;
use App\Models\Lead;
use App\Models\Programas\FasePrograma;
use App\Models\Programas\Programa;
use App\Models\Programas\ProgramaConvocatoria;
use App\Models\User;
use App\Models\Role;
use App\Models\TablasReferencias\CategoriasIntervenciones;
use App\Models\TablasReferencias\TiposIntervenciones;
use App\Services\IAService;
use App\Services\IntervencionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class IntervencionesController extends Controller {

    /**
     * Muestra la vista principal con los datos para los selectores.
     */
    public function list(Request $request) {
        $data = [
            'programas' => Programa::get(),
            'convocatorias' => ProgramaConvocatoria::get(),
            'categorias' => CategoriasIntervenciones::get(),
            'fasesProgramas' => FasePrograma::get(),
            'tipos' => TiposIntervenciones::get(),
            'modalidades' => UnidadProductivaIntervenciones::$modalidades,
            'asesores' => User::whereNotNull('rol_id')->get(),
            'esAsesor' => Auth::user()->rol_id == Role::ASESOR ? 1 : 0,
            'filtros' => $request->all(),
            'unidades' => [],
            'leads' => [],
        ];

        if ($unidad = $request->get('unidad')) {
            $data['unidades'] = UnidadProductiva::where('unidadproductiva_id', $unidad)->get();
        }

        if ($lead = $request->get('otroParticipante')) {
            $data['leads'] = Lead::where('id', $lead)->get();
        }

        return view("intervenciones.index", $data);
    }

    /**
     * Endpoint para DataTables. 
     * Delegamos la construcción del Query al Servicio.
     */
    public function index(Request $request, IntervencionService $service) {
        $filters = $request->all();

        $query = $service->getListQuery($filters, Auth::user());

        $total = $query->count();

        $data = $query
            ->skip(($filters['page'] - 1) * $filters['pageSize'])
            ->take($filters['pageSize'])
            ->get();

        return response()->json([
            'data' => $data,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        ]);
    }

    /**
     * Exportación a Excel usando el Query del servicio.
     */
    public function export(Request $request, IntervencionService $service) {
        $query = $service->getListQuery($request->all());
        return Excel::download(new IntervencionesExport($query), 'Intervenciones.xlsx');
    }

    /**
     * Importación masiva desde Excel.
     */
    public function import(Request $request) {
        // 1. Validar que el archivo exista y sea del tipo correcto
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240', // Máx 10MB
        ]);

        try {
            // 2. Ejecutar la importación
            Excel::import(new UnidadProductivaIntervencionesImport, $request->file('archivo'));
            return back()->with('ok', 'Los datos de las intervenciones han sido cargados correctamente.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->with('error', 'Error en la importación. Revisa el formato del archivo.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error crítico: ' . $e->getMessage());
        }
    }


    /**
     * Guardado de nueva intervención.
     */
    public function store(Request $request, IntervencionService $service) {
        try {
            DB::beginTransaction();

            $data = $request->only([
                'programa_id',
                'convocatoria_id',
                'fase_id',
                'categoria_id',
                'tipo_id',
                'fecha_inicio',
                'fecha_fin',
                'modalidad',
                'descripcion',
                'conclusiones'
            ]);

            $data['asesor_id'] = Auth::id();


            $estadoInput = strtoupper($request->estado_guardado ?? 'BORRADOR');
            $data['estado'] = match ($estadoInput) {
                'FIRME' => 'REPORTADO',
                default => 'BORRADOR'
            };

            // ELIMINAR Archivo
            if ($request->eliminarSoporte == '1') {

                // eliminar archivo físico si existe
                if (!empty($request->soporteActual)) {

                    $ruta = str_replace(config('app.archivos_url'), '', $request->soporteActual);
                    $rutaFisica = storage_path('app/public/' . $ruta);

                    if (file_exists($rutaFisica)) {
                        unlink($rutaFisica); // ⚠️ cuidado permisos
                    }
                }

                $data['soporte'] = null;
            }

            // 2. NUEVO ARCHIVO
            if ($request->hasFile('formFile')) {

                // eliminar anterior si existe (reemplazo)
                if (!empty($request->soporteActual)) {

                    $ruta = str_replace(config('app.archivos_url'), '', $request->soporteActual);
                    $rutaFisica = storage_path('app/public/' . $ruta);

                    if (file_exists($rutaFisica)) {
                        unlink($rutaFisica);
                    }
                }

                $path = $request->file('formFile')->store('intervenciones', 'public');
                $data['soporte'] = config('app.archivos_url') . $path;
            }

            // Participantes
            $unidades = is_string($request->unidades)
                ? json_decode($request->unidades, true)
                : ($request->unidades ?? []);

            $leads = is_string($request->otrosParticipantes)
                ? json_decode($request->otrosParticipantes, true)
                : ($request->otrosParticipantes ?? []);

            if ($request->id) {
                $intervencion = UnidadProductivaIntervenciones::findOrFail($request->id);
                $intervencion->update($data);
                $service->syncParticipantes($intervencion, $unidades, $leads);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Actualizado correctamente'
                ]);
            } else {
                $intervencion = $service->storeIntervencion($data, $unidades, $leads);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Guardado con {$intervencion->participantes} asistentes."
                ], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminación con SoftDeletes.
     */
    public function destroy($id) {
        try {
            DB::beginTransaction();
            $intervencion = UnidadProductivaIntervenciones::findOrFail($id);

            // Eliminamos relaciones y el padre
            $intervencion->unidades()->delete();
            $intervencion->leads()->delete();
            $intervencion->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Registro eliminado correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show($id) {
        $intervencion = UnidadProductivaIntervenciones::with([
            'unidades.unidadProductiva',
            'leads.lead',
            'categoria',
            'tipo',
            'fase',
            'asesor'
        ])->findOrFail($id);

        return response()->json($intervencion);
    }

    public function edit($id) {
        $intervencion = UnidadProductivaIntervenciones::findOrFail($id);

        // Cargamos los mismos datos del método list() para los selects
        $data = [
            'intervencion'  => $intervencion,
            'programas'     => Programa::get(),
            'convocatorias' => ProgramaConvocatoria::get(),
            'categorias'    => CategoriasIntervenciones::get(),
            'tipos'         => TiposIntervenciones::get(),
            // Cargamos las unidades y leads ya asociados para que aparezcan en el Select2/Tagify
            'unidadesAsociadas' => $intervencion->unidades()->with('unidadProductiva')->get(),
            'leadsAsociados'    => $intervencion->leads()->with('lead')->get(),
        ];

        return view('intervenciones.edit', $data);
    }



    /**
     * Muestra la previsualización del informe antes de generar el PDF o guardar.
     */
    public function preview(Request $request, IntervencionService $service, IAService $iaService) {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date',
        ]);

        // Obtenemos la data procesada desde el servicio
        $data = $service->getInformeData($request->all(), Auth::user());

        // Análisis de IA (Lógica externa o privada del controlador)
        $data['analisis_ia'] = $iaService->analizarInforme($request, $data);

        // Creamos el registro del reporte (Estado inicial)
        $reporte = ReporteMensual::create([
            'asesor_id'            => Auth::id(),
            'fecha_generacion'     => now(),
            'total_intervenciones' => $data['totalGeneral'] ?? 0,
            'total_unidades'       => count($data['porUnidad'] ?? []),
            'conclusiones'         => $data['conclusiones'] ?? '',
            'usuario_creo'         => Auth::id(),
            'usuario_actualizo'    => Auth::id(),
        ]);

        $data['reporte'] = $reporte;
        $data['reporte_id'] = $reporte->id;
        $data['asesor'] = $reporte->asesor;
        $data['supervisor'] = $reporte->supervisor; 
        return view('intervenciones.preview', $data);
    }

    /**
     * Genera el PDF para visualización inmediata (Stream).
     */
    public function informe(Request $request, IntervencionService $service) {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date',
            'reporte_id'   => 'required|exists:reportes_mensuales,id'
        ]);



        $data = $service->getInformeData($request->all(), Auth::user());
        $data['analisis_ia'] = null; // Opcional en el PDF según tu lógica
        $reporte = \App\Models\Intervenciones\ReporteMensual::with(['asesor', 'supervisor'])
            ->findOrFail($request->reporte_id);
        $data['reporte'] = $reporte;
        $data['asesor'] = $reporte->asesor;
        $data['supervisor'] = $reporte->supervisor;


        // Asegurar que la carpeta de almacenamiento existe
        $carpeta = public_path('storage/InformesIntervenciones');
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        $pdf = PDF::loadView('intervenciones.informe', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('informe_intervenciones_' . uniqid() . '.pdf');
    }

    /**
     * Guarda el informe físico (PDF) y actualiza el registro mensual en la DB.
     */
    public function saveInforme(Request $request, IntervencionService $service) {
        $request->validate([
            'mes'  => 'required|integer',
            'anio' => 'required|integer',
            'reporte_id' => 'required|exists:reportes_mensuales,id'
        ]);

        $reporteId = $request->input('reporte_id');
        $asesorId  = Auth::id();

        try {
            DB::beginTransaction();

            // 🔴 Validar duplicado
            if ($service->validarReporteDuplicado($asesorId, $request->anio, $request->mes, $reporteId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un informe para este asesor en el periodo seleccionado.'
                ], 422);
            }

            $data = $service->getInformeData($request->all(), Auth::user());
            $nombreArchivo = "informe_" . time() . ".pdf";
            $rutaPublica = "informes/{$nombreArchivo}";

            $service->generarInformePDF($data, $rutaPublica);
            ReporteMensual::where('id', $reporteId)->update([
                'anio'              => $request->anio,
                'mes'               => $request->mes,
                'conclusiones'      => $data['conclusiones'] ?? '',
                'estado'            => 'PENDIENTE_REVISION',
                'informe_url'       => $rutaPublica,
                'usuario_actualizo' => $asesorId,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'url' => asset($rutaPublica),
                'message' => 'Informe guardado y enviado a revisión.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
