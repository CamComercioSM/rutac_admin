<!-- Bottom Offcanvas -->
<!-- Botón que abre una nueva página -->
<a href="https://chatgpt.com/g/g-683a175ce0008191a30e84e26385f8cd-asesor-ruta-c"
   target="_blank"
   rel="noopener noreferrer"
   class="btn btn-primary toggle-btn" data-bs-toggle="offcanvas" >
    <i class="icon-base ri ri-user-search-fill"></i>
    <span class="btn-text">marIA C</span>
</a>


<button class="btn btn-primary toggle-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">
    <i class="icon-base ri ri-user-search-fill"></i>
    <span class="btn-text">Expedientes Mercantiles</span>
</button>


<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel" style="min-height: 60%;">
  <div class="offcanvas-header">
    <h5 id="offcanvasBottomLabel" class="offcanvas-title">Consulta de Expedientes Mercantiles</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">   

    <!-- Encabezado -->
    <div class="text-center mb-4">
      <p class="text-muted">Ingrese los datos de búsqueda para consultar expedientes mercantiles.</p>
    </div>

    <!-- Formulario de búsqueda -->
    <div class="card shadow-sm p-4 mb-4">
      <form id="searchForm" class="row g-3">
        <div class="col-md-2">
          <input type="text" id="nit" class="form-control" placeholder="NIT">
        </div>
        <div class="col-md-4">
          <input type="text" id="razon" class="form-control" placeholder="Razón Social">
        </div>
        <div class="col-md-2">
          <input type="text" id="matricula" class="form-control" placeholder="Matrícula">
        </div>
        <div class="col-md-2">
          <input type="text" id="proponente" class="form-control" placeholder="Proponente">
        </div>
        <div class="col-md-2 d-grid">
          <button type="submit" class="btn btn-primary">
            <span id="spinner" class="spinner-border spinner-border-sm me-2 d-none"></span>
            Buscar
          </button>
        </div>
      </form>
    </div>

    <!-- Resultados -->
    <div id="resultados" class="mt-4"></div>
  
  </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="detalleModal" aria-labelledby="detalleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="detalleModalLabel">Detalle del Expediente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="detalleContenido"></div>
    </div>
  </div>
</div>

<script>
  const etiquetasHumanas = {
          nombre: "Nombre completo",
          nombre1: "Primer nombre",
          nombre2: "Segundo nombre",
          apellido1: "Primer apellido",
          apellido2: "Segundo apellido",
          sigla: "Sigla",
          identificacion: "Número de identificación",
          nit: "NIT",
          genero: "Género",
          idclase: "Clase de persona",
          organizacion: "Código de organización",
          organizaciontextual: "Tipo de organización",
          categoria: "Categoría",
          estado: "Estado de matrícula",
          afiliado: "Afiliado",
          afiliadotextual: "Estado de afiliación",
          dircom: "Dirección comercial",
          barriocom: "Barrio comercial",
          muncom: "Municipio comercial",
          telcom1: "Teléfono comercial 1",
          telcom2: "Teléfono comercial 2",
          telcom3: "Teléfono comercial 3",
          emailcom: "Email comercial",
          urlcom: "Página web comercial",
          dirnot: "Dirección de notificación",
          barrionot: "Barrio de notificación",
          munnot: "Municipio de notificación",
          telnot1: "Teléfono notificación 1",
          telnot2: "Teléfono notificación 2",
          telnot3: "Teléfono notificación 3",
          emailnot: "Email de notificación",
          fechamatricula: "Fecha de matrícula",
          fecharenovacion: "Fecha de última renovación",
          ultanorenovado: "Último año renovado",
          fechacancelacion: "Fecha de cancelación",
          fechadatos: "Fecha de datos financieros",
          anodatostamanoempresarial: "Año datos tamaño empresarial",
          fechadatostamanoempresarial: "Fecha datos tamaño empresarial",
          fechainicioactividades: "Fecha inicio de actividades",
          activos: "Activos",
          actcte: "Activos corrientes",
          actnocte: "Activos no corrientes",
          acttot: "Activos totales",
          pasivos: "Pasivos",
          pascte: "Pasivos corrientes",
          paslar: "Pasivos a largo plazo",
          pastot: "Pasivos totales",
          pattot: "Patrimonio total",
          paspat: "Pasivo + Patrimonio",
          patrimonio: "Patrimonio",
          ingresos: "Ingresos",
          ingresostamanoempresarial: "Ingresos tamaño empresarial",
          ingope: "Ingresos operacionales",
          ingnoope: "Ingresos no operacionales",
          utilidad: "Utilidad neta",
          utiope: "Utilidad operacional",
          utinet: "Utilidad neta",
          gastos: "Gastos",
          gtoadm: "Gastos administrativos",
          gtoven: "Gastos de ventas",
          cosven: "Costo de ventas",
          gasint: "Gastos intereses",
          gasimp: "Gastos impuestos",
          balsoc: "Balance social"
      };

  const grupos = {
        'Identificación y Representación': ["nombre", "nombre1", "nombre2", "apellido1", "apellido2", "sigla", "identificacion", "nit", "genero", "idclase", "organizacion", "organizaciontextual", "categoria", "estado", "afiliado", "afiliadotextual"],
        'Contacto y Ubicación': ["dircom", "barriocom", "muncom", "telcom1", "telcom2", "telcom3", "emailcom", "urlcom", "dirnot", "barrionot", "munnot", "telnot1", "telnot2", "telnot3", "emailnot"],
        'Fechas y Renovaciones': ["fechamatricula", "fecharenovacion", "ultanorenovado", "fechacancelacion", "fechadatos", "anodatostamanoempresarial", "fechadatostamanoempresarial", "fechainicioactividades"],
        'Información Económica': ["activos", "actcte", "actnocte", "acttot", "pasivos", "pascte", "paslar", "pastot", "pattot", "paspat", "patrimonio", "ingresos", "ingresostamanoempresarial", "ingope", "ingnoope", "utilidad", "utiope", "utinet", "gastos", "gtoadm", "gtoven", "cosven", "gasint", "gasimp", "balsoc"]
    };

  function camelCaseToLabel(text) {
    return etiquetasHumanas[text] ||
      text.replace(/([A-Z])/g, ' $1')
        .replace(/^./, str => str.toUpperCase())
        .replace(/_/g, ' ');
  }

  const loading = document.querySelectorAll('.cargando')[0];

  // Manejo del submit
  document.getElementById('searchForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    loading.classList.remove('d-none');

    const campos = ['nit', 'razon', 'matricula', 'proponente'];
    let criterio = '';
    let palabras_claves = '';

    for (let campo of campos) {
      const valor = document.getElementById(campo).value.trim();
      if (valor !== '') {
        criterio = campo.toUpperCase();
        palabras_claves = valor;
        break;
      }
    }

    if (!criterio) {
      alert('Por favor ingrese al menos un criterio de búsqueda.');
      loading.classList.add('d-none');
      return;
    }

    try {
      const res = await fetch('https://sii.apisicam.net/consultarDatos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ criterio, palabras_claves })
      });
      const data = await res.json();
      mostrarResultados(data.expedientes || []);
    } catch (err) {
      console.error(err);
      alert("Error consultando el servicio.");
    } finally {
      loading.classList.add('d-none');
    }
  });

  function mostrarResultados(expedientes) {
    
    const contenedor = document.getElementById('resultados');
    contenedor.innerHTML = '';

    if (!expedientes.length) {
      contenedor.innerHTML = '<div class="alert alert-warning">No se encontraron resultados.</div>';
      return;
    }

    // Tabla para desktop
    const wrapper = document.createElement('div');
    wrapper.className = 'table-responsive d-none d-md-block';

    const tabla = document.createElement('table');
    tabla.className = 'table table-bordered align-middle';
    tabla.innerHTML = `
      <thead>
        <tr>
          <th>Acción</th><th>Tipo</th><th>NIT</th><th>Nombre</th><th>Matrícula</th>
          <th>Estado</th><th>Última Renovación</th><th>Municipio/Dirección</th><th>Correo</th><th>Celular</th>
        </tr>
      </thead>
      <tbody>
        ${expedientes.map(e => `
          <tr>
            <td>
              <button class="btn btn-outline-info btn-sm" onclick="verDetalle('${e.matricula}')">Ver</button>
            </td>
            <td>${e.organizaciontextual || ''}</td>
            <td>${e.nit || ''}</td>
            <td>${e.nombre || ''}</td>
            <td>${e.matricula || ''}</td>
            <td>${e.estadomatricula || e.estadoMatriculaTitulo || ''}</td>
            <td>${e.ultanorenovado || ''}</td>
            <td>${e.municipiotextual || ''} / ${e.direccion || ''}</td>
            <td>${e.emailcom || ''}</td>
            <td>${e.telcom1 || ''}</td>
          </tr>`).join('')}
      </tbody>
    `;
    wrapper.appendChild(tabla);
    contenedor.appendChild(wrapper);

    // Tarjetas para móviles
    const cards = expedientes.map(e => `
      <div class="card mb-3 d-md-none">
        <div class="card-body">
          <h5 class="card-title">${e.nombre || ''}</h5>
          <p class="card-text">
            <strong>Tipo:</strong> ${e.organizaciontextual || ''}<br>
            <strong>NIT:</strong> ${e.nit || ''}<br>
            <strong>Matrícula:</strong> ${e.matricula || ''}<br>
            <strong>Estado:</strong> ${e.estadomatricula || e.estadoMatriculaTitulo || ''}<br>
            <strong>Última Renovación:</strong> ${e.ultanorenovado || ''}<br>
            <strong>Municipio:</strong> ${e.municipiotextual || ''}<br>
            <strong>Dirección:</strong> ${e.direccion || ''}<br>
            <strong>Correo:</strong> ${e.emailcom || ''}<br>
            <strong>Celular:</strong> ${e.telcom1 || ''}<br>
          </p>
          <button class="btn btn-outline-info btn-sm" onclick="verDetalle('${e.matricula}')">Ver</button>
        </div>
      </div>`).join('');
    contenedor.innerHTML += cards;
  }

  async function verDetalle(matricula) {
    try {

      loading.classList.remove('d-none');

      const res = await fetch('https://sii.apisicam.net/consultarExpediente', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ matricula })
      });
      const data = await res.json();
      const detalle = data.resultado;

      let html = '<div class="accordion" id="accordionDetalle">';
      let index = 0;

      for (const grupo in grupos) {
        const campos = grupos[grupo];
        const contenido = campos
          .filter(campo => detalle[campo])
          .map(campo => `<li class="list-group-item"><strong>${camelCaseToLabel(campo)}:</strong> ${detalle[campo]}</li>`)
          .join('');
        if (contenido) {
          html += `
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading${index}">
                <button class="accordion-button ${index !== 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="${index === 0}" aria-controls="collapse${index}">
                  ${grupo}
                </button>
              </h2>
              <div id="collapse${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" data-bs-parent="#accordionDetalle">
                <div class="accordion-body">
                  <ul class="list-group">${contenido}</ul>
                </div>
              </div>
            </div>`;
          index++;
        }
      }

      html += '</div>';
      document.getElementById('detalleContenido').innerHTML = html;
      new bootstrap.Modal(document.getElementById('detalleModal')).show();

    } catch (err) {
      console.error(err);
      alert("Error obteniendo detalle.");
    }
    finally {
      loading.classList.add('d-none');
    }
  }
</script>

<style>
.toggle-btn {
  position: fixed;
  top: 35%;
  right: 0;
  transform: translateY(-50%);
  border-radius: 25px 0 0 25px;
  padding: 8px;
  transition: all 0.3s ease;
  z-index: 1050;
  white-space: nowrap;
}

.toggle-btn .btn-text {
  max-width: 0;
  overflow: hidden;
  opacity: 0;
  transition: all 0.3s ease;
}

.toggle-btn:hover .btn-text {
  max-width: 220px;
  opacity: 1;
  padding-left:5px;
}
</style>
