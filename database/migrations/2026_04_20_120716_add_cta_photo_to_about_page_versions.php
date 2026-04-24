<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('about_page_versions', function (Blueprint $table) {
            $table->string('cta_photo')->nullable()->after('cta_subtitle_en');
        });
    }

    public function down(): void
    {
        Schema::table('about_page_versions', function (Blueprint $table) {
            $table->dropColumn('cta_photo');
        });
    }
};
