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

            $table->string('nombre');
            $table->text('descripcion')->nullable();

            $table->unsignedInteger('orden')->nullable();

            $table->boolean('activa')->default(true);

            // Auditoría
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
            $table->timestamp('fecha_eliminacion')->nullable()->default(null);

            $table->integer('usuario_creo')->nullable();
            $table->integer('usuario_actualizo')->nullable();
            $table->integer('usuario_elimino')->nullable();

            $table->index(['activa']);
            $table->index(['orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('fases_programas');
    }
};
