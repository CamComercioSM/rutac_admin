<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DefaultEmailTemplate;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DefaultEmailTemplateController extends Controller
{
    /**
     * Mostrar la lista de plantillas por defecto
     */
    public function index()
    {
        $defaultTemplates = DefaultEmailTemplate::with('emailTemplate')
            ->orderBy('process_type')
            ->get();

        $availableProcessTypes = DefaultEmailTemplate::getAvailableProcessTypes();
        $emailTemplates = EmailTemplate::orderBy('name')->get();

        return view('default-email-templates.index', compact(
            'defaultTemplates',
            'availableProcessTypes',
            'emailTemplates'
        ));
    }

    /**
     * Mostrar el formulario para crear una nueva plantilla por defecto
     */
    public function create()
    {
        $availableProcessTypes = DefaultEmailTemplate::getAvailableProcessTypes();
        $emailTemplates = EmailTemplate::orderBy('name')->get();

        return view('default-email-templates.create', compact(
            'availableProcessTypes',
            'emailTemplates'
        ));
    }

    /**
     * Almacenar una nueva plantilla por defecto
     */
    public function store(Request $request)
    {
        $request->validate([
            'process_type' => [
                'required',
                'string',
                Rule::in(array_keys(DefaultEmailTemplate::getAvailableProcessTypes())),
                'unique:default_email_templates,process_type'
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email_template_id' => 'required|exists:email_templates,id',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Si se está activando esta plantilla, desactivar otras del mismo tipo
            if ($request->boolean('is_active')) {
                DefaultEmailTemplate::where('process_type', $request->process_type)
                    ->update(['is_active' => false]);
            }

            DefaultEmailTemplate::create([
                'process_type' => $request->process_type,
                'name' => $request->name,
                'description' => $request->description,
                'email_template_id' => $request->email_template_id,
                'is_active' => $request->boolean('is_active')
            ]);

            DB::commit();

            return redirect()->route('admin.default-email-templates.index')
                ->with('success', 'Plantilla por defecto creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al crear la plantilla por defecto: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar el formulario para editar una plantilla por defecto
     */
    public function edit(DefaultEmailTemplate $defaultEmailTemplate)
    {
        $availableProcessTypes = DefaultEmailTemplate::getAvailableProcessTypes();
        $emailTemplates = EmailTemplate::orderBy('name')->get();

        return view('default-email-templates.edit', compact(
            'defaultEmailTemplate',
            'availableProcessTypes',
            'emailTemplates'
        ));
    }

    /**
     * Actualizar una plantilla por defecto
     */
    public function update(Request $request, DefaultEmailTemplate $defaultEmailTemplate)
    {
        $request->validate([
            'process_type' => [
                'required',
                'string',
                Rule::in(array_keys(DefaultEmailTemplate::getAvailableProcessTypes())),
                Rule::unique('default_email_templates', 'process_type')->ignore($defaultEmailTemplate->id)
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email_template_id' => 'required|exists:email_templates,id',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Si se está activando esta plantilla, desactivar otras del mismo tipo
            if ($request->boolean('is_active')) {
                DefaultEmailTemplate::where('process_type', $request->process_type)
                    ->where('id', '!=', $defaultEmailTemplate->id)
                    ->update(['is_active' => false]);
            }

            $defaultEmailTemplate->update([
                'process_type' => $request->process_type,
                'name' => $request->name,
                'description' => $request->description,
                'email_template_id' => $request->email_template_id,
                'is_active' => $request->boolean('is_active')
            ]);

            DB::commit();

            return redirect()->route('admin.default-email-templates.index')
                ->with('success', 'Plantilla por defecto actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al actualizar la plantilla por defecto: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar una plantilla por defecto
     */
    public function destroy(DefaultEmailTemplate $defaultEmailTemplate)
    {
        try {
            $defaultEmailTemplate->delete();
            return redirect()->route('admin.default-email-templates.index')
                ->with('success', 'Plantilla por defecto eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la plantilla por defecto: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar el estado activo de una plantilla por defecto
     */
    public function toggleActive(DefaultEmailTemplate $defaultEmailTemplate)
    {
        try {
            DB::beginTransaction();

            if ($defaultEmailTemplate->is_active) {
                // Desactivar la plantilla
                $defaultEmailTemplate->update(['is_active' => false]);
                $message = 'Plantilla desactivada exitosamente.';
            } else {
                // Desactivar otras plantillas del mismo tipo y activar esta
                DefaultEmailTemplate::where('process_type', $defaultEmailTemplate->process_type)
                    ->update(['is_active' => false]);
                
                $defaultEmailTemplate->update(['is_active' => true]);
                $message = 'Plantilla activada exitosamente.';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $defaultEmailTemplate->fresh()->is_active
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener plantilla activa para un tipo de proceso específico
     */
    public function getActiveTemplate(string $processType)
    {
        $template = DefaultEmailTemplate::getActiveTemplate($processType);
        
        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró plantilla activa para ' . $processType
            ], 404);
        }

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Vista previa de una plantilla por defecto
     */
    public function preview(DefaultEmailTemplate $defaultEmailTemplate)
    {
        $template = $defaultEmailTemplate->emailTemplate;
        
        // Datos de ejemplo para la previsualización
        $sampleData = [
            'business_name' => 'Mi Empresa',
            'contact_person' => 'Juan Pérez',
            'project_name' => 'Ruta C',
            'current_year' => date('Y'),
            'reset_link' => 'https://ejemplo.com/reset-password?token=abc123',
            'verification_link' => 'https://ejemplo.com/verify?token=xyz789'
        ];

        return view('default-email-templates.preview', compact('template', 'sampleData'));
    }
}
