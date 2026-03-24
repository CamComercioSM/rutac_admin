<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('soporte_novedades', function (Blueprint $tabla) {
            $tabla->id('soporte_novedad_id');
            $tabla->string('titulo');
            $tabla->text('descripcion');
            $tabla->string('estilo_visual')->default('primary');
            $tabla->boolean('activo')->default(true);

            // Campos de Auditoría (Requeridos por UserTrait)
            $tabla->unsignedBigInteger('usuario_creo')->nullable();
            $tabla->unsignedBigInteger('usuario_actualizo')->nullable();
            $tabla->unsignedBigInteger('usuario_elimino')->nullable();

            // Timestamps en español
            $tabla->timestamp('fecha_creacion')->nullable();
            $tabla->timestamp('fecha_actualizacion')->nullable();
            $tabla->softDeletes('fecha_eliminacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('soporte_novedads');
    }
};
