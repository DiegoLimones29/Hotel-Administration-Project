<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_details', function (Blueprint $table) {
            $table->id();
            // Relación con el usuario (huésped)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('document_id'); // INE, Pasaporte, Cédula
            $table->string('country_of_origin')->default('Mexico');
            $table->string('phone_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_details');
    }
};
