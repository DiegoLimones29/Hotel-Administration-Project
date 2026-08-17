<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services');

            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 8, 2);
            $table->date('requested_date');

            $table->enum('status', ['solicitado', 'en_proceso', 'entregado'])
                  ->default('solicitado');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_services');
    }
};