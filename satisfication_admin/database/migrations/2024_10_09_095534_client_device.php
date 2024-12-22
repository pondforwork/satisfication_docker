<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_device', function (Blueprint $table) {
            $table->id('client_device_id');
            $table->string('name', 50)->nullable(false);
            $table->unsignedBigInteger('counter_id')->nullable(false); 
            $table->tinyInteger('is_using')->default(1)->nullable(false);

            $table->foreign('counter_id')->references('counter_id')->on('counter')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists( 'client_device'); 
    }
};
