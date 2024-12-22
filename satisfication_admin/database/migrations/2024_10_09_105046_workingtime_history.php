<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('workingtime_history', function (Blueprint $table) {
            $table->id('workingtime_history_id');
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('counter_id')->nullable(true);
            $table->unsignedBigInteger('workingtime_shift_id')->nullable(true);
            $table->tinyInteger('is_late')->nullable(false);
            $table->date('checkin_date')->nullable(false);
            $table->time('checkin_time')->nullable(false);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('counter_id')->references('counter_id')->on('counter')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('workingtime_shift_id')->references('workingtime_shift_id')->on('workingtime_shift')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists( 'workingtime_history'); 

    }
};
