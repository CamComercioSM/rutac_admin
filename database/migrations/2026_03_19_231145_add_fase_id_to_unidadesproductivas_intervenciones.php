<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaseIdToUnidadesproductivasIntervenciones extends Migration {
    public function up() {
        Schema::table('unidadesproductivas_intervenciones', function (Blueprint $table) {
            // Agregar campo NULL (clave para no romper producción)
            $table->unsignedBigInteger('fase_id')->nullable()->after('convocatoria_id');

            // Índice (muy importante para consultas)
            $table->index('fase_id');

            // Llave foránea
            $table->foreign('fase_id')
                ->references('fase_id')
                ->on('fases_programas')
                ->onDelete('set null'); // importante: no romper históricos
        });
    }

    public function down() {
        Schema::table('unidadesproductivas_intervenciones', function (Blueprint $table) {
            $table->dropForeign(['fase_id']);
            $table->dropIndex(['fase_id']);
            $table->dropColumn('fase_id');
        });
    }
}
