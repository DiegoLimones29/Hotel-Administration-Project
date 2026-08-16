<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->decimal('room_subtotal', 10, 2);   // Suma total de solo hospedaje
            $table->decimal('services_subtotal', 10, 2); // Suma de todos los extras en reservation_service
            $table->decimal('total_amount', 10, 2);      // Suma total de ambos
            $table->enum('payment_method', ['cash', 'card', 'transfer']);
            $table->boolean('is_facturated')->default(false); // Si solicitó factura fiscal o solo nota
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
