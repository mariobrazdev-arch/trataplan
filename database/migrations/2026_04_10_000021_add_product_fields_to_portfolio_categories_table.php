<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_categories', function (Blueprint $table) {
            $table->text('description_pt')->nullable()->after('name_en');
            $table->text('description_en')->nullable()->after('description_pt');
            $table->string('cover_photo')->nullable()->after('description_en');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_categories', function (Blueprint $table) {
            $table->dropColumn(['description_pt', 'description_en', 'cover_photo']);
        });
    }
};
