<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameUnidadesproductivasIntervencionesTable extends Migration {
    public function up() {
        if (Schema::hasTable('unidadesproductivas_intervenciones') && !Schema::hasTable('intervenciones_unidadesproductivas')) {
            Schema::rename('unidadesproductivas_intervenciones', 'intervenciones_unidadesproductivas');
        }
    }

    public function down() {
        if (Schema::hasTable('intervenciones_unidadesproductivas') && !Schema::hasTable('unidadesproductivas_intervenciones')) {
            Schema::rename('intervenciones_unidadesproductivas', 'unidadesproductivas_intervenciones');
        }
    }
}
