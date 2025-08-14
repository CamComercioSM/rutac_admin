@extends('layouts.admin')

@section('content')

<div class="mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Menú</h4>
        <button class="btn btn-primary" onclick="openCreateMenu()">+ Crear Menú</button>
    </div>
    
    <div id="menuList"></div>
</div>

<!-- Modal para crear/editar -->
<div class="modal fade" id="menuModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="menuForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Crear Menú</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="id" id="menuId">

        <div class="mb-3">
          <label>Label</label>
          <input type="text" name="label" class="form-control" placeholder="Label" required>
        </div>

        <div class="mb-3">
            <label for="icon">Icono</label>
            <input type="text" class="form-control" name="icon" id="icon" placeholder="Icono" required>
        </div>

        <div class="mb-3">
          <label>URL</label>
          <input type="text" name="url" class="form-control" placeholder="URL">
        </div>

        <div class="mb-3">
          <label>Menú padre</label>
          <select name="parent_id" class="form-select" id="parentSelect">
            <option value="">-- Ninguno --</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Orden</label>
          <input type="number" name="order" class="form-control" value="0" placeholder="#">
        </div>

        <div class="mb-3">
            <label>Roles</label>
            <div id="roleCheckboxes">
                @foreach ($roles as $item)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $item->id }}" id="role_{{ $item->id }}">
                        <label class="form-check-label" for="role_{{ $item->id }}">
                            {{ $item->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

@endsection

@section('page-script')
    @vite([ 'resources/js/admin-menu.js' ])
@endsection