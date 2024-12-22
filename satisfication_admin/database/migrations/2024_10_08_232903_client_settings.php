<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('client_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('client_settings_id');
            $table->mediumText('wallpaper_url')->nullable(false);
            $table->string('header_text', 45)->nullable(false);
            $table->integer('header_font_size')->nullable(true);
            $table->string('header_text_color', 45)->nullable(true);
            $table->string('footer_text', 45)->nullable(false);
            $table->integer('footer_font_size')->nullable(true);
            $table->string('footer_text_color',45)->nullable(true);
            $table->dateTime('last_updated')->nullable(false);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(table: 'client_settings'); 
    }
};
