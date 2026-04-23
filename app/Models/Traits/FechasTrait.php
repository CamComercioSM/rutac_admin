<?php

namespace App\Models\Traits;

trait FechasTrait {

    /**
     * Sobrescribir los nombres de las columnas de timestamps
     */
    public function getCreatedAtColumn() {
        return 'fecha_creacion';
    }

    public function getUpdatedAtColumn() {
        return 'fecha_actualizacion';
    }

    /**
     * Nota: Para SoftDeletes, Laravel busca la columna definida en el modelo.
     * Al incluir este método, ayudamos a centralizar la lógica.
     */
    public function getDeletedAtColumn() {
        return 'fecha_eliminacion';
    }
}
