<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('checkin_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('checkin_settings_id'); 
            $table->integer('advance_late_duration')->nullable(false); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'checkin_settings'); 
    }
};
