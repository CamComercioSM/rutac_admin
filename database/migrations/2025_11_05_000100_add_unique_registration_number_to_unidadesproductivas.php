<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidadesproductivas', function (Blueprint $table) {
            // Índice único para evitar duplicidad de matrícula
            $table->unique('registration_number', 'unidadesproductivas_registration_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('unidadesproductivas', function (Blueprint $table) {
            $table->dropUnique('unidadesproductivas_registration_number_unique');
        });
    }
};


