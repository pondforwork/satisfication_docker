<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location', function (Blueprint $table) {
            $table->id('location_id');
            $table->string('name',50)->nullable(false);
            $table->string('code')->nullable(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists( 'location'); 
    }
};
