<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('counter', function (Blueprint $table) {
            $table->id('counter_id');
            $table->unsignedBigInteger('location_id'); 
            $table->string('name', 50)->nullable(false);
            $table->tinyInteger('owned_by_client')->default(0)->nullable(false);

            $table->foreign('location_id')->references('location_id')->on('location')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists( 'counter'); 
    }
};
