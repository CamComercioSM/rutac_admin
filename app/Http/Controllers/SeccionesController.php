<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SeccionesController extends Controller
{
    function index()
    { 
        $data = Section::find(1); 
        return View("secciones.index", ['data'=>$data]);
    }

    public function store(Request $request)
    {
        $section = Section::findOrFail(1);

        $layouts = []; 
        
        // === FOOTER ===
        $footerAttributes = [
            'footer_logo_rutac' => $section->footer['footer_logo_rutac'] ?? '',
            'footer_logo_rutac' => $section->footer['footer_logo_rutac'] ?? '',
            'footer_logo_ally' => $section->footer['footer_logo_ally'] ?? '',
        ];

        foreach (['footer_logo_rutac', 'footer_logo_ally'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $path = $request->file($fileField)->store('logos', 'public');
                $footerAttributes[$fileField] = 'storage/' . $path;
            }
        }

        $footerAttributes['footer_number_contact'] = $request->input('footer_number_contact');
        $footerAttributes['footer_ally_page'] = $request->input('footer_ally_page');
        $footerAttributes['footer_email_contact'] = $request->input('footer_email_contact');
        $footerAttributes['footer_address'] = $request->input('footer_address');

        $layouts[] = [
            'layout' => 'footer',
            'attributes' => $footerAttributes
        ];

        // === HISTORIES ===
        $historiesAttributes = [
            'discover_bg_image' => $section->historia['discover_bg_image'] ?? '',
        ];

        if ($request->hasFile('discover_bg_image')) {
            $path = $request->file('discover_bg_image')->store('backgrounds', 'public');
            $historiesAttributes['discover_bg_image'] = 'storage/' . $path;
        }

        $historiesAttributes['histories_title'] = $request->input('histories_title');
        $historiesAttributes['histories_description'] = $request->input('histories_description');
        $historiesAttributes['discover_title'] = $request->input('discover_title');
        $historiesAttributes['discover_button_1_label'] = $request->input('discover_button_1_label');
        $historiesAttributes['discover_button_1_url'] = $request->input('discover_button_1_url');
        $historiesAttributes['discover_button_2_label'] = $request->input('discover_button_2_label');
        $historiesAttributes['discover_button_2_url'] = $request->input('discover_button_2_url');

        $layouts[] = [
            'layout' => 'Histories',
            'attributes' => $historiesAttributes
        ];

        // === GUARDAR ===
        $section->update([
            'tag' => $request->input('tag'),
            'h1' => $request->input('h1'),
            'video_url' => $request->input('video_url'),
            'seo_title' => $request->input('seo_title'),
            'seo_description' => $request->input('seo_description'),
            'data' => $layouts
        ]);

        return response()->json([
            'message' => 'Sección guardada correctamente'
        ], 201);
    }
}
