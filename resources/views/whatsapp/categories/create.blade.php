@extends('layouts.admin')

@section('title', 'Nueva categoría WhatsApp')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark fw-bold">
                        <i class="ri-add-line text-primary me-2"></i>
                        Nueva categoría
                    </h2>
                    <p class="text-muted mb-0">Crear categoría de plantillas WhatsApp</p>
                </div>
                <a href="{{ route('admin.whatsapp.categories.index') }}" class="btn btn-outline-secondary"><i class="ri-arrow-left-line me-2"></i>Volver</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.whatsapp.categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold">Código (único) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required maxlength="100" placeholder="ej: notificaciones">
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" maxlength="500">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Activa</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="ri-save-line me-2"></i>Guardar</button>
                            <a href="{{ route('admin.whatsapp.categories.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
