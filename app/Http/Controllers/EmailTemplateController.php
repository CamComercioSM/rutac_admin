<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;

class EmailTemplateController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function index()
    {
        $templates = EmailTemplate::orderBy('name')->get();
        return view('email-templates.index', compact('templates'));
    }

    public function create()
    {
        // Datos de ejemplo para mostrar en el formulario
        $sampleData = [
            'business_name' => 'Mi Empresa S.A.S.',
            'contact_person' => 'Juan Pérez',
            'project_name' => 'Ruta C',
            'current_year' => date('Y'),
        ];
        
        return view('email-templates.create', compact('sampleData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'variables' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        EmailTemplate::create($request->all());

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Plantilla creada exitosamente.');
    }

    public function show(EmailTemplate $emailTemplate)
    {
        // Datos de ejemplo para mostrar en la vista
        $sampleData = [
            'business_name' => 'Mi Empresa S.A.S.',
            'contact_person' => 'Juan Pérez',
            'project_name' => 'Ruta C',
            'current_year' => date('Y'),
        ];
        
        return view('email-templates.show', compact('emailTemplate', 'sampleData'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        // Datos de ejemplo para mostrar en el formulario
        $sampleData = [
            'business_name' => 'Mi Empresa S.A.S.',
            'contact_person' => 'Juan Pérez',
            'project_name' => 'Ruta C',
            'current_year' => date('Y'),
        ];
        
        return view('email-templates.edit', compact('emailTemplate', 'sampleData'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'variables' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        $emailTemplate->update($request->all());

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Plantilla actualizada exitosamente.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Plantilla eliminada exitosamente.');
    }

    public function preview(Request $request, EmailTemplate $emailTemplate)
    {
        $variables = $request->input('variables', []);
        
        // Generar contenido con variables reemplazadas
        $htmlContent = $this->replaceVariables($emailTemplate->html_content, $variables);
        $textContent = $this->replaceVariables($emailTemplate->text_content, $variables);
        
        return response()->json([
            'html_content' => $htmlContent,
            'text_content' => $textContent,
            'subject' => $emailTemplate->subject
        ]);
    }

    /**
     * Enviar email de prueba
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
        ]);

        try {
            $testEmail = $request->input('test_email');
            $subject = $request->input('subject');
            $htmlContent = $request->input('html_content');
            $textContent = $request->input('text_content');

            // Generar el email completo con header y footer
            $fullHtmlContent = $this->generateFullEmail($htmlContent);
            $fullTextContent = $this->generateFullTextEmail($textContent);

            // Enviar email de prueba
            $this->mailService->sendTestEmail($testEmail, $subject, $fullHtmlContent, $fullTextContent);

            return response()->json([
                'success' => true,
                'message' => 'Email de prueba enviado exitosamente a ' . $testEmail
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar email de prueba: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar email completo con header y footer
     */
    private function generateFullEmail($content)
    {
        $currentYear = date('Y');
        $projectName = 'Ruta C';
        
        return '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Email de Prueba</title>
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8f9fa;">
            <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <!-- Header -->
                <div style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); padding: 40px 20px 30px; text-align: center; position: relative;">
                    <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 30px; margin-bottom: 25px;">
                        <div style="width: 180px; height: 100px; background-color: transparent; display: flex; align-items: center; justify-content: center;">
                            <img src="https://cdnsicam.net/img/rutac/rutac_blanco.png" alt="Ruta C Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <p style="color: #ffffff; text-align: left; font-size: 20px; font-weight: 600; margin: 0; opacity: 0.9;">Haz crecer tu negocio</p>
                    </div>
                    <h2 style="color: #ffffff; font-size: 32px; font-weight: 600; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">Email de Prueba</h2>
                </div>
                
                <!-- Content -->
                <div style="padding: 40px 30px; background-color: #ffffff;">
                    ' . $content . '
                </div>
                
                <!-- Footer -->
                <div style="background-color: #1e3a8a; color: #ffffff; text-align: center; padding: 25px 30px; font-size: 14px;">
                    <p style="margin: 5px 0; color: #cbd5e1;">Este es un correo de prueba del constructor de plantillas.</p>
                    <p style="color: #94a3b8; font-size: 12px;">&copy; ' . $currentYear . ' ' . $projectName . '. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Generar email de texto plano completo
     */
    private function generateFullTextEmail($content)
    {
        $currentYear = date('Y');
        $projectName = 'Ruta C';
        
        return "RUTA C - Email de Prueba\n" .
               "========================\n\n" .
               $content . "\n\n" .
               "---\n" .
               "Este es un correo de prueba del constructor de plantillas.\n" .
               "© {$currentYear} {$projectName}. Todos los derechos reservados.";
    }

    /**
     * Reemplazar variables en el contenido
     */
    private function replaceVariables($content, $variables)
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{ $' . $key . ' }}', $value, $content);
        }
        return $content;
    }

    public function toggleStatus(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update(['is_active' => !$emailTemplate->is_active]);
        
        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Estado de la plantilla actualizado.');
    }
}
