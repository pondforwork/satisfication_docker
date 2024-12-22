<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_answer', function (Blueprint $table) {
            $table->id('feedback_answer_id');
            $table->string('text',45)->nullable(false);
            $table->integer('order_no')->nullable(false);
            $table->tinyInteger('is_active')->default(1)->nullable(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_answer'); 
    }
};
