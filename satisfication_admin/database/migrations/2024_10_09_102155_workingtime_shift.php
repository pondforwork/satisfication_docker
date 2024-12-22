<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('workingtime_shift', function (Blueprint $table) {
            $table->id('workingtime_shift_id');
            $table->time('start_time')->nullable(false);
            $table->time('end_time')->nullable(false);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists( 'workingtime_shift'); 
    }
};
