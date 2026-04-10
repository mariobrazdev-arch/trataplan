<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_pt');
            $table->string('title_en');
            $table->longText('content_pt')->nullable();
            $table->longText('content_en')->nullable();
            $table->text('excerpt_pt')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('meta_title_pt', 60)->nullable();
            $table->string('meta_title_en', 60)->nullable();
            $table->string('meta_description_pt', 160)->nullable();
            $table->string('meta_description_en', 160)->nullable();
            $table->string('og_image')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
