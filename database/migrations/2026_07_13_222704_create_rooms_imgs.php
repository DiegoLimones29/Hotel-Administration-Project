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
        Schema::create('rooms_imgs', function (Blueprint $table) {
            $table->id();
            //foreign key del id del room type 
            $table->foreignId('room_type_id')->constrained('room_type')->onDelete('cascade');
            $table->string('img_url', 2048); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms_imgs');
    }
};
