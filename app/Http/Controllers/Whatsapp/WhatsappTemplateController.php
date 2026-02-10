<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\StoreTemplateRequest;
use App\Http\Requests\Whatsapp\UpdateTemplateRequest;
use App\Models\WhatsappTemplate;
use App\Models\WhatsappTemplateCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsappTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $query = WhatsappTemplate::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $templates = $query->orderBy('name')->get();
        $categories = WhatsappTemplateCategory::orderBy('name')->get();

        return view('whatsapp.templates.index', compact('templates', 'categories'));
    }

    public function create(): View
    {
        $categories = WhatsappTemplateCategory::where('is_active', true)->orderBy('name')->get();
        return view('whatsapp.templates.create', compact('categories'));
    }

    public function store(StoreTemplateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['channel'] = $data['channel'] ?? 'whatsapp';
        $data['expected_fields'] = $this->parseJsonField($data['expected_fields'] ?? null);
        $data['default_payload'] = $this->parseJsonField($data['default_payload'] ?? null, true);
        $data['is_active'] = $request->boolean('is_active', true);

        WhatsappTemplate::create($data);
        return redirect()->route('admin.whatsapp.templates.index')
            ->with('success', 'Plantilla creada correctamente.');
    }

    public function show(WhatsappTemplate $template): View
    {
        $template->load('category');
        return view('whatsapp.templates.show', compact('template'));
    }

    public function edit(WhatsappTemplate $template): View
    {
        $categories = WhatsappTemplateCategory::orderBy('name')->get();
        return view('whatsapp.templates.edit', compact('template', 'categories'));
    }

    public function update(UpdateTemplateRequest $request, WhatsappTemplate $template): RedirectResponse
    {
        $data = $request->validated();
        $data['expected_fields'] = $this->parseJsonField($data['expected_fields'] ?? null);
        $data['default_payload'] = $this->parseJsonField($data['default_payload'] ?? null, true);
        $data['is_active'] = $request->boolean('is_active', true);

        $template->update($data);
        return redirect()->route('admin.whatsapp.templates.index')
            ->with('success', 'Plantilla actualizada correctamente.');
    }

    public function destroy(WhatsappTemplate $template): RedirectResponse
    {
        $template->delete();
        return redirect()->route('admin.whatsapp.templates.index')
            ->with('success', 'Plantilla eliminada correctamente.');
    }

    private function parseJsonField(?string $value, bool $assoc = true): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $decoded = json_decode($value, $assoc);
        return is_array($decoded) ? $decoded : null;
    }
}
