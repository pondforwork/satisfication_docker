<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id('feedback_id');
            $table->integer('score')->nullable(false);
            $table->integer('counter_id')->nullable(true);
            $table->string('feedback_text',45)->nullable(true);
            $table->date('date_added')->nullable(false);
            $table->time('time_added')->nullable(false);
            $table->unsignedBigInteger('user_id')->nullable(true);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists( 'feedback'); 
    }
};
