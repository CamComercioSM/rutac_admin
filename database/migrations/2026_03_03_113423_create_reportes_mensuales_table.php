<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reportes_mensuales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asesor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->year('anio')->nullable();
            $table->unsignedTinyInteger('mes')->nullable();

            $table->unsignedInteger('total_intervenciones')->default(0);
            $table->unsignedInteger('total_unidades')->default(0);

            $table->enum('estado', [
                'BORRADOR',
                'PENDIENTE_REVISION',
                'APROBADO',
                'RECHAZADO'
            ])->default('BORRADOR');

            $table->text('observaciones_supervisor')->nullable();

            $table->foreignId('supervisor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('fecha_generacion')->nullable();
            $table->timestamp('fecha_revision')->nullable();

            $table->string('informe_url')->nullable();
            $table->string('hash_consolidado', 64)->nullable();

            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
            $table->timestamp('fecha_eliminacion')->nullable();

            $table->integer('usuario_creo')->nullable();
            $table->integer('usuario_actualizo')->nullable();
            $table->integer('usuario_elimino')->nullable();

            $table->unique(['asesor_id', 'anio', 'mes']);
            $table->index(['estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_mensuales');
    }
};
