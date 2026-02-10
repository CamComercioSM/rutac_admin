<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\StoreCategoryRequest;
use App\Http\Requests\Whatsapp\UpdateCategoryRequest;
use App\Models\WhatsappTemplateCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WhatsappCategoryController extends Controller
{
    public function index(): View
    {
        $categories = WhatsappTemplateCategory::orderBy('name')->get();
        return view('whatsapp.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('whatsapp.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        WhatsappTemplateCategory::create($request->validated());
        return redirect()->route('admin.whatsapp.categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(WhatsappTemplateCategory $category): View
    {
        $category->load('templates');
        return view('whatsapp.categories.show', compact('category'));
    }

    public function edit(WhatsappTemplateCategory $category): View
    {
        return view('whatsapp.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, WhatsappTemplateCategory $category): RedirectResponse
    {
        $category->update($request->validated());
        return redirect()->route('admin.whatsapp.categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(WhatsappTemplateCategory $category): RedirectResponse
    {
        if ($category->templates()->exists()) {
            return redirect()->route('admin.whatsapp.categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene plantillas asociadas.');
        }
        $category->delete();
        return redirect()->route('admin.whatsapp.categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }

    public function toggleStatus(WhatsappTemplateCategory $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);
        return redirect()->route('admin.whatsapp.categories.index')
            ->with('success', 'Estado de la categoría actualizado.');
    }
}
