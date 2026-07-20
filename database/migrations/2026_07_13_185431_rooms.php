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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            //foreign key de el roomtype id 
            $table->foreignId('room_type_id')->constrained('room_type')->onDelete('cascade'); 

            $table->integer('room_number'); 
            $table->integer('room_floor'); 
            $table->enum('state', ['occupied','on maintenance', 'out of service', 'available'])->default('available'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
