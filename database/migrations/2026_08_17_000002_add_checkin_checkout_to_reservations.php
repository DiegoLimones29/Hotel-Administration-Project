<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('actual_check_in_at')->nullable()->after('status');
            $table->timestamp('actual_check_out_at')->nullable()->after('actual_check_in_at');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['actual_check_in_at', 'actual_check_out_at']);
        });
    }
};
