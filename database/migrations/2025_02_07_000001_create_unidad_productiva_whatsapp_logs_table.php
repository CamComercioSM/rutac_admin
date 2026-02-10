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
        Schema::create('unidad_productiva_whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('unidadproductiva_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('phone', 20);
            $table->string('phone_type', 30)->nullable()->comment('mobile, telephone, contact_phone');
            $table->text('message');
            $table->string('status', 20)->default('pending')->comment('pending, sent, failed');
            $table->json('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('unidadproductiva_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_productiva_whatsapp_logs');
    }
};
