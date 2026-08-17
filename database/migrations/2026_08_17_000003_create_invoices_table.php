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

            $table->foreignId('reservation_id')->unique()->constrained('reservations')->onDelete('cascade');

            $table->decimal('room_cost', 10, 2);
            $table->decimal('services_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2);

            $table->enum('payment_method', ['cash', 'card', 'transfer']);
            $table->timestamp('issued_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
