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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('num_guests')->default(1);
            $table->integer('num_nights');
            $table->decimal('total_cost', 10, 2);

            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])
                ->default('pending');

            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
