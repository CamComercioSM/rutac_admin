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
<script src="/libs/axios.min.js"></script>
<script src="/libs/jquery.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let allMenus = [];

        function loadMenus() {
            axios.get("/menu")
                .then(response => {
                    allMenus = response.data;
                    const tree = buildTree(allMenus);
                    document.getElementById("menuList").innerHTML = renderMenu(tree);
                })
                .catch(error => {
                    console.error("Error al cargar menús:", error);
                });
        }

        function buildTree(list, parentId = null) {
            return list
                .filter(item => item.parent_id === parentId)
                .map(item => ({
                    ...item,
                    children: buildTree(list, item.id)
                }));
        }

        function renderMenu(menus, level = 0) {
            let html = '<ul class="list-group">';
            menus.forEach(menu => {
                html += `
                    <li class="list-group-item">
                        <div style="margin-left:${level * 20}px">
                            <i class="${menu.icon} me-1"></i>
                            <strong>${menu.label}</strong>
                            <small class="text-muted">${menu.url || ''}</small>
                            <div class="float-end">
                                <button class="btn btn-sm btn-primary me-1" onclick="openEditMenu(${menu.id})">Editar</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMenu(${menu.id})">Eliminar</button>
                            </div>
                        </div>
                    </li>
                `;
                if (menu.children?.length > 0) {
                    html += renderMenu(menu.children, level + 1);
                }
            });
            html += '</ul>';
            return html;
        }

        window.openCreateMenu = function () {
            document.getElementById("menuForm").reset();
            document.getElementById("menuId").value = '';
            document.getElementById("modalTitle").textContent = "Crear Menú";

            document.querySelectorAll("#roleCheckboxes input[type='checkbox']").forEach(chk => chk.checked = false);

            fillParentSelect();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('menuModal')).show();
        };

        window.openEditMenu = function (id) {
            const menu = allMenus.find(m => m.id === id);
            if (!menu) return;

            document.getElementById("menuId").value = menu.id;
            document.querySelector("input[name='label']").value = menu.label;
            document.querySelector("input[name='url']").value = menu.url;
            document.querySelector("input[name='icon']").value = menu.icon;
            document.querySelector("input[name='order']").value = menu.order;

            fillParentSelect(menu.parent_id, menu.id);

            document.querySelectorAll("#roleCheckboxes input[type='checkbox']").forEach(chk => {
                chk.checked = menu.roles?.includes(parseInt(chk.value)) ?? false;
            });

            document.getElementById("modalTitle").textContent = "Editar Menú";
            bootstrap.Modal.getOrCreateInstance(document.getElementById('menuModal')).show();
        };

        window.deleteMenu = function (id) {
            if (!confirm("¿Eliminar este menú?")) return;

            axios.delete(`/admin/menu/${id}`)
                .then(() => loadMenus())
                .catch(err => {
                    console.error("Error al eliminar:", err);
                    alert("Error al eliminar");
                });
        };

        function fillParentSelect(selected = "", currentId = "") {
            const select = document.getElementById("parentSelect");
            select.innerHTML = '<option value="">-- Ninguno --</option>';

            allMenus.forEach(menu => {
                if (menu.id !== currentId) {
                    const option = document.createElement("option");
                    option.value = menu.id;
                    option.text = menu.label;
                    if (menu.id == selected) option.selected = true;
                    select.appendChild(option);
                }
            });
        }

        document.getElementById("menuForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Checkboxes (roles[])
            data.roles = Array.from(form.querySelectorAll("input[name='roles[]']:checked")).map(el => el.value);

            axios.post("/menu", data)
                .then(() => {
                    bootstrap.Modal.getInstance(document.getElementById('menuModal')).hide();
                    loadMenus();
                })
                .catch(err => {
                    console.error("Error al guardar:", err);
                    alert("Error al guardar");
                });
        });

        loadMenus();
    });
</script>

@endsection