<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            
            // Fechas de planificación (App o Teléfono)
            $table->date('estimated_check_in');
            $table->date('estimated_check_out');
            
            // Fechas reales controladas por el recepcionista en Intranet
            $table->dateTime('actual_check_in')->nullable();
            $table->dateTime('actual_check_out')->nullable();
            
            // Flujo de la reserva
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->enum('origin', ['app', 'intranet'])->default('intranet');
            
            // Precio cobrado por noche (se respalda por si cambia el precio en room_type en el futuro)
            $table->decimal('price_per_night_charged', 8, 2);
            $table->decimal('total_room_amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
