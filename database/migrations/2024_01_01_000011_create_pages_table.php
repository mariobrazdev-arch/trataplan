<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_pt');
            $table->string('title_en');
            $table->longText('content_pt')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('meta_title_pt', 60)->nullable();
            $table->string('meta_title_en', 60)->nullable();
            $table->string('meta_description_pt', 160)->nullable();
            $table->string('meta_description_en', 160)->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
