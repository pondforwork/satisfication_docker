<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('role',45)->nullable(false);
        });
    }

  
    public function down(): void
    {
        Schema::dropIfExists(table: 'role'); 
    }
};
