<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500);
            $table->string('title', 200)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('device', 20)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->unsignedSmallInteger('duration')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('country');
            $table->index('device');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
