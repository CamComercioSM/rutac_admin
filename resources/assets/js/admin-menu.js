$(document).ready(function () {

    let allMenus = [];

    function loadMenus() {
        axios.get("/menu")
            .then(response => {
                allMenus = response.data;
                const tree = buildTree(allMenus);
                document.getElementById("menuList").innerHTML = renderMenu(tree);
                $('.cargando').addClass('d-none');
            })
            .catch(error => {
                console.error("Error al cargar menús:", error);
                $('.cargando').addClass('d-none');
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

            let icon = menu.icon ? `<i class="icon-base ri ${menu.icon} me-1"></i>` : '';

            html += `
                <li class="list-group-item">
                    <div style="margin-left:${level * 20}px">
                        ${icon}
                        <strong>${menu.label}</strong>
                        <small class="text-muted">${menu.url || ''}</small>
                        <div class="float-end">
                            <button class="btn btn-sm py-1 btn-primary me-1" onclick="openEditMenu(${menu.id})">Editar</button>
                            <button class="btn btn-sm py-1 btn-danger" onclick="deleteMenu(${menu.id})">Eliminar</button>
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

        axios.delete(`/menu/${id}`)
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
            if (menu.id !== currentId && menu.parent_id == null) {
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
        
        $('.cargando').removeClass('d-none');
        
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
