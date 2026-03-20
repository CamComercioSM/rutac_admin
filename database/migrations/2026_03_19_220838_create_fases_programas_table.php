<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('fases_programas', function (Blueprint $table) {

            $table->bigIncrements('fase_id');

            $table->string('fase_nombre');
            $table->text('fase_descripcion')->nullable();

            $table->unsignedInteger('fase_orden')->nullable();

            $table->boolean('fase_activa')->default(true);

            // Auditoría
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
            $table->softDeletes('fecha_eliminacion');

            $table->integer('usuario_creo')->nullable();
            $table->integer('usuario_actualizo')->nullable();
            $table->integer('usuario_elimino')->nullable();

            $table->index(['fase_activa']);
            $table->index(['fase_orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('fases_programas');
    }
};
