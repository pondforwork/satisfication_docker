<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequence', function (Blueprint $table) {
            $table->unsignedBigInteger('sequence_id');
            $table->string('sequence_name',45)->nullable(false);
            $table->string('prefix', 45)->nullable(false);
            $table->integer('last_order')->nullable(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'sequence'); 
    }
};
