# Gestión de Fases de Programas - Documentación

## Archivos Creados/Actualizados

### 1. Modelo: `FasePrograma.php`
**Ubicación:** `app/Models/Programas/FasePrograma.php`

- Extiende `RutaCModel` (que incluye SoftDeletes, UserTrait y FechasTrait)
- Primary Key: `fase_id` (BigInteger)
- Campos fillable: `nombre`, `descripcion`, `orden`, `activa`
- Scopes disponibles:
  - `->activas()` - Obtiene solo fases activas
  - `->ordenados()` - Ordena por el campo orden ascendente

### 2. Controlador: `FaseProgramaController.php`
**Ubicación:** `app/Http/Controllers/FaseProgramaController.php`

Métodos disponibles:
- `index(Request $request)` - Lista todas las fases con filtros y paginación
- `export(Request $request)` - Exporta a Excel
- `activas()` - Retorna solo fases activas
- `store(Request $request)` - Crea una nueva fase
- `show($id)` - Obtiene una fase específica
- `update(Request $request, $id)` - Actualiza una fase
- `destroy($id)` - Elimina lógicamente una fase (soft delete)
- `restore($id)` - Restaura una fase eliminada
- `forceDelete($id)` - Elimina permanentemente
- `toggleActivo($id)` - Cambia el estado activa/inactiva
- `reordenar(Request $request)` - Reordena fases
- `estadisticas()` - Obtiene estadísticas
- `proximoOrden()` - Obtiene el próximo número de orden

### 3. Servicio: `FaseProgramaService.php`
**Ubicación:** `app/Services/FaseProgramaService.php`

Métodos disponibles:
- `listar($filters, $perPage, $page)` - Lista con filtros y paginación
- `obtenerActivas()` - Obtiene fases activas
- `obtener($id)` - Obtiene una fase por ID
- `crear(array $datos)` - Crea nueva fase
- `actualizar($id, array $datos)` - Actualiza fase
- `eliminar($id)` - Elimina lógicamente
- `restaurar($id)` - Restaura fase eliminada
- `eliminarPermanentemente($id)` - Elimina permanentemente
- `obtenerProximoOrden()` - Obtiene próximo orden
- `reordenar(array $ordenes)` - Reordena fases
- `toggleActivo($id)` - Cambia estado
- `obtenerEstadisticas()` - Obtiene estadísticas

### 4. Export: `FaseProgramaExport.php`
**Ubicación:** `app/Exports/FaseProgramaExport.php`

- Exporta fases a formato Excel
- Incluye: ID, Nombre, Descripción, Orden, Activa, Fechas y usuarios

### 5. Migración: `2026_03_19_220838_create_fases_programas_table.php`
**Ubicación:** `database/migrations/2026_03_19_220838_create_fases_programas_table.php`

**Estructura de la tabla:**
```
- fase_id (BigInt, PK, Auto-increment)
- nombre (Varchar 255)
- descripcion (Text, nullable)
- orden (Unsigned Integer, nullable)
- activa (Boolean, default: 1)
- fecha_creacion (Timestamp, nullable)
- fecha_actualizacion (Timestamp, nullable)
- fecha_eliminacion (Timestamp, nullable) - Soft Delete
- usuario_creo (Integer, nullable)
- usuario_actualizo (Integer, nullable)
- usuario_elimino (Integer, nullable)

Índices:
- activa
- orden
```

## Rutas API a Agregar

Agregar las siguientes rutas en `routes/api.php` o `routes/web.php`:

```php
use App\Http\Controllers\FaseProgramaController;

// Rutas de Fases de Programas
Route::prefix('fases-programas')->middleware(['auth:sanctum'])->group(function () {
    // CRUD básico
    Route::get('/', [FaseProgramaController::class, 'index']);              // Listar con filtros
    Route::post('/', [FaseProgramaController::class, 'store']);             // Crear
    Route::get('/{id}', [FaseProgramaController::class, 'show']);           // Ver detalle
    Route::put('/{id}', [FaseProgramaController::class, 'update']);         // Actualizar
    Route::delete('/{id}', [FaseProgramaController::class, 'destroy']);     // Eliminar (soft)
    
    // Acciones adicionales
    Route::get('/activas', [FaseProgramaController::class, 'activas']);     // Solo activas
    Route::get('/export', [FaseProgramaController::class, 'export']);       // Exportar Excel
    Route::post('/{id}/restore', [FaseProgramaController::class, 'restore']); // Restaurar
    Route::delete('/{id}/force', [FaseProgramaController::class, 'forceDelete']); // Eliminar permanente
    Route::patch('/{id}/toggle', [FaseProgramaController::class, 'toggleActivo']); // Cambiar estado
    Route::post('/reordenar', [FaseProgramaController::class, 'reordenar']); // Reordenar
    Route::get('/stats/estadisticas', [FaseProgramaController::class, 'estadisticas']); // Estadísticas
    Route::get('/utils/proximo-orden', [FaseProgramaController::class, 'proximoOrden']); // Próximo orden
});
```

## Parámetros de Búsqueda y Filtros

### GET `/api/fases-programas`
**Query Parameters:**
- `nombre` (string) - Buscar por nombre (LIKE)
- `activa` (boolean) - Filtrar por estado activo/inactivo
- `sortBy` (string, default: 'orden') - Campo para ordenar
- `sortOrder` (string, default: 'asc') - Orden (asc/desc)
- `perPage` (integer, default: 15) - Registros por página
- `page` (integer, default: 1) - Página

**Respuesta:**
```json
{
  "data": [
    {
      "fase_id": 1,
      "nombre": "Fase 1",
      "descripcion": "Descripción...",
      "orden": 1,
      "activa": true,
      "fecha_creacion": "2026-04-23T10:00:00",
      "fecha_actualizacion": "2026-04-23T10:00:00",
      "usuario_creo": 1,
      "usuario_actualizo": null
    }
  ],
  "current_page": 1,
  "per_page": 15,
  "total": 5,
  "last_page": 1
}
```

## Validaciones

**Store/Update:**
```
- nombre: required|string|max:255
- descripcion: nullable|string
- orden: nullable|integer|min:1
- activa: boolean
```

## Ejemplo de Uso

### Crear una Fase
```bash
POST /api/fases-programas
Content-Type: application/json

{
  "nombre": "Preparación",
  "descripcion": "Fase de preparación del programa",
  "orden": 1,
  "activa": true
}
```

### Listar Fases Activas
```bash
GET /api/fases-programas/activas
```

### Actualizar Fase
```bash
PUT /api/fases-programas/1
Content-Type: application/json

{
  "nombre": "Preparación Actualizada",
  "orden": 2,
  "activa": true
}
```

### Cambiar Estado (Activa/Inactiva)
```bash
PATCH /api/fases-programas/1/toggle
```

### Exportar a Excel
```bash
GET /api/fases-programas/export?activa=1
```

### Reordenar Fases
```bash
POST /api/fases-programas/reordenar
Content-Type: application/json

{
  "ordenes": [3, 1, 2]
}
```

## Notas de Implementación

1. **Auditoría:** Los campos `usuario_creo`, `usuario_actualizo` y `usuario_elimino` se asignan automáticamente a través del trait `UserTrait`.

2. **Soft Delete:** Usa eliminación lógica. El campo `fecha_eliminacion` se establece automáticamente. Para queries normales, no se incluyen registros eliminados.

3. **Relaciones:** Si necesitas relacionar las fases con programas, agrega:
```php
// En el modelo FasePrograma
public function programas()
{
    return $this->hasMany(Programa::class, 'fase_id', 'fase_id');
}

// En el modelo Programa
public function fase()
{
    return $this->belongsTo(FasePrograma::class, 'fase_id', 'fase_id');
}
```

4. **Exportación:** La ruta de export genera un archivo Excel con todas las fases que coincidan con los filtros proporcionados.

5. **Estadísticas:** Retorna conteos de:
   - Total de fases
   - Fases activas
   - Fases inactivas
   - Fases eliminadas (soft delete)
