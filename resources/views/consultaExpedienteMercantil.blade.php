@extends('layouts.admin', [ 'titulo'=> 'Consulta de Expedientes Mercantiles' ])

@section('content')

    <!-- Encabezado -->
    <div class="text-center mb-4">
      <h2 class="fw-bold mb-0">Consulta de Expedientes Mercantiles</h2>
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

  <!-- Modal Detalle -->
  <div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
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

@endsection

@section('page-script')
<script>

    document.getElementById('searchForm').addEventListener('submit', function (e) {
      e.preventDefault();
      
      document.getElementById('spinner').classList.remove('d-none');

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
        document.getElementById('spinner').classList.add('d-none');
        return;
      }

      fetch('https://sii.apisicam.net/consultarDatos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ criterio, palabras_claves })
      })
      .then(res => res.json())
      .then(data => {
        document.getElementById('spinner').classList.add('d-none');
        mostrarResultados(data.expedientes);
      });
    });

    function mostrarResultados(expedientes) {
      const contenedor = document.getElementById('resultados');
      contenedor.innerHTML = '';

      if (!expedientes.length) {
        contenedor.innerHTML = '<div class="alert alert-warning">No se encontraron resultados.</div>';
        return;
      }

      // Tabla en pantallas medianas en adelante
      const wrapper = document.createElement('div');
      wrapper.className = 'table-responsive d-none d-md-block';

      const tabla = document.createElement('table');
      tabla.className = 'table table-hover table-bordered align-middle';
      tabla.innerHTML = `
        <thead class="table-light">
          <tr>
            <th>Acción</th><th>Tipo</th><th>NIT</th><th>Nombre</th><th>Matrícula</th>
            <th>Estado</th><th>Última Renovación</th><th>Municipio/Dirección</th><th>Correo</th><th>Celular</th>
          </tr>
        </thead>
        <tbody>
          ${expedientes.map(e => `
            <tr>
              <td><button class="btn btn-outline-info btn-sm" onclick="verDetalle('${e.matricula}')">Ver</button></td>
              <td>${e.organizaciontextual}</td>
              <td>${e.nit}</td>
              <td>${e.nombre}</td>
              <td>${e.matricula}</td>
              <td>${e.estadoMatriculaTitulo}</td>
              <td>${e.ultanorenovado}</td>
              <td>${e.municipiotextual} / ${e.direccion}</td>
              <td>${e.emailcom}</td>
              <td>${e.telcom1}</td>
            </tr>`).join('')}
        </tbody>
      `;
      wrapper.appendChild(tabla);
      contenedor.appendChild(wrapper);

      // Tarjetas para pantallas pequeñas
      const cards = expedientes.map(e => `
        <div class="card shadow-sm mb-3 d-md-none">
          <div class="card-body">
            <h5 class="card-title fw-bold">${e.nombre}</h5>
            <p class="card-text small">
              <strong>Tipo:</strong> ${e.organizaciontextual}<br>
              <strong>NIT:</strong> ${e.nit}<br>
              <strong>Matrícula:</strong> ${e.matricula}<br>
              <strong>Estado:</strong> ${e.estadomatricula}<br>
              <strong>Última Renovación:</strong> ${e.ultanorenovado}<br>
              <strong>Municipio:</strong> ${e.municipiotextual}<br>
              <strong>Dirección:</strong> ${e.direccion}<br>
              <strong>Correo:</strong> ${e.emailcom}<br>
              <strong>Celular:</strong> ${e.telcom1}<br>
            </p>
            <button class="btn btn-outline-info btn-sm" onclick="verDetalle('${e.matricula}')">Ver</button>
          </div>
        </div>`).join('');
      contenedor.innerHTML += cards;
    }
</script>
<script>
    document.querySelectorAll('.cargando').forEach(function(element) {
        element.classList.add('d-none');
    });
</script>
@endsection